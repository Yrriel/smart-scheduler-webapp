<?php
session_start();
require __DIR__ . '/../connection/connection.php';


$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($username) || empty($password)) {
    echo "Username and password required.";
    exit;
}

$stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // Plain text password comparison
    if ($password === $row['password']) {
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $username;

        // Redirect to dashboard
        header("Location: ../../frontend/page_dashboard/dashboard.php");
        exit;
    } else {
        echo "Invalid password.";
    }
} else {
    echo "User not found.";
}

$stmt->close();
$conn->close();
?>
