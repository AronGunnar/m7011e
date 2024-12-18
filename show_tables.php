<?php
// Include the database connection file
include('db_connection.php');

// SQL query to fetch all tables
$sql = "SHOW TABLES";
$result = $conn->query($sql);

// Check if the query returns results
if ($result->num_rows > 0) {
    echo "<h1>Tables in the Database:</h1>";
    echo "<ul>";
    // Output each table name
    while ($row = $result->fetch_assoc()) {
        echo "<li>" . $row["Tables_in_" . $dbname] . "</li>";
    }
    echo "</ul>";
} else {
    echo "No tables found in the database.";
}

// Close the database connection
$conn->close();

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
