<?php
include __DIR__ . '/../connection/connection.php';

$id = $_POST['id'] ?? '';
$faculty_name = trim($_POST['faculty_name'] ?? '');
$employment_type = trim($_POST['employment_type'] ?? '');
$current_status = trim($_POST['current_status'] ?? '');
$total_hours_per_week = trim($_POST['total_hours_per_week'] ?? '');

// ✅ FROM CHECKBOX TABLE
$subjects = $_POST['subjects'] ?? []; // array of subject codes

if (!$id || !$faculty_name) {
    die("Missing required fields");
}

/* =========================
   UPDATE FACULTY MAIN INFO
========================= */
$stmt = $conn->prepare("
    UPDATE manage_faculty 
    SET faculty_name = ?, 
        employment_type = ?, 
        current_status = ?, 
        total_hours_per_week = ?
    WHERE id = ?
");
$stmt->bind_param(
    "ssssi",
    $faculty_name,
    $employment_type,
    $current_status,
    $total_hours_per_week,
    $id
);
$stmt->execute();
$stmt->close();

/* =========================
   UPDATE FACULTY SUBJECTS
========================= */

// Remove old subjects
$del = $conn->prepare("
    DELETE FROM manage_faculty_subject 
    WHERE faculty_name = ?
");
$del->bind_param("s", $faculty_name);
$del->execute();
$del->close();

// Insert new subjects
if (!empty($subjects)) {
    $insert = $conn->prepare("
        INSERT INTO manage_faculty_subject (faculty_name, subject)
        VALUES (?, ?)
    ");

    foreach ($subjects as $subj) {
        $subj = trim($subj);
        if ($subj === '') continue;

        $insert->bind_param("ss", $faculty_name, $subj);
        $insert->execute();
    }

    $insert->close();
}

$conn->close();

/* =========================
   REDIRECT BACK
========================= */
header("Location: ../../frontend/page_manage/manage.php?tab=faculty");
exit;
