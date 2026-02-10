<?php

require __DIR__ . '/../../vendor/autoload.php';
include __DIR__ . '/../connection/connection.php';
include __DIR__ . '/../../config_openai.php';

use OpenAI;

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
    SELECT section_name, section_course, section_year
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
        'start_time' => '07:00',
        'end_time' => '21:00'
    ]
];

/* ============================
   OPENAI CLIENT
============================ */

$client = OpenAI::client($OPENAI_API_KEY);

/* ============================
   PROMPT
============================ */

$prompt = <<<PROMPT
System Prompt

Return ONLY valid JSON.
Do NOT include explanations, comments, or any non-JSON text.

Output Format

Return either:
- a JSON array of schedule entries, or
- a JSON object containing a single array of schedule entries.

Every schedule entry MUST include all of the following fields:
- section
- subject
- subject_name
- faculty
- room
- day
- time_start
- time_end

Scheduling Constraints

Mandatory Vacant Period
- There must be NO scheduled classes between 12:00pm and 1:00pm for any section.
- This vacant window is absolute and must always be respected.
- School opens at 7:30am and closes at 5:30pm.

Section Completion
- For each section, schedule subjects efficiently within the available time.
- If time slots remain after initial scheduling, insert additional subjects where possible.
- The goal is to avoid unnecessary gaps and reduce inconvenience for students.

Subject Duration Rules
- If a subject has a 3-hour duration, it may be split into two equal parts.
- The remaining portion must be scheduled on a different day.

Day Arrangement Preference
- Schedule days should be close to each other or follow a clear pattern, such as:
    - Monday, Tuesday, Wednesday
    - Monday, Wednesday, Friday
    - Tuesday, Thursday, Friday, Saturday
- Prefer consistency and predictable patterns across sections when possible.

Section and Room Exclusivity
- Different sections must NOT be scheduled in the same class at the same time, even if they share the same faculty.
- If a conflict occurs, move the class to another time or another day.
- One room may host only one section at a time.
- No two schedule entries may share the same room, day, and time range.
- No faculty members can share room if the room is already occupied during that time.

Logical Validity
- No overlapping time slots within the same section.
- time_start must always be earlier than time_end.
- Use a consistent 24-hour HH:mm time format for all entries.

Schedule Entry Schema

Each schedule entry MUST follow this structure:

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


PROMPT;

/* ============================
   CALL OPENAI
============================ */

$response = $client->chat()->create([
    'model' => 'gpt-4.1-mini',
    'messages' => [
        ['role' => 'system', 'content' => 'You generate conflict-free university schedules.'],
        ['role' => 'user', 'content' => $prompt . "\n\nDATA:\n" . json_encode($inputData)]
    ],
    'response_format' => ['type' => 'json_object']
]);

$jsonText = $response['choices'][0]['message']['content'] ?? '{}';
file_put_contents(__DIR__ . "/output.json", $jsonText);

/* ============================
   PARSE RESPONSE (FIXED)
============================ */

$data = json_decode($jsonText, true);

if (!is_array($data)) {
    die("❌ Invalid JSON returned.");
}

/**
 * 🔥 SMART NORMALIZATION
 * Finds the FIRST array of schedule rows automatically
 */
$rows = null;

foreach ($data as $value) {
    if (is_array($value) && isset($value[0]) && is_array($value[0])) {
        $rows = $value;
        break;
    }
}

if ($rows === null && isset($data[0])) {
    $rows = $data;
}

if (!is_array($rows)) {
    die("❌ No schedule array found in AI response.");
}

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

    if (!$stmt->execute()) {
        die("❌ DB insert error: " . $stmt->error);
    }

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

header("Location: ../../frontend/page_schedule/schedule-ui.php");
exit;
