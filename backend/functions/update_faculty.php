<?php
include __DIR__ . '/../connection/connection.php';


$id = $_POST['id'] ?? '';
$faculty_name = trim($_POST['faculty_name'] ?? '');
$employment_type = trim($_POST['employment_type'] ?? '');
$current_status = trim($_POST['current_status'] ?? '');
$total_hours_per_week = trim($_POST['total_hours_per_week'] ?? '');

// From subject checkbox table
$subjects = $_POST['subjects'] ?? [];           // array
$preferred_days = $_POST['preferred_day'] ?? []; // array

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

        echo "Subject: $subj | affected rows: " . $insert->affected_rows . "<br>";
    }

    $insert->close();
    //exit; // ⬅️ VERY IMPORTANT: stop redirect so you can see output
}


/* =========================
   UPDATE PREFERRED DAYS
========================= */

// Remove old preferred days
$delDays = $conn->prepare("
    DELETE FROM manage_faculty_days
    WHERE faculty_name = ?
");
$delDays->bind_param("s", $faculty_name);
$delDays->execute();
$delDays->close();

// Insert new preferred days
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

$conn->close();

/* =========================
   REDIRECT BACK TO FACULTY TAB
========================= */
header("Location: ../../frontend/page_manage/manage.php?tab=faculty");
exit;
