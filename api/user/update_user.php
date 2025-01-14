<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include('../../db_connection.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

// Check if required POST parameters are provided
if (isset($_POST['email'], $_POST['new_password'])) {
    $user_id = $_SESSION['user_id'];
    $new_email = $_POST['email'];
    $new_password = $_POST['new_password'];

    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Update user details in the database
    $sql_update = "UPDATE Users SET email = ?, password = ? WHERE user_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param('ssi', $new_email, $hashed_password, $user_id);

    if ($stmt_update->execute()) {
        echo json_encode(['success' => 'User profile updated successfully']);
    } else {
        echo json_encode(['error' => 'Error updating user profile']);
    }
} else {
    echo json_encode(['error' => 'Missing required parameters']);
}
?>
