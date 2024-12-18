<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include('db_connection.php');

// Fetch all posts
$sql = "SELECT posts.*, users.username FROM Posts posts INNER JOIN Users users ON posts.user_id = users.user_id";
$result = $conn->query($sql);

// Function to display the "action" buttons (edit and delete)
function displayPostActions($post_id, $user_id, $current_user_role) {
    // Check if the current user is the author of the post or if they are an admin/editor
    if (isset($_SESSION['user_id']) && ($_SESSION['user_id'] == $user_id || $current_user_role == 'admin' || $current_user_role == 'editor')) {
        echo '<a href="edit_post.php?post_id=' . $post_id . '">Edit</a> | ';
        echo '<a href="delete_post.php?post_id=' . $post_id . '" onclick="return confirm(\'Are you sure you want to delete this post?\')">Delete</a>';
    }
}

// Function to format the timestamp
function formatTimestamp($timestamp) {
    return date("F j, Y, g:i a", strtotime($timestamp)); // Format: Month Day, Year, Hour:Minute am/pm
}

// Function to fetch the tags for a post
function getPostTags($post_id, $conn) {
    $sql_tags = "SELECT tags.tag_name FROM Post_Tags pt JOIN Tags tags ON pt.tag_id = tags.tag_id WHERE pt.post_id = ?";
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Posts</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }
        .post {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ccc;
        }
        .post-title {
            font-size: 1.5em;
            font-weight: bold;
        }
        .post-content {
            margin-top: 10px;
        }
        .post-author, .post-timestamp {
            font-size: 0.9em;
            color: gray;
        }
        .action-row {
            margin-top: 10px;
        }
        .tags {
            margin-top: 10px;
            font-style: italic;
            color: #555;
        }
        .link-button {
            text-decoration: none;
            color: black;
        }
    </style>
</head>
<body>
    <h1>View All Posts</h1>

    <?php
    // Fetch the user's role (admin/editor)
    $user_role = $_SESSION['role'] ?? ''; // Assuming role is stored in session

    // Check if there are any posts
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $post_id = $row['post_id'];
            $title = $row['title'];
            $content = $row['content'];
            $username = $row['username'];
            $user_id = $row['user_id'];
            $timestamp = $row['created_at']; // Assuming 'created_at' is the column for timestamp

            // Get tags for this post
            $tags = getPostTags($post_id, $conn);

            // Display each post
            echo '<div class="post">';
            echo '<div class="post-title">' . htmlspecialchars($title) . '</div>';
            echo '<div class="post-content">' . nl2br(htmlspecialchars($content)) . '</div>';
            echo '<div class="post-author">Posted by: ' . htmlspecialchars($username) . '</div>';
            echo '<div class="post-timestamp">Posted on: ' . formatTimestamp($timestamp) . '</div>';

            // Display tags for the post
            if (!empty($tags)) {
                echo '<div class="tags"><strong>Tags:</strong> ' . implode(', ', $tags) . '</div>';
            }

            // Show the action row (edit/delete) only if the user is logged in and is the post owner, admin, or editor
            if (isset($_SESSION['user_id']) && ($_SESSION['user_id'] == $user_id || $user_role == 'admin' || $user_role == 'editor')) {
                echo '<div class="action-row">';
                displayPostActions($post_id, $user_id, $user_role);
                echo '</div>';
            }

            echo '</div>';
        }
    } else {
        echo "<p>No posts available.</p>";
    }
    ?>

    <p><a href="index.php" class="link-button">Back to Home</a></p>
</body>
</html>
