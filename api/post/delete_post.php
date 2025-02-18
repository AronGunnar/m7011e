<?php
// Include necessary files
include('../../db_connection.php');
include('../auth.php');

// Set response header
header('Content-Type: application/json');

// Validate JWT
$user = validate_jwt();
if (!$user) {
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'Unauthorized: Invalid or expired token']);
    exit;
}

$user_id = $user->user_id;
$user_role = $user->role;

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405); // Not Allowed
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

// Read request body
$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['post_id'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Post ID is required']);
    exit;
}

$post_id = intval($data['post_id']);

// Fetch post (owner)
$sql_fetch_post = "SELECT user_id FROM Posts WHERE post_id = ?";
$stmt = $conn->prepare($sql_fetch_post);
$stmt->bind_param('i', $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404); // Not Found
    echo json_encode(['error' => 'Post not found']);
    exit;
}

$post = $result->fetch_assoc();
$post_owner_id = $post['user_id'];

// Check if the user is allowed to delete the post
if ($user_id === $post_owner_id || $user_role === 'admin' || $user_role === 'editor') {
    // delete post
    $sql_delete_post = "DELETE FROM Posts WHERE post_id = ?";
    $stmt_delete = $conn->prepare($sql_delete_post);
    $stmt_delete->bind_param('i', $post_id);

    if ($stmt_delete->execute()) {
        http_response_code(200); // OK
        echo json_encode(['success' => 'Post deleted successfully']);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'Error deleting post']);
    }
} else {
    http_response_code(403); // Forbidden
    echo json_encode(['error' => 'You are not authorized to delete this post']);
}
?>
