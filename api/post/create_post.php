<?php
// Include necessary files
include('../../db_connection.php');
include('../auth.php');

// Validate JWT
$user_data = validate_jwt();
if (!$user_data) {
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'Access denied: Unauthorized']);
    exit;
}

// Authorization Error response 
if ($user_data->role !== 'admin' && $user_data->role !== 'editor' && $user_data->role !== 'user') {
    http_response_code(403); // Forbidden
    echo json_encode(['error' => 'Access denied: Insufficient permissions']);
    exit;
}

// Read JSON input from request body
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['title'], $data['content'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Title and content are required']);
    exit;
}

$title = $data['title'];
$content = $data['content'];
$tags = isset($data['tags']) ? $data['tags'] : [];

// Insert post
$sql = "INSERT INTO Posts (title, content, user_id) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ssi', $title, $content, $user_data->user_id);

if ($stmt->execute()) {
    $post_id = $stmt->insert_id;

    if (!empty($tags)) {
        foreach ($tags as $tag_id) {
            $sql_tag = "INSERT INTO Post_Tags (post_id, tag_id) VALUES (?, ?)";
            $stmt_tag = $conn->prepare($sql_tag);
            $stmt_tag->bind_param('ii', $post_id, $tag_id);
            $stmt_tag->execute();
        }
    }

    http_response_code(201); // Created
    echo json_encode(['success' => true, 'message' => 'Post created successfully']);
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Failed to create post']);
}
?>
