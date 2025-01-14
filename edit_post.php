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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Styles for the form */
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
    </style>
</head>
<body>

<h1>Edit Post</h1>

<form id="edit-post-form" class="form-container">
    <label for="title">Post Title</label>
    <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($title); ?>" required>

    <label for="content">Post Content</label>
    <textarea name="content" id="content" rows="6" required><?php echo htmlspecialchars($content); ?></textarea>

    <label for="tags">Select Tags for the Post</label>
    <div class="tags-container" id="tags-container">
        <!-- Tags will be dynamically populated here via JavaScript -->
    </div>
    
    <button type="submit" id="update-post-btn">Update Post</button>
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
// JavaScript to handle form submission via AJAX
$(document).ready(function() {
    // Fetch tags and pre-select the ones already associated with the post
    fetchTags();

    $('#edit-post-form').on('submit', function(event) {
        event.preventDefault();

        // Gather form data
        var formData = {
            post_id: <?php echo $post_id; ?>,
            title: $('#title').val(),
            content: $('#content').val(),
            tags: $('input[name="tags[]"]:checked').map(function() {
                return this.value;
            }).get()
        };

        // Send the AJAX request to the edit post API
        $.ajax({
            url: 'api/post/edit_post.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                var data = JSON.parse(response);
                if (data.success) {
                    alert(data.success);
                    window.location.href = 'view_posts.php'; // Redirect after successful update
                } else {
                    alert(data.error);
                }
            },
            error: function(xhr, status, error) {
                alert('Error: ' + error);
            }
        });
    });
});

// Fetch tags from the API and pre-select the tags associated with the post
async function fetchTags() {
    try {
        const response = await fetch('api/tag/get_tag.php');
        const data = await response.json();

        const tagsContainer = document.getElementById('tags-container');
        if (data.success) {
            data.data.forEach(tag => {
                const label = document.createElement('label');
                const isChecked = <?php echo json_encode($selected_tags); ?>.includes(tag.tag_id) ? 'checked' : '';
                label.innerHTML = `
                    <input type="checkbox" name="tags[]" value="${tag.tag_id}" ${isChecked}>
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
</script>

</body>
</html>
