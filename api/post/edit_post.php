<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include('../../db_connection.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

// Fetch the current user role
$user_role = $_SESSION['role'] ?? '';
$current_user_id = $_SESSION['user_id'];

// Check if post_id and new data are provided
if (isset($_POST['post_id'], $_POST['title'], $_POST['content'])) {
    $post_id = intval($_POST['post_id']); // Sanitize input
    $new_title = $_POST['title'];
    $new_content = $_POST['content'];
    $new_selected_tags = $_POST['tags'] ?? [];

    // Fetch the post owner
    $sql_fetch_post = "SELECT user_id FROM Posts WHERE post_id = ?";
    $stmt = $conn->prepare($sql_fetch_post);
    $stmt->bind_param('i', $post_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $post = $result->fetch_assoc();
        $post_owner_id = $post['user_id'];

        // Check if the current user is authorized to edit the post
        if ($current_user_id == $post_owner_id || $user_role == 'admin' || $user_role == 'editor') {
            // Update the post in the Posts table
            $sql_update_post = "UPDATE Posts SET title = ?, content = ? WHERE post_id = ?";
            $stmt_update = $conn->prepare($sql_update_post);
            $stmt_update->bind_param('ssi', $new_title, $new_content, $post_id);

            if ($stmt_update->execute()) {
                // Delete old tags for this post
                $sql_delete_tags = "DELETE FROM Post_Tags WHERE post_id = ?";
                $stmt_delete = $conn->prepare($sql_delete_tags);
                $stmt_delete->bind_param('i', $post_id);
                $stmt_delete->execute();

                // Insert new tags into the Post_Tags table
                foreach ($new_selected_tags as $tag_id) {
                    $sql_insert_tag = "INSERT INTO Post_Tags (post_id, tag_id) VALUES (?, ?)";
                    $stmt_insert_tag = $conn->prepare($sql_insert_tag);
                    $stmt_insert_tag->bind_param('ii', $post_id, $tag_id);
                    $stmt_insert_tag->execute();
                }

                echo json_encode(['success' => 'Post updated successfully']);
            } else {
                echo json_encode(['error' => 'Error updating post']);
            }
        } else {
            echo json_encode(['error' => 'Unauthorized to edit this post']);
        }
    } else {
        echo json_encode(['error' => 'Post not found']);
    }
} else {
    echo json_encode(['error' => 'Missing required parameters']);
}
?>
