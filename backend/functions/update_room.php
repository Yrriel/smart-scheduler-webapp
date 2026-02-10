<?php
session_start();
include __DIR__ . '/../connection/connection.php';

$id = $_POST['id'] ?? '';
$room_name = trim($_POST['room_name'] ?? '');
$room_capacity = trim($_POST['room_capacity'] ?? '');

// Validate required fields
if (!$id || !$room_name || !$room_capacity) {
    echo "All fields are required.";
    exit;
}

// Update room info
$stmt = $conn->prepare("
    UPDATE manage_rooms 
    SET room_name = ?, room_capacity = ?
    WHERE id = ?
");

$stmt->bind_param("sii", $room_name, $room_capacity, $id);

if ($stmt->execute()) {
    header("Location: ../../frontend/page_manage/manage.php?tab=room");
    exit;
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
