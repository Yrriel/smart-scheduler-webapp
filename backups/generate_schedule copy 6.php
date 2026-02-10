<?php

session_start();

/* ============================

   generate_schedule function:
        - Generate Schedule from OpenAI
        - Redirect to edit.php after generating.

        Note from Dev: 
            this generation type relies on free tier of openai, means it'll
            be slower but hey, atleast it's free.

            if it had an error of : Maximum execution time of 120 seconds exceeded
            just increase the time. check readme.txt

============================ */

// if (isset($_SESSION['schedule_running'])) {
//     die("⏳ Schedule generation already in progress.");
// }

// $_SESSION['schedule_running'] = true;

/* ============================

    planning to uncomment that section above so that
    there will be no multiple requests

============================ */


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
   BUILD AI INPUT
============================ */

$inputData = [
    'assigned_subjects' => $subjects,
    'sections' => $sections,
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

/* ============================
   OPENAI CLIENT (PSR-18)
============================ */

$symfonyClient = HttpClient::create([
    'timeout' => 120,
    'max_duration' => 180
]);

$psr18Client = new Psr18Client($symfonyClient);

$client = OpenAI::factory()
    ->withApiKey($OPENAI_API_KEY)
    ->withHttpClient($psr18Client)
    ->make();

/* ============================
   PROMPT (STRICT SCHEMA)
============================ */

$prompt = <<<PROMPT
You are a deterministic university scheduling engine.

Return ONLY valid JSON.
Do NOT include explanations, comments, markdown, or any non-JSON text.

OUTPUT FORMAT
Return a SINGLE JSON OBJECT with this exact structure:

{
  "schedules": [ <array of schedule entries> ]
}

Each schedule entry MUST include EXACTLY these fields:
section, subject, subject_name, faculty, room, day, time_start, time_end

TIME RULES
- School hours are 07:30 to 17:30.
- NO classes between 12:00 and 13:00.
- Class time ranges MUST NOT overlap the lunch period.
- Use 24-hour HH:mm format.
- time_start must be earlier than time_end.

SCHEDULING RULES
- Schedule each section independently.
- Avoid unnecessary gaps.
- If a subject is 3 hours and if it will overlap to lunchbreak or end of class, split by half and move the class to another valid time or day.

CONFLICT RULES
- No two schedule entries may overlap in time on the same day.
- Faculty members may teach ONLY ONE section at any given time.
- One room may host ONLY ONE section at any given time.
- A section may have ONLY ONE class at any given time.
- Time overlap includes partial overlap, not just identical start and end times.
- If any conflict occurs, move the class to another valid time or day.
- Any available room may be used regardless of room type (including CHEMLAB, AVR, or NSTP),
  provided the room is not occupied at that time and has sufficient capacity
  for the section.
- A room may be assigned to a section ONLY if room_capacity is greater than or equal to the section's total_students.


EXAMPLE OUTPUT FORMAT:

{
  "schedules": [
    {
      "section": "BSCS 1A",
      "subject": "COMPROG1",
      "subject_name": "Computer Programming 1",
      "faculty": "Juan Dela Cruz",
      "room": "LAB 101",
      "day": "Monday",
      "time_start": "08:00",
      "time_end": "09:30"
    }
  ]
}
PROMPT;

/* ============================
   CALL OPENAI
============================ */

/* ============================
   GENERATE PER SECTION (FIX B)
============================ */

$allSchedules = [];
$skippedSections = [];

foreach ($sections as $section) {

    // Build section-specific input
    $sectionInputData = [
        'assigned_subjects' => array_values(array_filter(
            $subjects,
            fn($s) =>
                $s['section_course'] === $section['section_course'] &&
                $s['section_year'] === $section['section_year']
        )),
        'sections' => [$section],
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

    $response = null;
    $maxRetries = 5;
    $delay = 2;

    for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
        try {
            $response = $client->chat()->create([
                'model' => 'gpt-4.1-mini',
                'max_tokens' => 1200,
                'messages' => [
                    ['role' => 'system', 'content' => 'You generate conflict-free university schedules and output JSON only.'],
                    ['role' => 'user', 'content' => $prompt . "\n\nDATA:\n" . json_encode($sectionInputData)]
                ],
                'response_format' => ['type' => 'json_object']
            ]);
            break;
        } catch (
            \OpenAI\Exceptions\RateLimitException |
            \OpenAI\Exceptions\TransporterException $e
        ) {
            // If still rate-limited after retries, skip this section
            if ($attempt === $maxRetries) {
                error_log(
                    "Skipped section {$section['section_name']} due to rate limit."
                );
                $skippedSections[] = $section['section_name'];
                continue 2; // ⬅ exit retry loop AND move to next section
            }

            sleep($delay);
            $delay *= 2;
        }
    }

    if (
        !$response ||
        !isset($response['choices'][0]['message']['content'])
    ) {
        continue;
    }

    $jsonText = $response['choices'][0]['message']['content'];
    $data = json_decode($jsonText, true);

    if (
        !is_array($data) ||
        !isset($data['schedules']) ||
        !is_array($data['schedules'])
    ) {
        continue;
    }

    foreach ($data['schedules'] as $row) {
        $allSchedules[] = $row;
    }

    // pacing — CRITICAL
    /* ============================
        There's a Request Rate limit, delay is needed.
       ============================ */
    sleep(2);
}


$rows = $allSchedules;

file_put_contents(
    __DIR__ . '/skipped_sections.json',
    json_encode($skippedSections, JSON_PRETTY_PRINT)
);


file_put_contents(
    __DIR__ . '/output.json',
    json_encode(
        ['schedules' => $rows],
        JSON_PRETTY_PRINT
    )
);


/* ============================
   DONE
============================ */

// unset($_SESSION['schedule_running']);

header("Location: edit_schedule.php");
exit;
