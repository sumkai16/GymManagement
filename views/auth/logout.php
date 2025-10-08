<?php
/**
 * Logout Handler
 * Handles user logout and redirects to login page
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear all session data
$_SESSION = array();

// Destroy session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy session
session_destroy();

// Set logout message
session_start();
$_SESSION['success'] = "You have been successfully logged out.";

// Redirect to login page
header("Location: login.php");
exit;
?>
