<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include('db_connection.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
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
    <div class="tags-container" id="tags-container">
        <!-- Tags will be dynamically populated here via JavaScript -->
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

// JavaScript to fetch and display tags from the get_tag.php API
async function fetchTags() {
    try {
        const response = await fetch('api/tag/get_tag.php');
        const data = await response.json();

        const tagsContainer = document.getElementById('tags-container');
        if (data.success) {
            data.data.forEach(tag => {
                const label = document.createElement('label');
                label.innerHTML = `
                    <input type="checkbox" name="tags[]" value="${tag.tag_id}">
                    ${tag.tag_name}
                `;
                tagsContainer.appendChild(label);
            });
        } else {
            tagsContainer.innerHTML = '<p>No tags available.</p>';
        }
    } catch (error) {
        console.error('Error fetching tags:', error);
        document.getElementById('tags-container').innerHTML = '<p>Error fetching tags. Please try again later.</p>';
    }
}

// Call fetchTags to populate the tags when the page loads
fetchTags();
</script>

</body>
</html>
