<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include('../../db_connection.php');

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403); // Forbidden
    echo json_encode(['error' => 'Only admins can delete users']);
    exit;
}

// Check if user_id is provided and the request method is DELETE
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Retrieve user_id from the GET parameter (it could be passed in the body too, but GET works here)
    $user_id_to_delete = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;

    // Ensure user_id is provided
    if ($user_id_to_delete === null) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Missing user_id parameter']);
        exit;
    }

    // Delete the user from the Users table
    $sql_delete_user = "DELETE FROM Users WHERE user_id = ?";
    $stmt_delete = $conn->prepare($sql_delete_user);
    $stmt_delete->bind_param('i', $user_id_to_delete);

    if ($stmt_delete->execute()) {
        echo json_encode(['success' => 'User deleted successfully']);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'Error deleting user']);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Incorrect request method, expected DELETE']);
}
?>
