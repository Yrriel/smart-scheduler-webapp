<?php
session_start();
include "connection.php";

// Get POST values
$faculty_name = trim($_POST['faculty_name'] ?? '');
$employment_type = trim($_POST['employment_type'] ?? '');
$current_status = trim($_POST['current_status'] ?? '');
$total_units = trim($_POST['total_units'] ?? '');
$total_hours_per_week = trim($_POST['total_hours_per_week'] ?? '');

$preferred_days = $_POST['preferred_day'] ?? []; // <-- array of selected days

// Validate required fields (detailed errors)
if (!$faculty_name) { echo "Faculty name is required."; exit; }
if (!$employment_type) { echo "Employment type is required."; exit; }
if (!$current_status) { echo "Current status is required."; exit; }
if (!$total_units) { echo "Total units is required."; exit; }
if (!$total_hours_per_week) { echo "Total hours per week is required."; exit; }

// Insert into manage_faculty
$stmt = $conn->prepare("
    INSERT INTO manage_faculty 
    (faculty_name, employment_type, current_status, total_units, total_hours_per_week) 
    VALUES (?, ?, ?, ?, ?)
");

$stmt->bind_param("sssss", 
    $faculty_name, 
    $employment_type, 
    $current_status, 
    $total_units, 
    $total_hours_per_week
);

if ($stmt->execute()) {

    // Insert preferred days into manage_faculty_days
    if (!empty($preferred_days)) {
        
        $insertDay = $conn->prepare("
            INSERT INTO manage_faculty_days (faculty_name, day)
            VALUES (?, ?)
        ");

        foreach ($preferred_days as $day) {
            $insertDay->bind_param("ss", $faculty_name, $day);
            $insertDay->execute();
        }

        $insertDay->close();
    }

    header("Location: manage.php");
    exit;

} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
