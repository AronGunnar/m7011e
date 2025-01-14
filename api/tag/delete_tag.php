<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include('../../db_connection.php');

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

// Check if tag ID is provided in the GET request
if (isset($_GET['tag_id'])) {
    $tag_id = $_GET['tag_id'];

    // Delete the tag from the database
    $sql_delete_tag = "DELETE FROM Tags WHERE tag_id = ?";
    $stmt_delete_tag = $conn->prepare($sql_delete_tag);
    $stmt_delete_tag->bind_param('i', $tag_id);

    if ($stmt_delete_tag->execute()) {
        echo json_encode(['success' => 'Tag deleted successfully']);
    } else {
        echo json_encode(['error' => 'Failed to delete tag']);
    }
} else {
    echo json_encode(['error' => 'Tag ID parameter missing']);
}
?>
