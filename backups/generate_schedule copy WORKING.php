<?php

session_start();

require __DIR__ . '/../../vendor/autoload.php';
include __DIR__ . '/../connection/connection.php';
include __DIR__ . '/../../config_openai.php';

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Psr18Client;

/* ============================
   FETCH & NORMALIZE DATA
============================ */

$subjects = $conn->query("
    SELECT 
        a.assign_subject AS subject,
        s.subject_name,
        s.units,
        a.section_course,
        a.section_year
    FROM assign_section_subjects a
    JOIN manage_subjects s 
        ON s.subject = a.assign_subject
")->fetch_all(MYSQLI_ASSOC);

if (empty($subjects)) {
    die("❌ No assigned subjects found.");
}

$sections = $conn->query("
    SELECT section_name, section_course, section_year, total_students
    FROM manage_sections
")->fetch_all(MYSQLI_ASSOC);

$faculty = $conn->query("
    SELECT faculty_name, employment_type, current_status, total_hours_per_week
    FROM manage_faculty
")->fetch_all(MYSQLI_ASSOC);

$rooms = $conn->query("
    SELECT room_name, room_capacity
    FROM manage_rooms
")->fetch_all(MYSQLI_ASSOC);

$faculty_days = [];
$res = $conn->query("SELECT faculty_name, day FROM manage_faculty_days");
while ($r = $res->fetch_assoc()) {
    $faculty_days[$r['faculty_name']][] = $r['day'];
}

$faculty_subjects = [];
$res = $conn->query("SELECT faculty_name, subject FROM manage_faculty_subject");
while ($r = $res->fetch_assoc()) {
    $faculty_subjects[$r['faculty_name']][] = $r['subject'];
}

/* ============================
   OPENAI CLIENT (PSR-18)
============================ */

$symfonyClient = HttpClient::create([
    'timeout' => 180,
    'max_duration' => 300
]);

$psr18Client = new Psr18Client($symfonyClient);

$client = OpenAI::factory()
    ->withApiKey($OPENAI_API_KEY)
    ->withHttpClient($psr18Client)
    ->make();

/* ============================
   IMPROVED PROMPT
============================ */

$prompt = <<<'PROMPT'
You are a deterministic university scheduling engine.

CRITICAL: Return ONLY valid JSON. No markdown, no explanations, no ```json``` blocks.

OUTPUT FORMAT:
{
  "schedules": [
    {
      "section": "string",
      "subject": "string",
      "subject_name": "string",
      "faculty": "string",
      "room": "string",
      "day": "string",
      "time_start": "HH:mm",
      "time_end": "HH:mm"
    }
  ]
}

TIME RULES:
- School hours: 07:30 to 17:30
- NO classes during lunch: 12:00 to 13:00
- Use 24-hour format (e.g., "08:00", "14:30")
- time_start < time_end
- Classes cannot start before 07:30 or end after 17:30
- Classes cannot overlap 12:00-13:00 period

SCHEDULING RULES:
1. Schedule ALL subjects for the given section
2. Use faculty who are QUALIFIED (check faculty_subjects) and AVAILABLE (check faculty_days)
3. Assign rooms where room_capacity >= section total_students
4. Check EXISTING_SCHEDULES to avoid conflicts
5. If a class duration causes lunch overlap, split it:
   - Example: 3hr class at 11:00 → 1hr (11:00-12:00) + 2hr (13:00-15:00) same day
   - OR move entirely to another day

CONFLICT CHECKS (validate against EXISTING_SCHEDULES):
- NO duplicate: (section + day + time)
- NO duplicate: (faculty + day + time)
- NO duplicate: (room + day + time)

Time overlap formula:
Entry A overlaps B if same day AND (A.start < B.end AND A.end > B.start)

If conflict detected, try:
1. Different time on same day
2. Different day
3. Different room (if only room conflict)

MANDATORY: Every subject in assigned_subjects MUST be scheduled. If you cannot schedule a subject, log it in a "failed" array.
PROMPT;

/* ============================
   GENERATE SCHEDULE PER SECTION WITH CONTEXT
============================ */

$allSchedules = [];
$skippedSections = [];
$failedSubjects = [];

foreach ($sections as $section) {
    $sectionName = $section['section_name'];
    
    // Get subjects for this specific section
    $sectionSubjects = array_values(array_filter(
        $subjects,
        fn($s) =>
            $s['section_course'] === $section['section_course'] &&
            $s['section_year'] === $section['section_year']
    ));

    if (empty($sectionSubjects)) {
        error_log("No subjects assigned to section: $sectionName");
        continue;
    }

    // Build context-aware input
    $sectionInputData = [
        'current_section' => $section,
        'assigned_subjects' => $sectionSubjects,
        'faculty' => $faculty,
        'faculty_days' => $faculty_days,
        'faculty_subjects' => $faculty_subjects,
        'rooms' => $rooms,
        'existing_schedules' => $allSchedules, // ⬅ KEY FIX: Pass existing schedules
        'constraints' => [
            'days' => ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],
            'start_time' => '07:30',
            'end_time' => '17:30'
        ]
    ];

    $response = null;
    $maxRetries = 3;
    $baseDelay = 3;

    for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
        try {
            $response = $client->chat()->create([
                'model' => 'gpt-4o-mini', // Use correct model name
                'max_tokens' => 2000,
                'temperature' => 0.3, // Lower temperature for consistency
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a university scheduling system. Return only valid JSON.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt . "\n\nDATA:\n" . json_encode($sectionInputData, JSON_PRETTY_PRINT)
                    ]
                ],
                'response_format' => ['type' => 'json_object']
            ]);
            break; // Success, exit retry loop
            
        } catch (\OpenAI\Exceptions\ErrorException $e) {
            error_log("OpenAI API Error for $sectionName (attempt $attempt): " . $e->getMessage());
            
            if ($attempt === $maxRetries) {
                $skippedSections[] = $sectionName;
                continue 2; // Skip to next section
            }
            
            sleep($baseDelay * $attempt);
            
        } catch (\Exception $e) {
            error_log("Unexpected error for $sectionName: " . $e->getMessage());
            $skippedSections[] = $sectionName;
            continue 2;
        }
    }

    // Validate response
    if (!$response || !isset($response['choices'][0]['message']['content'])) {
        error_log("Invalid response for section: $sectionName");
        $skippedSections[] = $sectionName;
        continue;
    }

    $jsonText = $response['choices'][0]['message']['content'];
    
    // Clean potential markdown wrapping
    $jsonText = preg_replace('/^```json\s*|\s*```$/m', '', $jsonText);
    
    $data = json_decode($jsonText, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON decode error for $sectionName: " . json_last_error_msg());
        error_log("Raw response: " . substr($jsonText, 0, 500));
        $skippedSections[] = $sectionName;
        continue;
    }

    if (!is_array($data) || !isset($data['schedules']) || !is_array($data['schedules'])) {
        error_log("Invalid data structure for section: $sectionName");
        $skippedSections[] = $sectionName;
        continue;
    }

    // Validate each schedule entry
    $validEntries = 0;
    foreach ($data['schedules'] as $row) {
        // Check required fields
        $required = ['section', 'subject', 'subject_name', 'faculty', 'room', 'day', 'time_start', 'time_end'];
        $hasAllFields = true;
        
        foreach ($required as $field) {
            if (!isset($row[$field]) || empty($row[$field])) {
                $hasAllFields = false;
                break;
            }
        }
        
        if (!$hasAllFields) {
            error_log("Incomplete schedule entry for $sectionName: " . json_encode($row));
            continue;
        }
        
        // Validate time format and logic
        if ($row['time_start'] >= $row['time_end']) {
            error_log("Invalid time range for $sectionName: {$row['time_start']} to {$row['time_end']}");
            continue;
        }
        
        $allSchedules[] = $row;
        $validEntries++;
    }

    if ($validEntries === 0) {
        error_log("No valid entries generated for section: $sectionName");
        $skippedSections[] = $sectionName;
    } else {
        error_log("✓ Generated $validEntries schedule entries for $sectionName");
    }

    // Rate limiting: Increase delay between requests
    sleep(4);
}

/* ============================
   SAVE RESULTS
============================ */

// Save main schedule
file_put_contents(
    __DIR__ . '/output.json',
    json_encode(['schedules' => $allSchedules], JSON_PRETTY_PRINT)
);

// Save skipped sections for review
if (!empty($skippedSections)) {
    file_put_contents(
        __DIR__ . '/skipped_sections.json',
        json_encode([
            'timestamp' => date('Y-m-d H:i:s'),
            'count' => count($skippedSections),
            'sections' => $skippedSections
        ], JSON_PRETTY_PRINT)
    );
    
    error_log("⚠️ WARNING: " . count($skippedSections) . " sections were skipped. Check skipped_sections.json");
}

// Generate summary
$summary = [
    'total_sections' => count($sections),
    'successfully_scheduled' => count($sections) - count($skippedSections),
    'skipped' => count($skippedSections),
    'total_schedule_entries' => count($allSchedules),
    'timestamp' => date('Y-m-d H:i:s')
];

file_put_contents(
    __DIR__ . '/generation_summary.json',
    json_encode($summary, JSON_PRETTY_PRINT)
);

error_log("
=== SCHEDULE GENERATION COMPLETE ===
Total Sections: {$summary['total_sections']}
Successfully Scheduled: {$summary['successfully_scheduled']}
Skipped: {$summary['skipped']}
Total Entries: {$summary['total_schedule_entries']}
===================================
");

header("Location: edit_schedule.php");
exit;