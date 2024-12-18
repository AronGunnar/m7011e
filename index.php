<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Display user session information with a plain text "Dashboard" link
function displayUserSession() {
    if (isset($_SESSION['username'])) {
        echo '<div style="position: fixed; top: 10px; right: 10px; text-align: right;">';
        echo '<p style="margin: 0;">Logged in as: ' . htmlspecialchars($_SESSION['username']) . '</p>';
        echo '<a href="dashboard.php" style="text-decoration: none; color: black;">Dashboard</a>';
        echo '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to My Post Management System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        h1 {
            color: #333;
        }
        .container {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        .link-button {
            text-decoration: none;
            color: #4CAF50;
            font-weight: bold;
        }
        .link-button:hover {
            text-decoration: underline;
        }
        .section {
            margin-bottom: 30px;
        }
        .nav-link {
            display: block;
            font-size: 18px;
            margin-bottom: 10px;
        }
        .description {
            font-size: 16px;
            margin-top: 5px;
            color: #555;
        }
        .home-link {
            position: fixed;
            top: 10px;
            left: 10px;
            font-size: 18px;
            font-weight: bold;
            text-decoration: none;
            color: black;
        }
    </style>
</head>
<body>
    <?php displayUserSession(); ?>
    <div class="home-link">
        <a href="index.php" style="text-decoration: none; color: black;">Home</a>
    </div>    

    <div class="container">
        <h1>Welcome to My Post Management System</h1>
        <p>Manage your posts, tags, and user roles easily with this platform. Whether you're an admin, editor, or a regular user, you can interact with the site in different ways.</p>

        <div class="section">
            <h2>Manage Posts</h2>
            <p class="description">Create, view, edit, and delete posts. Admins and editors can manage all posts, while users can only manage their own posts.</p>
            <a href="view_posts.php" class="nav-link">View All Posts</a>
            <a href="make_post.php" class="nav-link">Create a New Post</a>
        </div>

        <div class="section">
            <h2>Manage Tags</h2>
            <p class="description">Add or remove tags from your posts to make them easily searchable. Admins can create and delete tags.</p>
            <a href="view_all_data.php" class="nav-link">View All Tags</a>
        </div>

        <div class="section">
            <h2>User Roles and Permissions</h2>
            <p class="description">Admins and editors have special permissions to edit and delete posts from other users. Regular users can only manage their own posts.</p>
        </div>

        <div class="section">
            <h2>Login / Sign Up</h2>
            <p class="description">Log in or sign up to start managing posts and tags. Admins and editors can perform additional tasks.</p>
            <a href="login.php" class="nav-link">Login / Sign Up</a>
        </div>
    </div>

</body>
</html>
