<?php

require __DIR__ . '/../connection/connection.php';

$editFile   = __DIR__ . '/edit-output.json';
$outputFile = __DIR__ . '/output.json';

/* 🔥 STEP 1: overwrite output.json with edited version */
if (!file_exists($editFile)) {
    die("❌ edit-output.json not found.");
}

if (!copy($editFile, $outputFile)) {
    die("❌ Failed to update output.json from edit-output.json.");
}

/* 🔥 STEP 2: continue using output.json (unchanged logic below) */
$jsonFile = $outputFile;

if (!file_exists($jsonFile)) {
    die("❌ output.json not found.");
}

$data = json_decode(file_get_contents($jsonFile), true);

if (!isset($data['schedules']) || !is_array($data['schedules'])) {
    die("❌ Invalid schedule format.");
}


$conn->query("TRUNCATE TABLE generated_schedule");

$stmt = $conn->prepare("
    INSERT INTO generated_schedule
    (section, subject, subject_name, faculty, room, day, time_start, time_end)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

$inserted = 0;

foreach ($data['schedules'] as $r) {
    $required = [
        'section','subject','subject_name',
        'faculty','room','day',
        'time_start','time_end'
    ];

    foreach ($required as $k) {
        if (empty($r[$k])) {
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

header("Location: ../../frontend/page_schedule/schedule-ui.php");
exit;
