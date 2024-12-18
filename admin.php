<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php'); // Redirect to login page if not logged in or not an admin
    exit;
}

// Database connection
include('db_connection.php');

// Fetch all users
$sql = "SELECT user_id, username, email, role FROM Users";
$result = $conn->query($sql);

// Handle role change for a user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_role'])) {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['role'];

    $sql_update_role = "UPDATE Users SET role = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql_update_role);
    $stmt->bind_param('si', $new_role, $user_id);

    if ($stmt->execute()) {
        echo "Role updated successfully!";
    } else {
        echo "Error updating role: " . $stmt->error;
    }
}

// Handle user deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];

    // Delete user and their associated profile and posts (using CASCADE for foreign keys)
    $sql_delete_user = "DELETE FROM Users WHERE user_id = ?";
    $stmt_delete = $conn->prepare($sql_delete_user);
    $stmt_delete->bind_param('i', $user_id);

    if ($stmt_delete->execute()) {
        echo "User deleted successfully!";
    } else {
        echo "Error deleting user: " . $stmt_delete->error;
    }
}

// Handle tag creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_tag'])) {
    $tag_name = $_POST['tag_name'];

    // Insert new tag into the Tags table
    $sql_create_tag = "INSERT INTO Tags (tag_name) VALUES (?)";
    $stmt_create_tag = $conn->prepare($sql_create_tag);
    $stmt_create_tag->bind_param('s', $tag_name);

    if ($stmt_create_tag->execute()) {
        echo "Tag created successfully!";
    } else {
        echo "Error creating tag: " . $stmt_create_tag->error;
    }
}

// Handle tag deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_tag'])) {
    $tag_id = $_POST['tag_id'];

    // Delete tag
    $sql_delete_tag = "DELETE FROM Tags WHERE tag_id = ?";
    $stmt_delete_tag = $conn->prepare($sql_delete_tag);
    $stmt_delete_tag->bind_param('i', $tag_id);

    if ($stmt_delete_tag->execute()) {
        echo "Tag deleted successfully!";
    } else {
        echo "Error deleting tag: " . $stmt_delete_tag->error;
    }
}

// Fetch all tags
$sql_tags = "SELECT * FROM Tags";
$result_tags = $conn->query($sql_tags);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
</head>
<body>
    <h1>Welcome to the Admin Page</h1>

    <h2>Manage Users</h2>
    <!-- Table to list all users -->
    <table border="1">
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td>
                        <form method="POST" action="" style="display: inline;">
                            <select name="role">
                                <option value="user" <?php echo $row['role'] == 'user' ? 'selected' : ''; ?>>User</option>
                                <option value="editor" <?php echo $row['role'] == 'editor' ? 'selected' : ''; ?>>Editor</option>
                                <option value="admin" <?php echo $row['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                            </select>
                            <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                            <button type="submit" name="change_role">Change Role</button>
                        </form>
                    </td>
                    <td>
                        <form method="POST" action="" style="display: inline;">
                            <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                            <button type="submit" name="delete_user" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <hr>

    <h2>Manage Tags</h2>

    <!-- Form to create a new tag -->
    <form method="POST" action="">
        <label for="tag_name">Create Tag:</label>
        <input type="text" id="tag_name" name="tag_name" required>
        <button type="submit" name="create_tag">Create Tag</button>
    </form>

    <hr>

    <!-- Display existing tags -->
    <h3>Existing Tags</h3>
    <table border="1">
        <thead>
            <tr>
                <th>Tag Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row_tag = $result_tags->fetch_assoc()) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($row_tag['tag_name']); ?></td>
                    <td>
                        <form method="POST" action="" style="display: inline;">
                            <input type="hidden" name="tag_id" value="<?php echo $row_tag['tag_id']; ?>">
                            <button type="submit" name="delete_tag" onclick="return confirm('Are you sure you want to delete this tag?')">Delete Tag</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <hr>
    <a href="view_all_data.php" style="text-decoration: none; color: black; font-size: 16px;">View All Data</a>
    <p><a href="dashboard.php">Back to Dashboard</a></p>

</body>
</html>
