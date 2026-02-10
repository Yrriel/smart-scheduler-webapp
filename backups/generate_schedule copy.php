<?php

require __DIR__ .  '/../../vendor/autoload.php';
include __DIR__ . '/../connection/connection.php';
include __DIR__ .  '/../../config_openai.php';

use OpenAI; // <-- correct for openai-php/client

/* ============================
   COLLECT DATA FROM DATABASE
============================ */

$subjects = $conn->query("SELECT * FROM assign_section_subjects")->fetch_all(MYSQLI_ASSOC);

$faculty   = $conn->query("SELECT * FROM manage_faculty")->fetch_all(MYSQLI_ASSOC);
$rooms     = $conn->query("SELECT * FROM manage_rooms")->fetch_all(MYSQLI_ASSOC);
$sections  = $conn->query("SELECT * FROM manage_sections")->fetch_all(MYSQLI_ASSOC);

// Faculty → preferred days
$faculty_days = [];
$daysRes = $conn->query("SELECT faculty_name, day FROM manage_faculty_days");
while ($d = $daysRes->fetch_assoc()) {
    $faculty_days[$d['faculty_name']][] = $d['day'];
}

// Faculty → subjects
$faculty_subjects = [];
$subRes = $conn->query("SELECT faculty_name, subject FROM manage_faculty_subject");
while ($s = $subRes->fetch_assoc()) {
    $faculty_subjects[$s['faculty_name']][] = $s['subject'];
}

/* ============================
   BUILD INPUT DATA FOR AI
============================ */

$inputData = [
    'subjects' => $subjects,
    'faculty' => $faculty,
    'faculty_days' => $faculty_days,
    'faculty_subjects' => $faculty_subjects,
    'rooms' => $rooms,
    'sections' => $sections,
    'constraints' => [
        'days' => ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],
        'start_time' => '07:00',
        'end_time' => '21:00',
        'max_hours_per_day_per_faculty' => 6,
        'max_concurrent_classes_per_room' => 1
    ]
];

/* ============================
   CREATE OPENAI CLIENT
============================ */

$client = OpenAI::client($OPENAI_API_KEY);

/* ============================
   PROMPT
============================ */

$prompt = <<<PROMPT
You are a university scheduling engine. Return ONLY JSON, no explanations.

Each schedule row must look like:

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
   CALL OPENAI API
============================ */

$response = $client->chat()->create([
    'model' => 'gpt-4.1-mini',
    'messages' => [
        ['role' => 'system', 'content' => 'You are an expert scheduling engine.'],
        ['role' => 'user', 'content' => $prompt . "\n\nDATA:\n" . json_encode($inputData)]
    ],
    'response_format' => ['type' => 'json_object']
]);

// Raw JSON from the model
$jsonText = $response['choices'][0]['message']['content'] ?? '[]';

/* ============================
   PARSE JSON
============================ */

// Save the RAW Respone returned by the model
file_put_contents("outputresponse.txt", $response);

// Save the RAW JSON returned by the model
file_put_contents("output.txt", $jsonText);

$schedule = json_decode($jsonText, true);


if (!is_array($schedule)) {
    die("AI returned invalid JSON:<br><pre>" . htmlspecialchars($jsonText) . "</pre>");
}

/* ============================
   SAVE TO DATABASE
============================ */

// Some models return { "schedule": [ ... ] }
// Others may return the array directly.
// Handle both safely:
$rows = $schedule['schedule'] ?? $schedule;

if (!is_array($rows)) {
    die("Schedule is not an array.");
}

$conn->query("TRUNCATE TABLE generated_schedule");

$stmt = $conn->prepare("
    INSERT INTO generated_schedule
    (section, subject, subject_name, faculty, room, day, time_start, time_end)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

foreach ($rows as $row) {

    // Validate required fields
    if (
        empty($row['section']) ||
        empty($row['subject']) ||
        empty($row['subject_name']) ||
        empty($row['faculty']) ||
        empty($row['room']) ||
        empty($row['day']) ||
        empty($row['time_start']) ||
        empty($row['time_end'])
    ) {
        continue; // skip incomplete rows
    }

    $stmt->bind_param(
        "ssssssss",
        $row['section'],
        $row['subject'],
        $row['subject_name'],
        $row['faculty'],
        $row['room'],
        $row['day'],
        $row['time_start'],
        $row['time_end']
    );

    $stmt->execute();
}

$stmt->close();

header("Location: ../../frontend/page_schedule/schedule.php");
exit;
