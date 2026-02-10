<?php
session_start();
include __DIR__ . '/../connection/connection.php';

// Get POST values from the form
$section_name = trim($_POST['section_name'] ?? '');
$total_students = trim($_POST['total_students'] ?? '');
$total_units = trim($_POST['section_course'] ?? '');
$total_hours_per_week = trim($_POST['section_year'] ?? '');

// Validate required fields
if (!$section_name || !$total_students || !$total_units || !$total_hours_per_week) {
    echo "All fields are required.";
    exit;
}

// Insert into the database
$stmt = $conn->prepare("INSERT INTO manage_sections (section_name, total_students, section_course, section_year) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $section_name, $total_students, $total_units, $total_hours_per_week);

if ($stmt->execute()) {
    header("Location: ../../frontend/page_manage/manage.php?tab=section");
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
