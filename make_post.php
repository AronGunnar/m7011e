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
        .message {
            margin-top: 20px;
            font-weight: bold;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
    </style>
</head>
<body>

<h1>Create a New Post</h1>

<form id="create-post-form" class="form-container">
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

<div id="message-container" class="message"></div>

<script>
// JavaScript to handle form submission via AJAX
document.getElementById('create-post-form').addEventListener('submit', async function (event) {
    event.preventDefault(); // Prevent the form from submitting the traditional way

    const title = document.getElementById('title').value;
    const content = document.getElementById('content').value;
    const tags = Array.from(document.querySelectorAll('input[name="tags[]"]:checked')).map(checkbox => checkbox.value);

    const formData = new FormData();
    formData.append('title', title);
    formData.append('content', content);
    formData.append('tags', JSON.stringify(tags)); // Convert tags array to JSON

    try {
        const response = await fetch('api/post/create_post.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        const messageContainer = document.getElementById('message-container');
        if (result.success) {
            messageContainer.innerHTML = `<span class="success">${result.message}</span>`;
            // Optionally, reset the form after success
            document.getElementById('create-post-form').reset();
        } else {
            messageContainer.innerHTML = `<span class="error">${result.message}</span>`;
        }
    } catch (error) {
        console.error('Error creating post:', error);
        document.getElementById('message-container').innerHTML = `<span class="error">Error creating post. Please try again later.</span>`;
    }
});
</script>

</body>
</html>
