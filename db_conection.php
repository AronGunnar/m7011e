<?php
// Database connection details
$servername = "fuxcp.h.filess.io";  // MySQL host
$username = "m7011e_recalltea";    // MySQL username
$password = "db08c1af076bb0f865adc178705b4ed4c8318e4c";  // MySQL password
$dbname = "m7011e_recalltea";      // Database name

// Create a connection to the MySQL database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>