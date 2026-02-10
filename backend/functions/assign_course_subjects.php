<?php
include __DIR__ . '/../connection/connection.php';

$section_course = trim($_POST['section_course'] ?? '');
$section_year   = trim($_POST['year_level'] ?? '');
$subjects       = $_POST['subjects'] ?? []; // checkbox array

if (!$section_course || !$section_year) {
    die("Missing course or year");
}

/* =========================
   DELETE OLD ASSIGNMENTS
========================= */
$del = $conn->prepare("
    DELETE FROM assign_section_subjects
    WHERE section_course = ? AND section_year = ?
");
$del->bind_param("ss", $section_course, $section_year);
$del->execute();
$del->close();

/* =========================
   INSERT NEW ASSIGNMENTS
========================= */
if (!empty($subjects)) {
    $insert = $conn->prepare("
        INSERT INTO assign_section_subjects
        (assign_subject, section_course, section_year)
        VALUES (?, ?, ?)
    ");

    foreach ($subjects as $subj) {
        $subj = trim($subj);
        if ($subj === '') continue;

        $insert->bind_param("sss", $subj, $section_course, $section_year);
        $insert->execute();
    }

    $insert->close();
}

$conn->close();

/* =========================
   REDIRECT BACK
========================= */
header("Location: ../../frontend/page_manage/manage.php?tab=course");
exit;
