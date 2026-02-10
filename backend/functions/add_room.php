<?php
session_start();
include __DIR__ . '/../connection/connection.php';

// Get POST values from the form
$room_name = trim($_POST['room_name'] ?? '');
$room_capacity = trim($_POST['room_capacity'] ?? '');

// Validate required fields
if (!$room_name || !$room_capacity) {
    echo "All fields are required.";
    exit;
}

// Insert into the database
$stmt = $conn->prepare("INSERT INTO manage_rooms (room_name, room_capacity) VALUES (?, ?)");
$stmt->bind_param("si", $room_name, $room_capacity);

if ($stmt->execute()) {
    header("Location: ../../frontend/page_manage/manage.php?tab=room");
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
