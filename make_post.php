<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include('db_connection.php');

// Fetch all available tags
$sql_tags = "SELECT * FROM Tags";
$result_tags = $conn->query($sql_tags);

// Handle form submission for creating a new post
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_post'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $selected_tags = $_POST['tags'] ?? []; // Default to empty array if no tags are selected

    // Insert the new post into the Posts table
    $sql_insert_post = "INSERT INTO Posts (user_id, title, content) VALUES (?, ?, ?)";
    $stmt_post = $conn->prepare($sql_insert_post);
    $stmt_post->bind_param('iss', $_SESSION['user_id'], $title, $content);
    
    if ($stmt_post->execute()) {
        $post_id = $stmt_post->insert_id;

        // Insert the selected tags into the Post_Tags table
        foreach ($selected_tags as $tag_id) {
            $sql_insert_tag = "INSERT INTO Post_Tags (post_id, tag_id) VALUES (?, ?)";
            $stmt_tag = $conn->prepare($sql_insert_tag);
            $stmt_tag->bind_param('ii', $post_id, $tag_id);
            $stmt_tag->execute();
        }

        echo "Post created successfully!";
    } else {
        echo "Error creating post: " . $stmt_post->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create a Post</title>
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

<h1>Create a New Post</h1>

<form method="POST" action="" class="form-container">
    <label for="title">Post Title</label>
    <input type="text" name="title" id="title" required>

    <label for="content">Post Content</label>
    <textarea name="content" id="content" rows="6" required></textarea>

    <label for="tags">Select Tags for the Post</label>
    <div class="tags-container">
        <?php
        // Display checkboxes for each available tag
        if ($result_tags->num_rows > 0) {
            while ($tag = $result_tags->fetch_assoc()) {
                echo '<label><input type="checkbox" name="tags[]" value="' . $tag['tag_id'] . '">' . htmlspecialchars($tag['tag_name']) . '</label>';
            }
        } else {
            echo "<p>No tags available.</p>";
        }
        ?>
    </div>

    <button type="submit" name="submit_post">Submit Post</button>
</form>

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
