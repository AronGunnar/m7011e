<?php
// Set content type to JSON
header('Content-Type: application/json');

// Include database connection
include($_SERVER['DOCUMENT_ROOT'] . '/m7011e/db_connection.php');

// Function to fetch tags for a post
function getTagsForPost($post_id, $conn) {
    $sql_tags = "SELECT tags.tag_name FROM Post_Tags pt 
                 JOIN Tags tags ON pt.tag_id = tags.tag_id 
                 WHERE pt.post_id = ?";
    $stmt_tags = $conn->prepare($sql_tags);
    $stmt_tags->bind_param('i', $post_id);
    $stmt_tags->execute();
    $result_tags = $stmt_tags->get_result();

    $tags = [];
    while ($tag = $result_tags->fetch_assoc()) {
        $tags[] = $tag['tag_name'];
    }
    return $tags;
}

try {
    // Fetch all posts along with their author information
    $sql = "SELECT posts.post_id, posts.title, posts.content, posts.created_at, 
                   users.username, posts.user_id
            FROM Posts posts 
            INNER JOIN Users users ON posts.user_id = users.user_id 
            ORDER BY posts.created_at DESC";
    $result = $conn->query($sql);

    if (!$result) {
        http_response_code(500); // Internal Server Error
        throw new Exception("Failed to fetch posts: " . $conn->error);
    }

    $posts = [];

    while ($row = $result->fetch_assoc()) {
        $post = [
            'post_id' => $row['post_id'],
            'title' => htmlspecialchars($row['title']),
            'content' => htmlspecialchars($row['content']),
            'created_at' => $row['created_at'],
            'username' => htmlspecialchars($row['username']),
            'user_id' => $row['user_id'],
            'tags' => getTagsForPost($row['post_id'], $conn),
        ];

        $posts[] = $post;
    }

    if (empty($posts)) {
        http_response_code(404); // Not Found
        echo json_encode(['error' => 'No posts found']);
        exit;
    }

    // Send success response
    http_response_code(200); // OK
    echo json_encode(['success' => true, 'posts' => $posts]);
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
