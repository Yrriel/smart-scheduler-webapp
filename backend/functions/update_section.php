<?php
session_start();
include __DIR__ . '/../connection/connection.php';

$id = $_POST['id'] ?? '';
$section_name = trim($_POST['section_name'] ?? '');
$total_students = trim($_POST['total_students'] ?? '');
$section_course = trim($_POST['section_course'] ?? '');
$section_year = trim($_POST['section_year'] ?? '');

// Validate required fields
if (!$id || !$section_name || !$total_students || !$section_course || !$section_year) {
    echo "All fields are required.";
    exit;
}

// Update data
$stmt = $conn->prepare("
    UPDATE manage_sections 
    SET section_name=?, total_students=?, section_course=?, section_year=?
    WHERE id=?
");

$stmt->bind_param("ssssi", 
    $section_name, 
    $total_students, 
    $section_course, 
    $section_year, 
    $id
);

if ($stmt->execute()) {
    header("Location: ../../frontend/page_manage/manage.php?tab=section");
    exit;
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
