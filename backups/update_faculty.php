<?php
include __DIR__ . '/../connection/connection.php';

$id = $_POST['id'] ?? '';
$faculty_name = trim($_POST['faculty_name'] ?? '');
$employment_type = trim($_POST['employment_type'] ?? '');
$current_status = trim($_POST['current_status'] ?? '');
$total_hours_per_week = trim($_POST['total_hours_per_week'] ?? '');
$subjects = isset($_POST['subjects_list']) ? explode(",", $_POST['subjects_list']) : [];

if (!$id || !$faculty_name) {
    echo "Missing fields!";
    exit;
}

// UPDATE faculty main info
$stmt = $conn->prepare("UPDATE manage_faculty 
    SET faculty_name=?, employment_type=?, current_status=?, total_hours_per_week=?
    WHERE id=?");
$stmt->bind_param("ssssi", $faculty_name, $employment_type, $current_status, $total_hours_per_week, $id);
$stmt->execute();
$stmt->close();

// DELETE old subjects first
$del = $conn->prepare("DELETE FROM manage_faculty_subject WHERE faculty_name = ?");
$del->bind_param("s", $faculty_name);
$del->execute();
$del->close();

// INSERT new subjects
if (!empty($subjects)) {
    $insert = $conn->prepare("INSERT INTO manage_faculty_subject (faculty_name, subject) VALUES (?, ?)");
    foreach ($subjects as $subj) {
        $insert->bind_param("ss", $faculty_name, $subj);
        $insert->execute();
    }
    $insert->close();
}

$conn->close();
header("Location: ../../frontend/page_manage/manage.php");
?>
