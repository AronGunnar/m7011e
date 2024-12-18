<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include('db_connection.php');

// Fetch data from Users table
$sql_users = "SELECT * FROM Users";
$result_users = $conn->query($sql_users);

// Fetch data from Profiles table
$sql_profiles = "SELECT * FROM Profiles";
$result_profiles = $conn->query($sql_profiles);

// Fetch data from Posts table
$sql_posts = "SELECT * FROM Posts";
$result_posts = $conn->query($sql_posts);

// Fetch data from Tags table
$sql_tags = "SELECT * FROM Tags";
$result_tags = $conn->query($sql_tags);

// Fetch data from Post_Tags table
$sql_post_tags = "SELECT * FROM Post_Tags";
$result_post_tags = $conn->query($sql_post_tags);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View All Data</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<h1>View All Data</h1>

<h2>Users Table</h2>
<table>
    <tr>
        <th>User ID</th>
        <th>Username</th>
        <th>Email</th>
        <th>Role</th>
    </tr>
    <?php
    if ($result_users->num_rows > 0) {
        while ($row = $result_users->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row['user_id'] . '</td>';
            echo '<td>' . htmlspecialchars($row['username']) . '</td>';
            echo '<td>' . htmlspecialchars($row['email']) . '</td>';
            echo '<td>' . htmlspecialchars($row['role']) . '</td>';
            echo '</tr>';
        }
    } else {
        echo "<tr><td colspan='4'>No data available</td></tr>";
    }
    ?>
</table>

<h2>Profiles Table</h2>
<table>
    <tr>
        <th>Profile ID</th>
        <th>User ID</th>
        <th>Bio</th>
    </tr>
    <?php
    if ($result_profiles->num_rows > 0) {
        while ($row = $result_profiles->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row['profile_id'] . '</td>';
            echo '<td>' . $row['user_id'] . '</td>';
            echo '<td>' . htmlspecialchars($row['bio']) . '</td>';
            echo '</tr>';
        }
    } else {
        echo "<tr><td colspan='3'>No data available</td></tr>";
    }
    ?>
</table>

<h2>Posts Table</h2>
<table>
    <tr>
        <th>Post ID</th>
        <th>User ID</th>
        <th>Title</th>
        <th>Content</th>
        <th>Created At</th>
    </tr>
    <?php
    if ($result_posts->num_rows > 0) {
        while ($row = $result_posts->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row['post_id'] . '</td>';
            echo '<td>' . $row['user_id'] . '</td>';
            echo '<td>' . htmlspecialchars($row['title']) . '</td>';
            echo '<td>' . htmlspecialchars($row['content']) . '</td>';
            echo '<td>' . $row['created_at'] . '</td>';
            echo '</tr>';
        }
    } else {
        echo "<tr><td colspan='5'>No data available</td></tr>";
    }
    ?>
</table>

<h2>Tags Table</h2>
<table>
    <tr>
        <th>Tag ID</th>
        <th>Tag Name</th>
    </tr>
    <?php
    if ($result_tags->num_rows > 0) {
        while ($row = $result_tags->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row['tag_id'] . '</td>';
            echo '<td>' . htmlspecialchars($row['tag_name']) . '</td>';
            echo '</tr>';
        }
    } else {
        echo "<tr><td colspan='2'>No data available</td></tr>";
    }
    ?>
</table>

<h2>Post_Tags Table</h2>
<table>
    <tr>
        <th>Post ID</th>
        <th>Tag ID</th>
    </tr>
    <?php
    if ($result_post_tags->num_rows > 0) {
        while ($row = $result_post_tags->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row['post_id'] . '</td>';
            echo '<td>' . $row['tag_id'] . '</td>';
            echo '</tr>';
        }
    } else {
        echo "<tr><td colspan='2'>No data available</td></tr>";
    }
    ?>
</table>

<p><a href="index.php" class="link-button">Back to Home</a></p>

</body>
</html>
