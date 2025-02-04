<?php
// Include necessary files
include('../../db_connection.php');
include('../auth.php'); // Include auth.php for JWT validation

// Set response header
header('Content-Type: application/json');

// Validate JWT token
$user = validate_jwt();
if (!$user) {
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'Unauthorized: Invalid or expired token']);
    exit;
}

// Check if the user has admin privileges
if ($user->role !== 'admin') {
    http_response_code(403); // Forbidden
    echo json_encode(['error' => 'Only admins can delete tags']);
    exit;
}

// Check if tag_id is provided in the DELETE request body
$data = json_decode(file_get_contents("php://input"), true);
if (isset($data['tag_id'])) {
    $tag_id = intval($data['tag_id']); // Sanitize input

    // Delete the tag from the database
    $sql_delete_tag = "DELETE FROM Tags WHERE tag_id = ?";
    $stmt_delete_tag = $conn->prepare($sql_delete_tag);
    $stmt_delete_tag->bind_param('i', $tag_id);

    if ($stmt_delete_tag->execute()) {
        http_response_code(200); // OK
        echo json_encode(['success' => 'Tag deleted successfully']);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'Failed to delete tag']);
    }
} else {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Tag ID parameter missing']);
}
?>
