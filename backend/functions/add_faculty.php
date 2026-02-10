<?php
session_start();
include __DIR__ . '/../connection/connection.php';

/* =========================
   GET POST VALUES
========================= */
$faculty_name         = trim($_POST['faculty_name'] ?? '');
$employment_type      = trim($_POST['employment_type'] ?? '');
$current_status       = trim($_POST['current_status'] ?? '');
$total_hours_per_week = trim($_POST['total_hours_per_week'] ?? '');

$preferred_days = $_POST['preferred_day'] ?? [];   // array
$subjects       = $_POST['subjects'] ?? [];        // array from checkboxes

/* =========================
   VALIDATION
========================= */
if (
    $faculty_name === '' ||
    $employment_type === '' ||
    $current_status === '' ||
    $total_hours_per_week === ''
) {
    die("All fields are required.");
}

/* =========================
   CALCULATE TOTAL UNITS
========================= */
$total_units = 0;

if (!empty($subjects)) {

    // Build ?,?,? placeholders
    $placeholders = implode(",", array_fill(0, count($subjects), "?"));

    $sql = "
        SELECT units 
        FROM manage_subjects 
        WHERE subject IN ($placeholders)
    ";

    $stmtUnits = $conn->prepare($sql);

    $types = str_repeat("s", count($subjects));
    $stmtUnits->bind_param($types, ...$subjects);

    $stmtUnits->execute();
    $resultUnits = $stmtUnits->get_result();

    while ($row = $resultUnits->fetch_assoc()) {
        $total_units += (int)$row['units'];
    }

    $stmtUnits->close();
}

/* =========================
   INSERT FACULTY
========================= */
$stmt = $conn->prepare("
    INSERT INTO manage_faculty
        (faculty_name, employment_type, current_status, total_units, total_hours_per_week)
    VALUES (?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "sssii",
    $faculty_name,
    $employment_type,
    $current_status,
    $total_units,
    $total_hours_per_week
);

if (!$stmt->execute()) {
    die("Insert failed: " . $stmt->error);
}

$stmt->close();

/* =========================
   INSERT PREFERRED DAYS
========================= */
if (!empty($preferred_days)) {
    $insertDay = $conn->prepare("
        INSERT INTO manage_faculty_days (faculty_name, day)
        VALUES (?, ?)
    ");

    foreach ($preferred_days as $day) {
        $day = trim($day);
        if ($day === '') continue;

        $insertDay->bind_param("ss", $faculty_name, $day);
        $insertDay->execute();
    }

    $insertDay->close();
}

/* =========================
   INSERT SUBJECTS
========================= */
if (!empty($subjects)) {
    $insertSubject = $conn->prepare("
        INSERT INTO manage_faculty_subject (faculty_name, subject)
        VALUES (?, ?)
    ");

    foreach ($subjects as $subj) {
        $subj = trim($subj);
        if ($subj === '') continue;

        $insertSubject->bind_param("ss", $faculty_name, $subj);
        $insertSubject->execute();
    }

    $insertSubject->close();
}

/* =========================
   CLEANUP & REDIRECT
========================= */
$conn->close();

header("Location: ../../frontend/page_manage/manage.php?tab=faculty");
exit;
