<?php
// Include necessary files
include('../../db_connection.php');
include('../auth.php'); // Include auth.php for JWT validation

// Set response header to JSON
header('Content-Type: application/json');

try {
    // Validate the JWT token
    $user = validate_jwt();

    // If the token is invalid or expired
    if (!$user) {
        http_response_code(401); // Unauthorized
        echo json_encode(['error' => 'Access denied: Unauthorized']);
        exit;
    }

    // Check if the user has admin privileges
    if ($user->role !== 'admin') {
        http_response_code(403); // Forbidden
        echo json_encode(['error' => 'Access denied: Admins only']);
        exit;
    }

    // Fetch all user data from the database
    $sql = "SELECT user_id, username, email, role FROM Users";
    $result = $conn->query($sql);

    if ($result === false) {
        throw new Exception('Database query failed');
    }

    // Check if there are users
    if ($result->num_rows > 0) {
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = [
                'user_id' => $row['user_id'],
                'username' => $row['username'],
                'email' => $row['email'],
                'role' => $row['role']
            ];
        }

        // Return the user data as JSON
        http_response_code(200); // OK
        echo json_encode(['success' => true, 'data' => $users]);
    } else {
        http_response_code(404); // Not Found
        echo json_encode(['error' => 'No users found']);
    }
} catch (Exception $e) {
    // Handle any unexpected errors
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
}
?>
