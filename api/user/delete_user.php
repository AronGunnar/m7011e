<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include('../../db_connection.php');

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'Only admins can delete users']);
    exit;
}

// Check if user_id is provided
if (isset($_POST['user_id'])) {
    $user_id_to_delete = intval($_POST['user_id']); // Sanitize input

    // Delete the user from the Users table
    $sql_delete_user = "DELETE FROM Users WHERE user_id = ?";
    $stmt_delete = $conn->prepare($sql_delete_user);
    $stmt_delete->bind_param('i', $user_id_to_delete);

    if ($stmt_delete->execute()) {
        echo json_encode(['success' => 'User deleted successfully']);
    } else {
        echo json_encode(['error' => 'Error deleting user']);
    }
} else {
    echo json_encode(['error' => 'Missing required parameters']);
}
?>
