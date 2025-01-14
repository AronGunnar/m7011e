<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set content type to JSON
header('Content-Type: application/json');

// Include database connection (use absolute path or correct relative path)
include($_SERVER['DOCUMENT_ROOT'] . '/m7011e/db_connection.php');  // Change this to your correct path

// Check if the connection was successful
if (!$conn) {
    die('Connection failed: ' . mysqli_connect_error());
}

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
        throw new Exception("Failed to fetch posts: " . $conn->error);
    }

    $posts = [];
    $current_user_id = $_SESSION['user_id'] ?? null;
    $current_user_role = $_SESSION['role'] ?? null;

    while ($row = $result->fetch_assoc()) {
        $post = [
            'post_id' => $row['post_id'],
            'title' => htmlspecialchars($row['title']),
            'content' => htmlspecialchars($row['content']),
            'created_at' => $row['created_at'],
            'username' => htmlspecialchars($row['username']),
            'user_id' => $row['user_id'],
            'tags' => getTagsForPost($row['post_id'], $conn),
            'current_user_can_edit' => ($current_user_id == $row['user_id'] || in_array($current_user_role, ['admin', 'editor'])),
            'current_user_can_delete' => ($current_user_id == $row['user_id'] || in_array($current_user_role, ['admin', 'editor'])),
        ];

        $posts[] = $post;
    }

    // Send success response
    echo json_encode([
        'success' => true,
        'posts' => $posts,
    ]);
} catch (Exception $e) {
    // Send error response
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
    ]);
}
?>
