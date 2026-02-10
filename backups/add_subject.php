<?php
session_start();
include __DIR__ . '/../connection/connection.php'; // Your DB connection

// Get POST values from the form
$subject = trim($_POST['subject_code'] ?? '');
$subject_name = trim($_POST['subject_name'] ?? '');
$short_name = trim($_POST['short_name'] ?? '');
$type = trim($_POST['type'] ?? '');
if (!in_array($type, ['Lecture', 'Laboratory'])) {
    $type = 'Lecture'; // default fallback
}
$units = trim($_POST['units'] ?? '');
$hours = trim($_POST['hours_per_week'] ?? '');
$status = trim($_POST['status'] ?? '');

// Validate required fields
if (!$subject || !$subject_name || !$short_name || !$type || !$units || !$hours || !$status) {
    echo "All fields are required.";
    exit;
}

// Insert into the database
$stmt = $conn->prepare("INSERT INTO manage_subjects (subject, subject_name, short_name, type, units, hours, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssiss", $subject, $subject_name, $short_name, $type, $units, $hours, $status);

if ($stmt->execute()) {
    header("Location: ../../frontend/page_manage/manage.php?tab=subject");
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
