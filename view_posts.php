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

    <div id="posts-container">
        <!-- Posts will be dynamically inserted here using JavaScript -->
    </div>

    <p><a href="index.php" class="link-button">Back to Home</a></p>

    <script>
        // Fetch posts from the API
        async function fetchPosts() {
            try {
                const response = await fetch('api/post/get_posts.php');
                const data = await response.json();

                // Check if the request was successful
                if (data.success) {
                    const postsContainer = document.getElementById('posts-container');
                    postsContainer.innerHTML = ''; // Clear any existing content

                    // Iterate over the posts and create HTML elements for each post
                    data.posts.forEach(post => {
                        const postElement = document.createElement('div');
                        postElement.classList.add('post');

                        postElement.innerHTML = `
                            <div class="post-title">${post.title}</div>
                            <div class="post-content">${post.content}</div>
                            <div class="post-author">Posted by: ${post.username}</div>
                            <div class="post-timestamp">Posted on: ${new Date(post.created_at).toLocaleString()}</div>
                            ${post.tags.length > 0 ? '<div class="tags"><strong>Tags:</strong> ' + post.tags.join(', ') + '</div>' : ''}
                            ${post.current_user_can_edit || post.current_user_can_delete ? `
                                <div class="action-row">
                                    ${post.current_user_can_edit ? `<a href="edit_post.php?post_id=${post.post_id}">Edit</a>` : ''}
                                    ${post.current_user_can_delete ? ` | <a href="delete_post.php?post_id=${post.post_id}" onclick="return confirm('Are you sure you want to delete this post?')">Delete</a>` : ''}
                                </div>
                            ` : ''}
                        `;
                        
                        postsContainer.appendChild(postElement);
                    });
                } else {
                    document.getElementById('posts-container').innerHTML = '<p>No posts available.</p>';
                }
            } catch (error) {
                console.error('Error fetching posts:', error);
                document.getElementById('posts-container').innerHTML = '<p>Error fetching posts.</p>';
            }
        }

        // Call the fetchPosts function when the page loads
        fetchPosts();
    </script>
</body>
</html>
