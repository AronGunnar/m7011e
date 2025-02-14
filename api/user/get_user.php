<?php
// Include necessary files
include('../../db_connection.php');
include('../auth.php'); // Use auth.php for JWT functions

// Set content type to JSON
header('Content-Type: application/json');

// Validate the JWT token
$user = validate_jwt();

// If token is invalid or expired
if (!$user) {
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'Invalid or expired token']);
    exit;
}

// Get user ID from the decoded JWT token
$user_id = $user->user_id;

// Fetch user details from the database
$sql_user = "SELECT username, email, role FROM Users WHERE user_id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param('i', $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows > 0) {
    $user_data = $result_user->fetch_assoc();
    http_response_code(200); // OK
    echo json_encode(['success' => 'User profile fetched successfully', 'data' => $user_data]);
} else {
    http_response_code(404); // Not Found
    echo json_encode(['error' => 'User not found']);
}
?>
