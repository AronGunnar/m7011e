<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include('../../db_connection.php');

// Check if required POST parameters are provided
if (isset($_POST['email'], $_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user details by email
    $sql_fetch_user = "SELECT user_id, username, password, role FROM Users WHERE email = ?";
    $stmt = $conn->prepare($sql_fetch_user);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify password using password_verify
        if (password_verify($password, $user['password'])) {
            // Set session variables upon successful login
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Include user details in the response (this is the missing part)
            echo json_encode([
                'success' => 'Login successful',
                'user_id' => $user['user_id'],
                'username' => $user['username'],
                'role' => $user['role']
            ]);
        } else {
            echo json_encode(['error' => 'Invalid password']);
        }
    } else {
        echo json_encode(['error' => 'User not found']);
    }
} else {
    echo json_encode(['error' => 'Missing required parameters']);
}
?>
