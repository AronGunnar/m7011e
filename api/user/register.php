<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include('../../db_connection.php');

// Default role for new users
$default_role = 'user';

// Check if required POST parameters are provided
if (isset($_POST['username'], $_POST['password'], $_POST['email'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $role = $default_role; // Default role is 'user'

    // Check if the username already exists
    $sql_check = "SELECT user_id FROM Users WHERE username = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param('s', $username);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['error' => 'Username already exists']);
        exit;
    }

    // Hash the password securely
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into the Users table
    $sql_insert = "INSERT INTO Users (username, password, email, role) VALUES (?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param('ssss', $username, $hashed_password, $email, $role);

    if ($stmt_insert->execute()) {
        http_response_code(201); // Success
        echo json_encode(['success' => 'User registered successfully']);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'Error registering user']);
    }
} else {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Missing required parameters']);
}
?>
