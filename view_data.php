<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login_signup.php'); // Redirect to login/signup page
    exit;
}

// Include the database connection
include('db_connection.php');

// Fetch all data from the Users table
$sql_users = "SELECT * FROM Users";
$result_users = $conn->query($sql_users);

// Fetch all data from the Profiles table
$sql_profiles = "SELECT * FROM Profiles";
$result_profiles = $conn->query($sql_profiles);

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
    <title>View All User Data</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <?php displayUserSession(); ?>
    <div style="position: fixed; top: 10px; left: 10px;">
        <a href="index.php" style="text-decoration: none; color: black; font-weight: bold;">Home</a>
    </div>
    
    <h1>All Data in Users and Profiles Tables</h1>

    <!-- Display all Users -->
    <h2>Users Table</h2>
    <table>
        <thead>
            <tr>
                <th>User ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Password (Hashed)</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $result_users->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $user['user_id']; ?></td>
                    <td><?php echo $user['username']; ?></td>
                    <td><?php echo $user['email']; ?></td>
                    <td><?php echo $user['password']; ?></td>
                    <td><?php echo $user['role']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Display all Profiles -->
    <h2>Profiles Table</h2>
    <table>
        <thead>
            <tr>
                <th>Profile ID</th>
                <th>User ID</th>
                <th>Bio</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($profile = $result_profiles->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $profile['profile_id']; ?></td>
                    <td><?php echo $profile['user_id']; ?></td>
                    <td><?php echo $profile['bio']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
