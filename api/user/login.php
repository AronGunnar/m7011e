<?php
// Include necessary files
include('../../db_connection.php');
require_once '../../vendor/autoload.php';
include('../auth.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user input (email and password)
    $email = $_POST['email'];
    $password = $_POST['password'];

    // error
    if (empty($email) || empty($password)) {
        echo json_encode(['error' => 'Please fill in both fields.']);
        exit;
    }

    $query = "SELECT * FROM Users WHERE email = ?";
    $stmt = $conn->prepare($query);

    $stmt->bind_param('s', $email);

    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // whoops
    if (!$user || !password_verify($password, $user['password'])) {
        echo json_encode(['error' => 'Login failed. Please try again.']);
        exit;
    }

    $jwt = generate_jwt($user['user_id'], $user['email'], $user['role']);

    // Return the JWT token as JSON
    echo json_encode(['success' => true, 'token' => $jwt]);
}
?>
