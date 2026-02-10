<?php
include __DIR__ . '/../connection/connection.php';

$id = $_POST['id'] ?? '';
$course = trim($_POST['course'] ?? '');
$course_name = trim($_POST['course_name'] ?? '');
$short_name = trim($_POST['short_name'] ?? '');
$type = trim($_POST['type'] ?? '');
// $units = trim($_POST['units'] ?? '');
// $hours = trim($_POST['hours'] ?? '');
$status = trim($_POST['status'] ?? '');

if (!$id || !$course || !$course_name) {
    echo "Missing fields!";
    exit;
}

$stmt = $conn->prepare("UPDATE manage_courses 
    SET course=?, course_name=?, short_name=?, type=?, status=? 
    WHERE id=?");
$stmt->bind_param("sssssi", $course, $course_name, $short_name, $type, $status, $id);

if ($stmt->execute()) {
    header("Location: ../../frontend/page_manage/manage.php?tab=course");
    exit;
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
