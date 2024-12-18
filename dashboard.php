<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
}

// Database connection
include('db_connection.php');

// Fetch current user's profile
$user_id = $_SESSION['user_id'];

// Handle form submission for updating bio
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_bio'])) {
    $bio = $_POST['bio'];
    $sql_update_bio = "UPDATE Profiles SET bio = ? WHERE user_id = ?";
    $stmt_update = $conn->prepare($sql_update_bio);
    $stmt_update->bind_param('si', $bio, $user_id);

    if ($stmt_update->execute()) {
        echo "<p class='message success'>Bio updated successfully!</p>";
    } else {
        echo "<p class='message error'>Error updating bio: " . $stmt_update->error . "</p>";
    }
}

// Fetch the current bio
$sql_fetch_bio = "SELECT bio FROM Profiles WHERE user_id = ?";
$stmt_fetch = $conn->prepare($sql_fetch_bio);
$stmt_fetch->bind_param('i', $user_id);
$stmt_fetch->execute();
$result = $stmt_fetch->get_result();
$profile = $result->fetch_assoc();
$bio = $profile['bio'] ?? '';

// Fetch the current user's details (from Users table)
$sql_fetch_user = "SELECT * FROM Users WHERE user_id = ?";
$stmt_fetch_user = $conn->prepare($sql_fetch_user);
$stmt_fetch_user->bind_param('i', $user_id);
$stmt_fetch_user->execute();
$result_user = $stmt_fetch_user->get_result();
$user = $result_user->fetch_assoc();

// Display user session information with a plain text "Dashboard" link
function displayUserSession() {
    if (isset($_SESSION['username'])) {
        echo '<div class="session-info">';
        echo '<p>Logged in as: ' . htmlspecialchars($_SESSION['username']) . '</p>';
        echo '<a href="dashboard.php" class="dashboard-link">Dashboard</a>';
        echo '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <style>
        /* General Styles */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7f6;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }

        .container {
            width: 80%;
            max-width: 900px;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
        }

        h1, h2 {
            text-align: center;
            color: #4CAF50;
        }

        .message {
            text-align: center;
            margin-top: 10px;
            font-size: 1em;
        }

        .message.success {
            color: green;
        }

        .message.error {
            color: red;
        }

        /* Form Styles */
        textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1em;
            resize: vertical;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            font-size: 1.1em;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        /* Links Styling */
        a {
            text-decoration: none;
            color: #4CAF50;
            font-weight: bold;
            display: block;
            text-align: center;
            margin: 15px 0;
        }

        a:hover {
            color: #45a049;
            text-decoration: underline;
        }

        .session-info {
            position: fixed;
            top: 10px;
            right: 10px;
            text-align: right;
            font-size: 0.9em;
            background-color: #f4f7f6;
            padding: 10px;
            border-radius: 4px;
        }

        .dashboard-link {
            text-decoration: none;
            color: black;
            font-weight: bold;
        }

        .dashboard-link:hover {
            text-decoration: underline;
        }

        /* Admin Button */
        .admin-link {
            font-size: 1.1em;
            font-weight: bold;
            text-align: center;
            margin-top: 20px;
            display: block;
            color: black;
        }

        .admin-link:hover {
            color: #4CAF50;
        }

        .back-home {
            font-size: 16px;
            color: #4CAF50;
            display: inline-block;
            margin-top: 10px;
            text-align: left;
        }

        .back-home:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="container">
        <?php displayUserSession(); ?>

        <a href="index.php" class="back-home">Back to Home</a>

        <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
        <h2>Your Profile</h2>

        <!-- Form to update bio -->
        <form method="POST" action="">
            <textarea name="bio" placeholder="Enter your bio"><?php echo $bio; ?></textarea><br>
            <button type="submit" name="update_bio">Update Bio</button>
        </form>

        <!-- Display success or error message for bio update -->
        <?php if (isset($message)) { echo $message; } ?>

        <hr>

        <h2>Your Information</h2>
        <p><strong>Username:</strong> <?php echo $user['username']; ?></p>
        <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
        <p><strong>Role:</strong> <?php echo $user['role']; ?></p>
        <p><strong>Bio:</strong> <?php echo $bio; ?></p>

        <a href="make_post.php">Make a Post</a> <!-- Link to create a new post -->
        <a href="view_posts.php">View All Posts</a> <!-- Link to view posts -->

        <hr>

        <!-- Admin button (only visible to admins) -->
        <?php if ($user['role'] == 'admin') : ?>
            <a href="admin.php" class="admin-link">Admin Page</a>
        <?php endif; ?>

        <hr>

        <h2>Logout</h2>
        <a href="logout.php">Logout</a> <!-- Logout button -->
    </div>

</body>
</html>
