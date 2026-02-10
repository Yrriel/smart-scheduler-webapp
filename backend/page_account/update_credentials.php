<?php
session_start();
require_once __DIR__ . '/../connection/connection.php';

// Make sure user is logged in
$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    die('Unauthorized access');
}

$username = trim($_POST['username'] ?? '');
$current  = $_POST['current_password'] ?? '';
$new      = $_POST['new_password'] ?? '';
$confirm  = $_POST['confirm_password'] ?? '';

if ($username === '' || $current === '' || $new === '' || $confirm === '') {
    die('All fields are required.');
}

if ($new !== $confirm) {
    die('New passwords do not match.');
}

// Fetch current password (PLAIN TEXT)
$stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if (!$result) {
    die('User not found.');
}

// Check current password directly (NO HASH)
if ($current !== $result['password']) {
    die('Current password is incorrect.');
}

// Update username and password (PLAIN TEXT)
$stmt = $conn->prepare("
    UPDATE users 
    SET username = ?, password = ?
    WHERE id = ?
");
$stmt->bind_param("ssi", $username, $new, $userId);
$stmt->execute();

// Redirect back with success flag
header("Location: ../../frontend/page_account/account.php?success=1");
exit;

