<?php
session_start();
header("Content-Type: application/json");

// If there’s an active session, destroy it
if (isset($_SESSION['user_id'])) {
    // Unset all session variables
    $_SESSION = [];

    // Destroy the session cookie (if it exists)
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Finally destroy the session
    session_destroy();

    header("Location: ../../index.html");
} else {
    echo json_encode(["success" => false, "message" => "No active session"]);
}
?>
