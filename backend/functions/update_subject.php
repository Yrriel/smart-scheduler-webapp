<?php
include __DIR__ . '/../connection/connection.php';

/* =========================
   INPUT
========================= */
$id           = $_POST['id'] ?? '';
$subject      = trim($_POST['subject_code'] ?? '');
$subject_name = trim($_POST['subject_name'] ?? '');
$short_name   = trim($_POST['short_name'] ?? '');
$typeInput    = trim($_POST['type'] ?? 'Lecture');
$units        = (int) ($_POST['units'] ?? 0);
$hours        = trim($_POST['hours_per_week'] ?? '');
$status       = trim($_POST['status'] ?? '');

$hasLab = ($typeInput === 'Laboratory');

if (!$id || !$subject || !$subject_name) {
    die("❌ Missing required fields.");
}

/* =========================
   UPDATE LECTURE ROW (ALWAYS)
========================= */
$lectureType = 'Lecture';

$updateLecture = $conn->prepare("
    UPDATE manage_subjects
    SET subject = ?, subject_name = ?, short_name = ?, type = ?, units = ?, hours = ?, status = ?
    WHERE id = ?
");
$updateLecture->bind_param(
    "ssssissi",
    $subject,
    $subject_name,
    $short_name,
    $lectureType,
    $units,
    $hours,
    $status,
    $id
);

if (!$updateLecture->execute()) {
    die("❌ Failed to update lecture subject.");
}
$updateLecture->close();

/* =========================
   CHECK IF LAB ROW EXISTS
========================= */
$checkLab = $conn->prepare("
    SELECT id FROM manage_subjects
    WHERE subject = ? AND type = 'Laboratory'
");
$checkLab->bind_param("s", $subject);
$checkLab->execute();
$labResult = $checkLab->get_result();
$labExists = ($labResult->num_rows > 0);
$labId = $labExists ? $labResult->fetch_assoc()['id'] : null;
$checkLab->close();

/* =========================
   HANDLE LAB LOGIC
========================= */
if ($hasLab) {

    // INSERT LAB ONLY IF NOT EXISTS
    if (!$labExists) {
        $insertLab = $conn->prepare("
            INSERT INTO manage_subjects
            (subject, subject_name, short_name, type, units, hours, status)
            VALUES (?, ?, ?, 'Laboratory', ?, ?, ?)
        ");
        $insertLab->bind_param(
            "sssiss",
            $subject,
            $subject_name,
            $short_name,
            $units,
            $hours,
            $status
        );
        $insertLab->execute();
        $insertLab->close();
    }

} else {
    // DELETE LAB IF USER REMOVED IT
    if ($labExists) {
        $deleteLab = $conn->prepare("
            DELETE FROM manage_subjects
            WHERE id = ?
        ");
        $deleteLab->bind_param("i", $labId);
        $deleteLab->execute();
        $deleteLab->close();
    }
}

/* =========================
   REDIRECT
========================= */
$conn->close();
header("Location: ../../frontend/page_manage/manage.php?tab=subject");
exit;
