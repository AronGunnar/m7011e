<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include('db_connection.php');

// Check if post_id is provided
if (!isset($_GET['post_id'])) {
    echo "Post ID is missing.";
    exit;
}

$post_id = $_GET['post_id'];

// Fetch post data to pre-fill the form
$sql_post = "SELECT * FROM Posts WHERE post_id = ?";
$stmt_post = $conn->prepare($sql_post);
$stmt_post->bind_param('i', $post_id);
$stmt_post->execute();
$result_post = $stmt_post->get_result();

// Check if post exists
if ($result_post->num_rows == 0) {
    echo "Post not found.";
    exit;
}

$post = $result_post->fetch_assoc();
$title = $post['title'];
$content = $post['content'];

// Fetch tags for this post
$sql_tags = "SELECT tag_id FROM Post_Tags WHERE post_id = ?";
$stmt_tags = $conn->prepare($sql_tags);
$stmt_tags->bind_param('i', $post_id);
$stmt_tags->execute();
$result_tags = $stmt_tags->get_result();
$selected_tags = [];
while ($tag = $result_tags->fetch_assoc()) {
    $selected_tags[] = $tag['tag_id']; // Store selected tag IDs
}

// Fetch all available tags
$sql_all_tags = "SELECT * FROM Tags";
$result_all_tags = $conn->query($sql_all_tags);

// Handle form submission for updating the post
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_post'])) {
    $new_title = $_POST['title'];
    $new_content = $_POST['content'];
    $new_selected_tags = $_POST['tags'] ?? [];

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

        echo "Post updated successfully!";
    } else {
        echo "Error updating post: " . $stmt_update->error;
    }
}

// Check if user is an admin or editor
$user_role = $_SESSION['role']; // Assuming user role is stored in session
$is_admin_or_editor = ($user_role == 'admin' || $user_role == 'editor');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .form-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-container input, .form-container textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-container label {
            font-weight: bold;
        }
        .tags-container {
            margin-top: 10px;
        }
        .tags-container label {
            margin-right: 10px;
        }
        .selected-tags {
            margin-top: 10px;
            font-style: italic;
            color: #555;
        }
    </style>
</head>
<body>

<h1>Edit Post</h1>

<form method="POST" action="" class="form-container">
    <label for="title">Post Title</label>
    <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($title); ?>" required>

    <label for="content">Post Content</label>
    <textarea name="content" id="content" rows="6" required><?php echo htmlspecialchars($content); ?></textarea>

    <label for="tags">Select Tags for the Post</label>
    <div class="tags-container">
        <?php
        // Display checkboxes for each available tag
        if ($result_all_tags->num_rows > 0) {
            while ($tag = $result_all_tags->fetch_assoc()) {
                $checked = in_array($tag['tag_id'], $selected_tags) ? 'checked' : '';
                echo '<label><input type="checkbox" name="tags[]" value="' . $tag['tag_id'] . '" ' . $checked . '>' . htmlspecialchars($tag['tag_name']) . '</label>';
            }
        } else {
            echo "<p>No tags available.</p>";
        }
        ?>
    </div>
    
    <button type="submit" name="update_post">Update Post</button>
</form>

<!-- Show delete button if the user is an admin or editor -->
<?php
if ($is_admin_or_editor) {
    echo '<form method="POST" action="delete_post.php" onsubmit="return confirm(\'Are you sure you want to delete this post?\')">';
    echo '<input type="hidden" name="post_id" value="' . $post_id . '">';
    echo '<button type="submit" name="delete_post">Delete Post</button>';
    echo '</form>';
}
?>

<script>
// JavaScript to update the selected tags list dynamically
const tagCheckboxes = document.querySelectorAll('input[name="tags[]"]');
const selectedTagsList = document.getElementById('selected-tags-list');

// Function to update the selected tags display
function updateSelectedTags() {
    const selectedTags = [];
    tagCheckboxes.forEach(checkbox => {
        if (checkbox.checked) {
            selectedTags.push(checkbox.nextElementSibling.textContent.trim());
        }
    });
    selectedTagsList.textContent = selectedTags.length ? selectedTags.join(', ') : 'None';
}

// Event listener to update the tags list when checkboxes are clicked
tagCheckboxes.forEach(checkbox => {
    checkbox.addEventListener('change', updateSelectedTags);
});

// Initial call to display selected tags
updateSelectedTags();
</script>

</body>
</html>
