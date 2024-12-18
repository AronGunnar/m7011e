<?php
// Include database connection
include('db_connection.php');

// Query to get all users
$sql = "SELECT * FROM Users";
$result = $conn->query($sql);

// Check if there are any users
if ($result->num_rows > 0) {
    echo "<h1>Users Data</h1>";
    echo "<table border='1'>
            <tr>
                <th>User ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row['user_id'] . "</td>
                <td>" . $row['username'] . "</td>
                <td>" . $row['email'] . "</td>
                <td>" . $row['role'] . "</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No users found!";
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

<p><a href="login.php">Go back to Login</a></p>
