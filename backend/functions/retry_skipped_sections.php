<?php

set_time_limit(0);

require __DIR__ . '/../../vendor/autoload.php';
include __DIR__ . '/../connection/connection.php';
include __DIR__ . '/../../config_openai.php';

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Psr18Client;

/* ============================
   LOAD SKIPPED SECTIONS
============================ */

$skippedFile = __DIR__ . '/skipped_sections.json';

if (!file_exists($skippedFile)) {
    die("✅ No skipped sections to retry.");
}

$skippedSections = json_decode(file_get_contents($skippedFile), true);

if (empty($skippedSections)) {
    die("✅ No skipped sections to retry.");
}

/* ============================
   FETCH DATA
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

$symfonyClient = HttpClient::create([
    'timeout' => 120,
    'max_duration' => 180
]);

$client = OpenAI::factory()
    ->withApiKey($OPENAI_API_KEY)
    ->withHttpClient(new Psr18Client($symfonyClient))
    ->make();

/* ============================
   LOAD EXISTING OUTPUT
============================ */

$outputFile = __DIR__ . '/output.json';
$output = json_decode(file_get_contents($outputFile), true);
$existingSchedules = $output['schedules'] ?? [];

/* ============================
   RETRY ONLY SKIPPED SECTIONS
============================ */

$newSchedules = [];
$stillSkipped = [];

foreach ($sections as $section) {

    if (!in_array($section['section_name'], $skippedSections)) {
        continue;
    }

    file_put_contents(__DIR__.'/retry_trace.log', "Generating {$section['section_name']}\n", FILE_APPEND);


    // pacing
    sleep(3);

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

    try {
        $response = $client->chat()->create([
            'model' => 'gpt-4.1-mini',
            'max_tokens' => 1200,
            'messages' => [
                ['role' => 'system', 'content' => 'You generate conflict-free university schedules and output JSON only.'],
                ['role' => 'user', 'content' => file_get_contents(__DIR__.'/prompt.txt') . "\n\nDATA:\n" . json_encode($sectionInputData)]
            ],
            'response_format' => ['type' => 'json_object']
        ]);

        $data = json_decode($response['choices'][0]['message']['content'], true);

        file_put_contents(
            __DIR__ . '/retry_raw_response.log',
            "SECTION: {$section['section_name']}\n" .
            ($response['choices'][0]['message']['content'] ?? 'NO CONTENT') .
            "\n\n-----------------\n",
            FILE_APPEND
        );

        file_put_contents(
            __DIR__ . "/retry_all_logs.log", FILE_APPEND
        );


        if (!isset($data['schedules']) || empty($data['schedules'])) {
            $stillSkipped[] = $section['section_name'];
            continue;
        }

        foreach ($data['schedules'] as $row) {
            $newSchedules[] = $row;
        }

    } catch (\Throwable $e) {
        $stillSkipped[] = $section['section_name'];
    }
}

/* ============================
   MERGE & SAVE
============================ */

// Remove old placeholders for retried sections
$existingSchedules = array_filter(
    $existingSchedules,
    fn($r) => !in_array($r['section'], $skippedSections)
);

$merged = array_merge($existingSchedules, $newSchedules);

file_put_contents(
    $outputFile,
    json_encode(['schedules' => array_values($merged)], JSON_PRETTY_PRINT)
);

file_put_contents(
    $skippedFile,
    json_encode($stillSkipped, JSON_PRETTY_PRINT)
);

header("Location: edit_schedule.php");
exit;
