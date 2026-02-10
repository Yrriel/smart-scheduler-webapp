<?php

session_start();

// if (isset($_SESSION['schedule_running'])) {
//     die("⏳ Schedule generation already in progress.");
// }

// $_SESSION['schedule_running'] = true;



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

$maxRetries = 5;
$delay = 1; // seconds
$response = null;

for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
    try {
        // $response = $client->chat()->create([
        //     'model' => 'gpt-4.1-mini',
        //     'max_tokens' => 4000,
        //     'messages' => [
        //         ['role' => 'system', 'content' => 'You generate conflict-free university schedules and output JSON only.'],
        //         ['role' => 'user', 'content' => $prompt . "\n\nDATA:\n" . json_encode($inputData)]
        //     ],
        //     'response_format' => ['type' => 'json_object']
        // ]);
        $response = $client->chat()->create([
            'model' => 'gpt-4.1-mini',
            'max_tokens' => 50,
            'messages' => [
                ['role' => 'user', 'content' => 'Say "ok"']
            ]
        ]);

        break; // success
    } catch (
        \OpenAI\Exceptions\RateLimitException |
        \OpenAI\Exceptions\TransporterException $e
    ) {
        if ($attempt === $maxRetries) {
            throw $e;
        }
        sleep($delay);
        $delay *= 2; // exponential backoff
    }
}


if (!isset($response['choices'][0]['message']['content'])) {
    die("❌ OpenAI returned no content.");
}

$jsonText = $response['choices'][0]['message']['content'];
file_put_contents(__DIR__ . "/output.json", $jsonText);

/* ============================
   PARSE RESPONSE (FIXED)
============================ */

$data = json_decode($jsonText, true);

if (!is_array($data)) {
    die("❌ Invalid JSON returned.");
}

if (!isset($data['schedules']) || !is_array($data['schedules'])) {
    die("❌ No schedules array found.");
}

$rows = $data['schedules'];

/* ============================
   SAVE TO DATABASE
============================ */

$conn->query("TRUNCATE TABLE generated_schedule");

$stmt = $conn->prepare("
    INSERT INTO generated_schedule
    (section, subject, subject_name, faculty, room, day, time_start, time_end)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

$inserted = 0;
$skipped = [];

foreach ($rows as $r) {
    $required = ['section','subject','subject_name','faculty','room','day','time_start','time_end'];
    foreach ($required as $k) {
        if (empty($r[$k])) {
            $skipped[] = $r;
            continue 2;
        }
    }

    $stmt->bind_param(
        "ssssssss",
        $r['section'],
        $r['subject'],
        $r['subject_name'],
        $r['faculty'],
        $r['room'],
        $r['day'],
        $r['time_start'],
        $r['time_end']
    );

    $stmt->execute();
    $inserted++;
}

$stmt->close();
$conn->close();

file_put_contents(__DIR__ . "/skipped_rows.json", json_encode($skipped, JSON_PRETTY_PRINT));

if ($inserted === 0) {
    die("❌ No rows inserted. Check skipped_rows.json");
}

/* ============================
   DONE
============================ */

// unset($_SESSION['schedule_running']);

header("Location: ../../frontend/page_schedule/schedule-ui.php");
exit;
