<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include('../../db_connection.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

// Get the user ID from session
$user_id = $_SESSION['user_id'];

// Read the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

// Check if bio is provided in the request body
if (isset($data['bio'])) {
    $bio = $data['bio'];

    // Update bio in the Profiles table
    $sql_update_bio = "UPDATE Profiles SET bio = ? WHERE user_id = ?";
    $stmt_update_bio = $conn->prepare($sql_update_bio);
    $stmt_update_bio->bind_param('si', $bio, $user_id);

    if ($stmt_update_bio->execute()) {
        echo json_encode(['success' => 'Bio updated successfully']);
    } else {
        echo json_encode(['error' => 'Failed to update bio']);
    }
} else {
    echo json_encode(['error' => 'Bio parameter missing']);
}
?>
