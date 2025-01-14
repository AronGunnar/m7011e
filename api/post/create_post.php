<?php
// Include database connection
include($_SERVER['DOCUMENT_ROOT'] . '/m7011e/db_connection.php');

// Handle form submission via API
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the user is logged in
    session_start();
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'You must be logged in to create a post.']);
        exit;
    }

    // Get the form data from the request
    $title = $_POST['title'];
    $content = $_POST['content'];
    $tags = json_decode($_POST['tags'], true); // Decode the JSON array of tags

    // Validate input
    if (empty($title) || empty($content)) {
        echo json_encode(['success' => false, 'message' => 'Title and content are required.']);
        exit;
    }

    // Insert the new post into the Posts table
    $sql_insert_post = "INSERT INTO Posts (user_id, title, content) VALUES (?, ?, ?)";
    $stmt_post = $conn->prepare($sql_insert_post);
    $stmt_post->bind_param('iss', $_SESSION['user_id'], $title, $content);

    if ($stmt_post->execute()) {
        $post_id = $stmt_post->insert_id;

        // Insert the selected tags into the Post_Tags table
        if (!empty($tags)) {
            foreach ($tags as $tag_id) {
                $sql_insert_tag = "INSERT INTO Post_Tags (post_id, tag_id) VALUES (?, ?)";
                $stmt_tag = $conn->prepare($sql_insert_tag);
                $stmt_tag->bind_param('ii', $post_id, $tag_id);
                $stmt_tag->execute();
            }
        }

        echo json_encode(['success' => true, 'message' => 'Post created successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error creating post: ' . $stmt_post->error]);
    }
}
?>
