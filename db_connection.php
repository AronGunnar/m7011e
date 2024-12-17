<?php
// Database connection details
$servername = "fuxcp.h.filess.io";  // Your MySQL host
$username = "m7011e_recalltea";    // Your MySQL username
$password = "db08c1af076bb0f865adc178705b4ed4c8318e4c";  // Your MySQL password
$dbname = "m7011e_recalltea";      // Your database name

// Create a connection to the MySQL database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
