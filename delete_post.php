<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include('db_connection.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch the current user role
$user_role = $_SESSION['role'] ?? '';
$current_user_id = $_SESSION['user_id'];

// Check if post_id is provided
if (isset($_GET['post_id'])) {
    $post_id = intval($_GET['post_id']); // Sanitize input

    // Fetch the post details (for display purposes)
    $sql_fetch_post = "SELECT title FROM Posts WHERE post_id = ?";
    $stmt = $conn->prepare($sql_fetch_post);
    $stmt->bind_param('i', $post_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $post = $result->fetch_assoc();
        $post_title = htmlspecialchars($post['title']);
    } else {
        echo "Post not found.";
        exit;
    }
} else {
    echo "No post ID provided.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Post</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Delete Post</h1>
    <p>Are you sure you want to delete the post titled "<strong><?php echo $post_title; ?></strong>"?</p>

    <button id="delete-post-btn">Delete Post</button>
    <p id="message"></p>

    <script>
    $(document).ready(function() {
        $('#delete-post-btn').on('click', function() {
            // Confirm the action
            if (confirm('Are you sure you want to delete this post?')) {
                // Send the AJAX request to delete the post
                $.ajax({
                    url: 'api/post/delete_post.php',
                    type: 'POST',
                    data: {
                        post_id: <?php echo $post_id; ?>
                    },
                    success: function(response) {
                        const data = JSON.parse(response);
                        if (data.success) {
                            $('#message').html('<span style="color: green;">' + data.success + '</span>');
                            setTimeout(function() {
                                window.location.href = 'view_posts.php';
                            }, 2000); // Redirect after 2 seconds
                        } else {
                            $('#message').html('<span style="color: red;">' + data.error + '</span>');
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#message').html('<span style="color: red;">Error: ' + error + '</span>');
                    }
                });
            }
        });
    });
    </script>
</body>
</html>
