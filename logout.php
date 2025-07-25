<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

// Log the logout activity
if (isLoggedIn()) {
    logActivity('User logout', "Username: " . $_SESSION['username']);
}

// Destroy session
session_destroy();

// Clear session cookies
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect to login page
header('Location: index.php?success=logged_out');
exit();
?> 