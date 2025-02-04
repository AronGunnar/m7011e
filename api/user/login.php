<?php
// Include necessary files
include('../../db_connection.php');
require_once '../../vendor/autoload.php'; // Ensure the path to the Composer autoload file is correct
include('../auth.php');  // Include auth.php for JWT generation

// Check if the form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user input (email and password)
    $email = $_POST['email'];  // Use email instead of username
    $password = $_POST['password']; // Get password from form

    // Check if email and password are provided
    if (empty($email) || empty($password)) {
        echo json_encode(['error' => 'Please fill in both fields.']);
        exit;
    }

    // Prepare the query with positional placeholders
    $query = "SELECT * FROM Users WHERE email = ?";
    $stmt = $conn->prepare($query);

    // Bind the email parameter to the query
    $stmt->bind_param('s', $email);  // 's' means the parameter is a string

    // Execute the query
    $stmt->execute();

    // Get the result of the query
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // If the user does not exist or the password is incorrect
    if (!$user || !password_verify($password, $user['password'])) {
        echo json_encode(['error' => 'Login failed. Please try again.']);
        exit;
    }

    // User is authenticated, generate a JWT token using the function from auth.php
    $jwt = generate_jwt($user['user_id'], $user['email'], $user['role']);

    // Return the JWT token as a JSON response
    echo json_encode(['success' => true, 'token' => $jwt]);
}
?>
