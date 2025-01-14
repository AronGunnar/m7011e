<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Destroy the session to log the user out
session_destroy();

echo json_encode(['success' => 'Logged out successfully']);
?>
