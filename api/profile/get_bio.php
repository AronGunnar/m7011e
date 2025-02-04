<?php
// Include necessary files
include('../../db_connection.php');
include('../auth.php'); // Include auth.php for JWT validation

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

// Fetch user bio from the database
$sql_bio = "SELECT bio FROM Profiles WHERE user_id = ?";
$stmt_bio = $conn->prepare($sql_bio);
$stmt_bio->bind_param('i', $user_id);
$stmt_bio->execute();
$result_bio = $stmt_bio->get_result();

// Check if bio exists for the user
if ($result_bio->num_rows > 0) {
    $profile = $result_bio->fetch_assoc();
    http_response_code(200); // OK
    echo json_encode(['success' => 'Bio fetched successfully', 'data' => $profile]);
} else {
    http_response_code(404); // Not Found
    echo json_encode(['error' => 'Bio not found']);
}
?>
