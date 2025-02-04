<?php
// Include necessary files
include('../../db_connection.php');
include('../auth.php');

// Validate JWT token
$user_data = validate_jwt();
if (!$user_data) {
    http_response_code(401);
    echo json_encode(['error' => 'Access denied: Unauthorized']);
    exit;
}

// Allow user to create posts (admin, editor, user)
if ($user_data->role !== 'admin' && $user_data->role !== 'editor' && $user_data->role !== 'user') {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied: Insufficient permissions']);
    exit;
}

// Read JSON input from request body
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['title'], $data['content'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Title and content are required']);
    exit;
}

$title = $data['title'];
$content = $data['content'];
$tags = isset($data['tags']) ? $data['tags'] : [];

// Insert post data into database
$sql = "INSERT INTO Posts (title, content, user_id) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ssi', $title, $content, $user_data->user_id);

if ($stmt->execute()) {
    $post_id = $stmt->insert_id;

    // Insert post tags if provided
    if (!empty($tags)) {
        foreach ($tags as $tag_id) {
            $sql_tag = "INSERT INTO Post_Tags (post_id, tag_id) VALUES (?, ?)";
            $stmt_tag = $conn->prepare($sql_tag);
            $stmt_tag->bind_param('ii', $post_id, $tag_id);
            $stmt_tag->execute();
        }
    }

    http_response_code(201);
    echo json_encode(['success' => true, 'message' => 'Post created successfully']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to create post']);
}
?>
