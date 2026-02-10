<?php

session_start();

require __DIR__ . '/../../vendor/autoload.php';
include __DIR__ . '/../connection/connection.php';
include __DIR__ . '/../../config_openai.php';

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Psr18Client;

/* ============================
   CONFLICT DETECTION FUNCTIONS
============================ */

function hasTimeOverlap($start1, $end1, $start2, $end2) {
    return $start1 < $end2 && $end1 > $start2;
}

function detectConflicts($schedules) {
    $conflicts = [];
    $count = count($schedules);
    
    for ($i = 0; $i < $count; $i++) {
        for ($j = $i + 1; $j < $count; $j++) {
            $a = $schedules[$i];
            $b = $schedules[$j];
            
            // Skip if different days
            if ($a['day'] !== $b['day']) continue;
            
            // Check time overlap
            if (!hasTimeOverlap($a['time_start'], $a['time_end'], $b['time_start'], $b['time_end'])) {
                continue;
            }
            
            // Detect specific conflict types
            $conflictTypes = [];
            
            if ($a['section'] === $b['section']) {
                $conflictTypes[] = 'SECTION_OVERLAP';
            }
            
            if ($a['faculty'] === $b['faculty']) {
                $conflictTypes[] = 'FACULTY_DOUBLE_BOOKED';
            }
            
            if ($a['room'] === $b['room']) {
                $conflictTypes[] = 'ROOM_DOUBLE_BOOKED';
            }
            
            if (!empty($conflictTypes)) {
                $conflicts[] = [
                    'type' => implode(' + ', $conflictTypes),
                    'entry1' => $a,
                    'entry2' => $b,
                    'entry1_index' => $i,
                    'entry2_index' => $j
                ];
            }
        }
    }
    
    return $conflicts;
}

function validateScheduleEntry($entry, $section, $rooms) {
    $errors = [];
    
    // Check required fields
    $required = ['section', 'subject', 'subject_name', 'faculty', 'room', 'day', 'time_start', 'time_end'];
    foreach ($required as $field) {
        if (!isset($entry[$field]) || empty($entry[$field])) {
            $errors[] = "Missing field: $field";
        }
    }
    
    if (!empty($errors)) return $errors;
    
    // Validate time format
    if (!preg_match('/^\d{2}:\d{2}$/', $entry['time_start']) || 
        !preg_match('/^\d{2}:\d{2}$/', $entry['time_end'])) {
        $errors[] = "Invalid time format";
    }
    
    // Validate time logic
    if ($entry['time_start'] >= $entry['time_end']) {
        $errors[] = "time_start must be before time_end";
    }
    
    // Validate school hours
    if ($entry['time_start'] < '07:30' || $entry['time_end'] > '17:30') {
        $errors[] = "Outside school hours (07:30-17:30)";
    }
    
    // Check lunch overlap
    if (hasTimeOverlap($entry['time_start'], $entry['time_end'], '12:00', '13:00')) {
        $errors[] = "Overlaps lunch period (12:00-13:00)";
    }
    
    // Validate room capacity
    foreach ($rooms as $room) {
        if ($room['room_name'] === $entry['room']) {
            if ($room['room_capacity'] < $section['total_students']) {
                $errors[] = "Room capacity insufficient ({$room['room_capacity']} < {$section['total_students']})";
            }
            break;
        }
    }
    
    return $errors;
}

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
   OPENAI CLIENT
============================ */

// Validate API key exists
if (empty($OPENAI_API_KEY)) {
    die("❌ OPENAI_API_KEY is not set in config_openai.php");
}

error_log("✓ OpenAI API Key found (length: " . strlen($OPENAI_API_KEY) . ")");

$symfonyClient = HttpClient::create([
    'timeout' => 180,
    'max_duration' => 300
]);

$psr18Client = new Psr18Client($symfonyClient);

try {
    $client = OpenAI::factory()
        ->withApiKey($OPENAI_API_KEY)
        ->withHttpClient($psr18Client)
        ->make();
    
    error_log("✓ OpenAI client initialized successfully");
    
} catch (\Exception $e) {
    die("❌ Failed to initialize OpenAI client: " . $e->getMessage());
}

/* ============================
   PROMPTS
============================ */

$initialPrompt = <<<'PROMPT'
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
- Classes cannot overlap 12:00-13:00 period

SCHEDULING RULES:
1. Schedule ALL subjects for the given section
2. Use faculty who are QUALIFIED (check faculty_subjects) and AVAILABLE (check faculty_days)
3. Assign rooms where room_capacity >= section total_students
4. Check EXISTING_SCHEDULES to avoid conflicts
5. If duration causes lunch overlap, split or move to another day

CONFLICT CHECKS (validate against EXISTING_SCHEDULES):
- NO duplicate: (section + day + time)
- NO duplicate: (faculty + day + time)
- NO duplicate: (room + day + time)

Time overlap: A overlaps B if same day AND (A.start < B.end AND A.end > B.start)

MANDATORY: Schedule ALL subjects in assigned_subjects.
PROMPT;

$conflictResolutionPrompt = <<<'PROMPT'
You are fixing scheduling conflicts in a university schedule.

CRITICAL: Return ONLY valid JSON. No markdown, no explanations.

You will receive:
1. CONFLICTS: List of conflicting schedule entries
2. VALID_SCHEDULES: All non-conflicting schedules that MUST remain unchanged
3. AVAILABLE_RESOURCES: Faculty, rooms, and constraints

YOUR TASK:
- For EACH conflict, generate NEW schedule entries that resolve the conflict
- DO NOT modify or include entries from VALID_SCHEDULES
- Ensure new entries don't conflict with VALID_SCHEDULES or each other
- Return ONLY the FIXED entries (not the entire schedule)

OUTPUT FORMAT:
{
  "fixed_schedules": [
    {
      "original_conflict_index": <number>,
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

CONFLICT RESOLUTION STRATEGIES:
1. Try different time on same day
2. Try different day
3. Try different room (if only room conflict)
4. Split class sessions if needed
5. Use alternative qualified faculty if needed

TIME RULES (same as before):
- School hours: 07:30 to 17:30
- NO classes during lunch: 12:00 to 13:00
- No overlap with 12:00-13:00
- Use 24-hour HH:mm format
PROMPT;

/* ============================
   INITIAL SCHEDULE GENERATION
============================ */

$allSchedules = [];
$skippedSections = [];

error_log("
╔════════════════════════════════════════════╗
║   STARTING SCHEDULE GENERATION             ║
╚════════════════════════════════════════════╝
");

foreach ($sections as $section) {
    $sectionName = $section['section_name'];
    
    error_log("\n--- Processing Section: $sectionName ---");
    
    $sectionSubjects = array_values(array_filter(
        $subjects,
        fn($s) =>
            $s['section_course'] === $section['section_course'] &&
            $s['section_year'] === $section['section_year']
    ));

    if (empty($sectionSubjects)) {
        error_log("⚠️  No subjects for: $sectionName (Course: {$section['section_course']}, Year: {$section['section_year']})");
        error_log("⚠️  Available subjects total: " . count($subjects));
        $skippedSections[] = $sectionName;
        continue;
    }
    
    error_log("📚 Found " . count($sectionSubjects) . " subjects for $sectionName");
    
    // Get qualified faculty for these subjects
    $qualifiedFaculty = [];
    foreach ($sectionSubjects as $subj) {
        foreach ($faculty_subjects as $fname => $fsubjects) {
            if (in_array($subj['subject'], $fsubjects)) {
                $qualifiedFaculty[$fname] = true;
            }
        }
    }
    error_log("👨‍🏫 Found " . count($qualifiedFaculty) . " qualified faculty members");
    
    if (empty($qualifiedFaculty)) {
        error_log("❌ No qualified faculty found for subjects in $sectionName");
        $skippedSections[] = $sectionName;
        continue;
    }

    $sectionInputData = [
        'current_section' => $section,
        'assigned_subjects' => $sectionSubjects,
        'faculty' => $faculty,
        'faculty_days' => $faculty_days,
        'faculty_subjects' => $faculty_subjects,
        'rooms' => $rooms,
        'existing_schedules' => $allSchedules,
        'constraints' => [
            'days' => ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],
            'start_time' => '07:30',
            'end_time' => '17:30'
        ]
    ];

    $response = null;
    $maxRetries = 3;

    for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
        try {
            error_log("🔄 Calling OpenAI for $sectionName (attempt $attempt)...");
            
            $response = $client->chat()->create([
                'model' => 'gpt-4o-mini',
                'max_tokens' => 2000,
                'temperature' => 0.3,
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a university scheduling system. Return only valid JSON.'],
                    ['role' => 'user', 'content' => $initialPrompt . "\n\nDATA:\n" . json_encode($sectionInputData, JSON_PRETTY_PRINT)]
                ],
                'response_format' => ['type' => 'json_object']
            ]);
            
            error_log("✓ API call successful for $sectionName");
            break;
            
        } catch (\OpenAI\Exceptions\ErrorException $e) {
            error_log("❌ OpenAI API Error for $sectionName (attempt $attempt): " . $e->getMessage());
            error_log("❌ Error Code: " . $e->getCode());
            
            if ($attempt === $maxRetries) {
                $skippedSections[] = $sectionName;
                continue 2;
            }
            
            sleep(3 * $attempt);
            
        } catch (\Exception $e) {
            error_log("❌ Unexpected error for $sectionName (attempt $attempt): " . $e->getMessage());
            error_log("❌ Exception type: " . get_class($e));
            
            if ($attempt === $maxRetries) {
                $skippedSections[] = $sectionName;
                continue 2;
            }
            
            sleep(3 * $attempt);
        }
    }

    if (!$response || !isset($response['choices'][0]['message']['content'])) {
        error_log("❌ Empty or invalid response for $sectionName");
        if ($response) {
            error_log("❌ Response structure: " . json_encode(array_keys((array)$response)));
        }
        $skippedSections[] = $sectionName;
        continue;
    }

    error_log("📝 Got response for $sectionName, parsing JSON...");
    
    $jsonText = preg_replace('/^```json\s*|\s*```$/m', '', $response['choices'][0]['message']['content']);
    
    error_log("📄 JSON length: " . strlen($jsonText) . " characters");
    
    $data = json_decode($jsonText, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("❌ JSON decode error for $sectionName: " . json_last_error_msg());
        error_log("❌ Raw response: " . substr($jsonText, 0, 500));
        $skippedSections[] = $sectionName;
        continue;
    }
    
    if (!isset($data['schedules']) || !is_array($data['schedules'])) {
        error_log("❌ No schedules array for $sectionName");
        error_log("❌ Data structure: " . json_encode(array_keys($data)));
        $skippedSections[] = $sectionName;
        continue;
    }

    $validEntries = 0;
    foreach ($data['schedules'] as $row) {
        $validationErrors = validateScheduleEntry($row, $section, $rooms);
        
        if (empty($validationErrors)) {
            $allSchedules[] = $row;
            $validEntries++;
        } else {
            error_log("⚠️  Invalid entry for $sectionName: " . implode(', ', $validationErrors));
        }
    }

    error_log("✓ Generated $validEntries entries for $sectionName (Total so far: " . count($allSchedules) . ")");
    sleep(4);
}

error_log("
╔════════════════════════════════════════════╗
║   INITIAL GENERATION COMPLETE              ║
║   Total Entries: " . str_pad(count($allSchedules), 25) . "║
║   Skipped Sections: " . str_pad(count($skippedSections), 21) . "║
╚════════════════════════════════════════════╝
");

// CRITICAL: If no schedules generated, stop here
if (empty($allSchedules)) {
    error_log("❌ CRITICAL: No schedules were generated!");
    error_log("❌ Check the error logs above for details.");
    
    $summary = [
        'timestamp' => date('Y-m-d H:i:s'),
        'total_sections' => count($sections),
        'successfully_scheduled' => 0,
        'skipped_sections' => count($skippedSections),
        'total_schedule_entries' => 0,
        'status' => 'FAILED - No schedules generated'
    ];
    
    file_put_contents(__DIR__ . '/generation_summary.json', json_encode($summary, JSON_PRETTY_PRINT));
    file_put_contents(__DIR__ . '/output.json', json_encode(['schedules' => []], JSON_PRETTY_PRINT));
    
    die("❌ Schedule generation failed. Check generation_summary.json and error logs.");
}

/* ============================
   CONFLICT DETECTION & RESOLUTION
============================ */

error_log("
╔════════════════════════════════════════════╗
║   DETECTING CONFLICTS                      ║
╚════════════════════════════════════════════╝
");

$conflicts = detectConflicts($allSchedules);

if (empty($conflicts)) {
    error_log("✓ No conflicts detected! Schedule is valid.");
    
    // Save immediately if no conflicts
    file_put_contents(
        __DIR__ . '/output.json',
        json_encode(['schedules' => $allSchedules], JSON_PRETTY_PRINT)
    );
    
} else {
    error_log("⚠️  Found " . count($conflicts) . " conflicts. Initiating auto-resolution...\n");
    
    // Log conflicts
    foreach ($conflicts as $idx => $conflict) {
        error_log("Conflict #" . ($idx + 1) . " - " . $conflict['type']);
        error_log("  Entry 1: {$conflict['entry1']['section']} | {$conflict['entry1']['subject']} | {$conflict['entry1']['faculty']} | {$conflict['entry1']['room']} | {$conflict['entry1']['day']} {$conflict['entry1']['time_start']}-{$conflict['entry1']['time_end']}");
        error_log("  Entry 2: {$conflict['entry2']['section']} | {$conflict['entry2']['subject']} | {$conflict['entry2']['faculty']} | {$conflict['entry2']['room']} | {$conflict['entry2']['day']} {$conflict['entry2']['time_start']}-{$conflict['entry2']['time_end']}\n");
    }
    
    // Extract indices of conflicting entries
    $conflictIndices = [];
    foreach ($conflicts as $conflict) {
        $conflictIndices[] = $conflict['entry1_index'];
        $conflictIndices[] = $conflict['entry2_index'];
    }
    $conflictIndices = array_unique($conflictIndices);
    sort($conflictIndices);
    
    // Separate valid and conflicting entries
    $validSchedules = [];
    $conflictingEntries = [];
    
    foreach ($allSchedules as $idx => $entry) {
        if (in_array($idx, $conflictIndices)) {
            $conflictingEntries[] = [
                'index' => $idx,
                'entry' => $entry
            ];
        } else {
            $validSchedules[] = $entry;
        }
    }
    
    error_log("Valid entries: " . count($validSchedules));
    error_log("Conflicting entries: " . count($conflictingEntries));
    
    // Prepare conflict resolution input
    $conflictResolutionInput = [
        'conflicts' => $conflicts,
        'conflicting_entries' => $conflictingEntries,
        'valid_schedules' => $validSchedules,
        'faculty' => $faculty,
        'faculty_days' => $faculty_days,
        'faculty_subjects' => $faculty_subjects,
        'rooms' => $rooms,
        'constraints' => [
            'days' => ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],
            'start_time' => '07:30',
            'end_time' => '17:30'
        ]
    ];
    
    // Call AI to resolve conflicts
    $maxConflictRetries = 3;
    $resolved = false;
    
    for ($retryAttempt = 1; $retryAttempt <= $maxConflictRetries; $retryAttempt++) {
        error_log("\n🔄 Conflict resolution attempt #$retryAttempt...");
        
        try {
            $conflictResponse = $client->chat()->create([
                'model' => 'gpt-4o-mini',
                'max_tokens' => 3000,
                'temperature' => 0.5,
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a conflict resolution specialist for university schedules. Return only valid JSON.'],
                    ['role' => 'user', 'content' => $conflictResolutionPrompt . "\n\nCONFLICT DATA:\n" . json_encode($conflictResolutionInput, JSON_PRETTY_PRINT)]
                ],
                'response_format' => ['type' => 'json_object']
            ]);
            
            $conflictJsonText = preg_replace('/^```json\s*|\s*```$/m', '', $conflictResponse['choices'][0]['message']['content']);
            $conflictData = json_decode($conflictJsonText, true);
            
            if (json_last_error() !== JSON_ERROR_NONE || !isset($conflictData['fixed_schedules'])) {
                error_log("❌ Invalid JSON from conflict resolution");
                sleep(5);
                continue;
            }
            
            // Replace conflicting entries with fixed ones
            $finalSchedules = $validSchedules;
            
            foreach ($conflictData['fixed_schedules'] as $fixed) {
                // Validate fixed entry
                $section = null;
                foreach ($sections as $s) {
                    if ($s['section_name'] === $fixed['section']) {
                        $section = $s;
                        break;
                    }
                }
                
                if (!$section) continue;
                
                $validationErrors = validateScheduleEntry($fixed, $section, $rooms);
                
                if (empty($validationErrors)) {
                    $finalSchedules[] = $fixed;
                } else {
                    error_log("⚠️  Fixed entry still invalid: " . implode(', ', $validationErrors));
                }
            }
            
            // Check if conflicts are actually resolved
            $remainingConflicts = detectConflicts($finalSchedules);
            
            if (empty($remainingConflicts)) {
                error_log("✓ All conflicts resolved!");
                $allSchedules = $finalSchedules;
                $resolved = true;
                break;
            } else {
                error_log("⚠️  " . count($remainingConflicts) . " conflicts remain after attempt $retryAttempt");
                
                // Update for next retry
                $conflictResolutionInput['conflicts'] = $remainingConflicts;
                sleep(5);
            }
            
        } catch (\Exception $e) {
            error_log("❌ Conflict resolution error: " . $e->getMessage());
            sleep(5);
        }
    }
    
    if (!$resolved) {
        error_log("⚠️  WARNING: Could not fully resolve all conflicts after $maxConflictRetries attempts");
        error_log("⚠️  Proceeding with best available schedule. Manual review required.");
        
        // Don't lose the schedule - save what we have
        file_put_contents(
            __DIR__ . '/output.json',
            json_encode(['schedules' => $allSchedules], JSON_PRETTY_PRINT)
        );
    } else {
        // Save the resolved schedule
        file_put_contents(
            __DIR__ . '/output.json',
            json_encode(['schedules' => $allSchedules], JSON_PRETTY_PRINT)
        );
    }
}

/* ============================
   SAVE RESULTS
============================ */

// Main schedule should already be saved above, but save again as backup
file_put_contents(
    __DIR__ . '/output.json',
    json_encode(['schedules' => $allSchedules], JSON_PRETTY_PRINT)
);

error_log("💾 Saved " . count($allSchedules) . " schedule entries to output.json");

if (!empty($skippedSections)) {
    file_put_contents(
        __DIR__ . '/skipped_sections.json',
        json_encode([
            'timestamp' => date('Y-m-d H:i:s'),
            'count' => count($skippedSections),
            'sections' => $skippedSections
        ], JSON_PRETTY_PRINT)
    );
}

$finalConflicts = detectConflicts($allSchedules);

$summary = [
    'timestamp' => date('Y-m-d H:i:s'),
    'total_sections' => count($sections),
    'successfully_scheduled' => count($sections) - count($skippedSections),
    'skipped_sections' => count($skippedSections),
    'total_schedule_entries' => count($allSchedules),
    'conflicts_detected_initially' => count($conflicts),
    'conflicts_remaining' => count($finalConflicts),
    'status' => empty($finalConflicts) ? 'SUCCESS' : 'NEEDS_REVIEW'
];

file_put_contents(
    __DIR__ . '/generation_summary.json',
    json_encode($summary, JSON_PRETTY_PRINT)
);

if (!empty($finalConflicts)) {
    file_put_contents(
        __DIR__ . '/remaining_conflicts.json',
        json_encode($finalConflicts, JSON_PRETTY_PRINT)
    );
}

error_log("
╔════════════════════════════════════════════╗
║   SCHEDULE GENERATION COMPLETE             ║
╠════════════════════════════════════════════╣
║ Total Sections: " . str_pad($summary['total_sections'], 23) . "║
║ Successfully Scheduled: " . str_pad($summary['successfully_scheduled'], 15) . "║
║ Skipped: " . str_pad($summary['skipped_sections'], 30) . "║
║ Total Entries: " . str_pad($summary['total_schedule_entries'], 24) . "║
║ Initial Conflicts: " . str_pad($summary['conflicts_detected_initially'], 20) . "║
║ Remaining Conflicts: " . str_pad($summary['conflicts_remaining'], 18) . "║
║ Status: " . str_pad($summary['status'], 31) . "║
╚════════════════════════════════════════════╝
");

header("Location: edit_schedule.php");
exit;