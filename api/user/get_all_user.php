<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection with absolute path
include($_SERVER['DOCUMENT_ROOT'] . '/m7011e/db_connection.php'); // Update the path as needed

// Check if the database connection was successful
if (!$conn) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'Access denied: Admins only']);
    exit;
}

// SQL query to fetch all user data
$sql = "SELECT user_id, username, email, role FROM Users";
$result = $conn->query($sql);

// Check if there are users
if ($result->num_rows > 0) {
    // Create an array to hold all user data
    $users = [];

    // Fetch each user and add to the array
    while ($row = $result->fetch_assoc()) {
        $users[] = [
            'user_id' => $row['user_id'],
            'username' => $row['username'],
            'email' => $row['email'],
            'role' => $row['role']
        ];
    }

    // Return the user data as JSON
    echo json_encode(['success' => true, 'data' => $users]);
} else {
    // Return an error if no users are found
    echo json_encode(['success' => false, 'error' => 'No users found']);
}
?>
