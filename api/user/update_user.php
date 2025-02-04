<?php
// Include necessary files
include('../../db_connection.php');
include('../auth.php'); // Include auth.php for JWT validation

// Set response header
header('Content-Type: application/json');

// Validate JWT token
$user = validate_jwt();
if (!$user) {
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'Unauthorized: Invalid or expired token']);
    exit;
}

// Check if the user has admin privileges
if ($user->role !== 'admin') {
    http_response_code(403); // Forbidden
    echo json_encode(['error' => 'Only admins can update user roles']);
    exit;
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if required parameters are provided
    $data = json_decode(file_get_contents('php://input'), true); // Decode the incoming JSON data

    if (isset($data['user_id'], $data['new_role'])) {
        $user_id_to_update = intval($data['user_id']); // Sanitize input
        $new_role = $data['new_role'];

        // Validate role
        $valid_roles = ['user', 'editor', 'admin'];
        if (!in_array($new_role, $valid_roles)) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Invalid role']);
            exit;
        }

        // Update user role in the database
        $sql_update_role = "UPDATE Users SET role = ? WHERE user_id = ?";
        $stmt_update_role = $conn->prepare($sql_update_role);
        $stmt_update_role->bind_param('si', $new_role, $user_id_to_update);

        if ($stmt_update_role->execute()) {
            http_response_code(200); // OK
            echo json_encode(['success' => 'User role updated successfully']);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => 'Error updating user role']);
        }
    } else {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Missing required parameters']);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Incorrect request method, expected POST']);
}
?>
