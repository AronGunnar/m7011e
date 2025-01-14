<?php
// Start session
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

// Check if post_id is provided
if (isset($_POST['post_id'])) {
    $post_id = intval($_POST['post_id']); // Sanitize input

    // Fetch the post owner
    $sql_fetch_post = "SELECT user_id FROM Posts WHERE post_id = ?";
    $stmt = $conn->prepare($sql_fetch_post);
    $stmt->bind_param('i', $post_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $post = $result->fetch_assoc();
        $post_owner_id = $post['user_id'];

        // Check if the current user is authorized to delete the post
        if ($current_user_id == $post_owner_id || $user_role == 'admin' || $user_role == 'editor') {
            // Proceed to delete the post
            $sql_delete_post = "DELETE FROM Posts WHERE post_id = ?";
            $stmt_delete = $conn->prepare($sql_delete_post);
            $stmt_delete->bind_param('i', $post_id);

            if ($stmt_delete->execute()) {
                echo json_encode(['success' => 'Post deleted successfully']);
            } else {
                echo json_encode(['error' => 'Error deleting post']);
            }
        } else {
            echo json_encode(['error' => 'Unauthorized to delete this post']);
        }
    } else {
        echo json_encode(['error' => 'Post not found']);
    }
} else {
    echo json_encode(['error' => 'No post ID provided']);
}
?>
