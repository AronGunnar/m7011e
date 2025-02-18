<?php
// Include necessary files
include('../../db_connection.php');
include('../auth.php');

header('Content-Type: application/json');

// Validate JWT token
$user = validate_jwt();
if (!$user) {
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'Unauthorized: Invalid or expired token']);
    exit;
}

// Authorization check
if ($user->role !== 'admin') {
    http_response_code(403); // Forbidden
    echo json_encode(['error' => 'Only admins can delete users']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Ensure user_id is provided
    if (!isset($data['user_id']) || empty($data['user_id'])) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Missing user_id parameter']);
        exit;
    }

    $user_id_to_delete = intval($data['user_id']);

    // Check is the user exists
    $sql_check_user = "SELECT user_id FROM Users WHERE user_id = ?";
    $stmt_check_user = $conn->prepare($sql_check_user);
    $stmt_check_user->bind_param('i', $user_id_to_delete);
    $stmt_check_user->execute();
    $result_check_user = $stmt_check_user->get_result();

    if ($result_check_user->num_rows === 0) {
        http_response_code(404); // Not Found
        echo json_encode(['error' => 'User not found']);
        exit;
    }

    //  delete user
    $sql_delete_user = "DELETE FROM Users WHERE user_id = ?";
    $stmt_delete = $conn->prepare($sql_delete_user);
    $stmt_delete->bind_param('i', $user_id_to_delete);

    if ($stmt_delete->execute()) {
        http_response_code(200); // OK
        echo json_encode(['success' => 'User deleted successfully']);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'Error deleting user']);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Incorrect request method, expected DELETE']);
}
?>
