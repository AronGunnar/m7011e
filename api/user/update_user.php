<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include('../../db_connection.php');

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'Only admins can update user roles']);
    exit;
}

// Check if required parameters are provided
if (isset($_POST['user_id'], $_POST['new_role'])) {
    $user_id_to_update = intval($_POST['user_id']); // Sanitize input
    $new_role = $_POST['new_role'];

    // Validate role
    $valid_roles = ['user', 'editor', 'admin'];
    if (!in_array($new_role, $valid_roles)) {
        echo json_encode(['error' => 'Invalid role']);
        exit;
    }

    // Update user role in the database
    $sql_update_role = "UPDATE Users SET role = ? WHERE user_id = ?";
    $stmt_update_role = $conn->prepare($sql_update_role);
    $stmt_update_role->bind_param('si', $new_role, $user_id_to_update);

    if ($stmt_update_role->execute()) {
        echo json_encode(['success' => 'User role updated successfully']);
    } else {
        echo json_encode(['error' => 'Error updating user role']);
    }
} else {
    echo json_encode(['error' => 'Missing required parameters']);
}
?>
