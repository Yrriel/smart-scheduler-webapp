<?php

require __DIR__ . '/../../vendor/autoload.php';
include __DIR__ . '/../connection/connection.php';
include __DIR__ . '/../../config_openai.php';

use OpenAI;

/* ============================
   FETCH & NORMALIZE DATA
============================ */

/**
 * Assigned subjects (COURSE + YEAR + SUBJECT DETAILS)
 */
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
    die("❌ No assigned subjects found. Please assign subjects to courses first.");
}

/**
 * Sections (used to map COURSE + YEAR → SECTION NAME)
 */
$sections = $conn->query("
    SELECT 
        section_name,
        section_course,
        section_year
    FROM manage_sections
")->fetch_all(MYSQLI_ASSOC);

/**
 * Faculty
 */
$faculty = $conn->query("
    SELECT 
        faculty_name,
        employment_type,
        current_status,
        total_hours_per_week
    FROM manage_faculty
")->fetch_all(MYSQLI_ASSOC);

/**
 * Rooms
 */
$rooms = $conn->query("
    SELECT room_name, room_capacity
    FROM manage_rooms
")->fetch_all(MYSQLI_ASSOC);

/**
 * Faculty preferred days
 */
$faculty_days = [];
$res = $conn->query("SELECT faculty_name, day FROM manage_faculty_days");
while ($r = $res->fetch_assoc()) {
    $faculty_days[$r['faculty_name']][] = $r['day'];
}

/**
 * Faculty subjects
 */
$faculty_subjects = [];
$res = $conn->query("SELECT faculty_name, subject FROM manage_faculty_subject");
while ($r = $res->fetch_assoc()) {
    $faculty_subjects[$r['faculty_name']][] = $r['subject'];
}

/* ============================
   BUILD AI INPUT PAYLOAD
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
        'end_time' => '21:00',
        'max_hours_per_day_per_faculty' => 6,
        'room_conflicts' => false
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
You are a university scheduling engine.

You will be given:
- Subjects assigned to a course and year
- Sections with course and year level
- Faculty with preferred days and subjects
- Rooms

Rules:
1. Match subjects to sections using course + year
2. Assign only faculty who can teach the subject
3. Respect faculty preferred days
4. Do not overlap faculty or rooms
5. Respect working hours (07:00–21:00)

Return ONLY valid JSON.
NO explanation text.

Each schedule entry MUST be:

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

Return an ARRAY of objects.
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

$jsonText = $response['choices'][0]['message']['content'] ?? '[]';

/* Save raw AI output for debugging */
file_put_contents(__DIR__ . "/output.txt", $jsonText);

/* ============================
   PARSE RESPONSE
============================ */

$data = json_decode($jsonText, true);

if (!$data) {
    die("❌ AI returned invalid JSON.");
}

$rows = $data['schedule'] ?? $data;

if (!is_array($rows)) {
    die("❌ AI response is not an array.");
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

foreach ($rows as $r) {

    if (
        empty($r['section']) ||
        empty($r['subject']) ||
        empty($r['subject_name']) ||
        empty($r['faculty']) ||
        empty($r['room']) ||
        empty($r['day']) ||
        empty($r['time_start']) ||
        empty($r['time_end'])
    ) {
        continue;
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
}

$stmt->close();
$conn->close();

/* ============================
   REDIRECT
============================ */

header("Location: ../../frontend/page_schedule/schedule.php");
exit;
