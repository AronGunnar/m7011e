<?php
// Database connection details (should probably be included as ignored file)
$servername = "fuxcp.h.filess.io";  // MySQL host
$username = "m7011e_recalltea";    // MySQL username
$password = "db08c1af076bb0f865adc178705b4ed4c8318e4c";  // MySQL password
$dbname = "m7011e_recalltea";      // database name

// Create connection to the MySQL database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
