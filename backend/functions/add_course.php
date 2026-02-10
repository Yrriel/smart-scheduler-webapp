<?php
session_start();
include __DIR__ . '/../connection/connection.php';
/* */
// Get POST values from the form
$course = trim($_POST['course'] ?? '');
$course_name = trim($_POST['course_name'] ?? '');
$short_name = trim($_POST['short_name'] ?? '');
$type = trim($_POST['type'] ?? '');
// $units = trim($_POST['units'] ?? '');
// $hours = trim($_POST['hours'] ?? '');
$status = trim($_POST['status'] ?? '');

// Validate required fields
if (!$course || !$course_name || !$short_name || !$type || /*!$units || !$hours */ !$status) {
    echo "All fields are required.";
    exit;
}

// Insert into the database
$stmt = $conn->prepare("INSERT INTO manage_courses (course, course_name, short_name, type, /* units, hours, */ status) VALUES (?, ?, ?, ?,/* ?, ?,*/ ?)");
$stmt->bind_param("sssss", $course, $course_name, $short_name, $type, /* $units, $hours,*/ $status);

if ($stmt->execute()) {
    header("Location: ../../frontend/page_manage/manage.php?tab=course");
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
