<?php
// Include necessary files
include('../../db_connection.php');
include('../auth.php');

// Validate JWT token
$user_data = validate_jwt();
if (!$user_data) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized: Missing or invalid token']);
    exit;
}

// Check if the request method is PATCH
if ($_SERVER['REQUEST_METHOD'] !== 'PATCH') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Invalid request method. Use PATCH.']);
    exit;
}

// Read raw input data
$data = json_decode(file_get_contents('php://input'), true);

// Get post ID
$post_id = $data['post_id'] ?? null;

if (!$post_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Post ID is required']);
    exit;
}

// Fetch the post owner from the database
$sql_owner = "SELECT user_id FROM Posts WHERE post_id = ?";
$stmt_owner = $conn->prepare($sql_owner);
$stmt_owner->bind_param('i', $post_id);
$stmt_owner->execute();
$result_owner = $stmt_owner->get_result();

if ($result_owner->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Post not found']);
    exit;
}

$post_owner = $result_owner->fetch_assoc()['user_id'];

// Check if the user is the post owner or has admin/editor role
if ($user_data->user_id != $post_owner && $user_data->role !== 'admin' && $user_data->role !== 'editor') {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied: Insufficient permissions']);
    exit;
}

// Prepare dynamic SQL updates
$update_fields = [];
$params = [];
$types = '';

if (!empty($data['title'])) {
    $update_fields[] = "title = ?";
    $params[] = $data['title'];
    $types .= 's';
}

if (!empty($data['content'])) {
    $update_fields[] = "content = ?";
    $params[] = $data['content'];
    $types .= 's';
}

if (empty($update_fields) && !isset($data['tags'])) {
    http_response_code(400);
    echo json_encode(['error' => 'No fields provided for update']);
    exit;
}

// Update post title/content if needed
if (!empty($update_fields)) {
    $sql_update = "UPDATE Posts SET " . implode(', ', $update_fields) . " WHERE post_id = ?";
    $params[] = $post_id;
    $types .= 'i';

    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param($types, ...$params);
    $stmt_update->execute();
}

// Update tags if provided
if (isset($data['tags']) && is_array($data['tags'])) {
    $sql_delete = "DELETE FROM Post_Tags WHERE post_id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param('i', $post_id);
    $stmt_delete->execute();

    foreach ($data['tags'] as $tag_id) {
        $sql_tag = "INSERT INTO Post_Tags (post_id, tag_id) VALUES (?, ?)";
        $stmt_tag = $conn->prepare($sql_tag);
        $stmt_tag->bind_param('ii', $post_id, $tag_id);
        $stmt_tag->execute();
    }
}

http_response_code(200);
echo json_encode(['success' => 'Post updated successfully']);
?>
