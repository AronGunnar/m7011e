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
?>
