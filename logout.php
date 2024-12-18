<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Destroy the session and redirect to the login/signup page
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session
header('Location: login.php');
exit;
?>
