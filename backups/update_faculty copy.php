<?php
include "connection.php";

$id = $_POST['id'] ?? '';
$faculty_name = trim($_POST['faculty_name'] ?? '');
$employment_type = trim($_POST['employment_type'] ?? '');
$current_status = trim($_POST['current_status'] ?? '');
$total_units = trim($_POST['total_units'] ?? '');
$total_hours_per_week = trim($_POST['total_hours_per_week'] ?? '');

if (!$id || !$faculty_name) {
    echo "Missing fields!";
    exit;
}

$stmt = $conn->prepare("UPDATE manage_faculty 
    SET faculty_name=?, employment_type=?, current_status=?, total_units=?, total_hours_per_week=? 
    WHERE id=?");
$stmt->bind_param("sssssi", $faculty_name, $employment_type, $current_status, $total_units, $total_hours_per_week, $id);

if ($stmt->execute()) {
    header("Location: manage.php");
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
