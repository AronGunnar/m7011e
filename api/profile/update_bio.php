<?php
// Include necessary files
include('../../db_connection.php');
include('../auth.php'); // Use auth.php for JWT functions

// Validate the JWT token
$user = validate_jwt();

// If token is invalid or expired
if (!$user) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized: Invalid or expired token']);
    exit;
}

// Get user ID from the decoded JWT token
$user_id = $user->user_id;

// Read the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

// Check if bio is provided in the request body
if (!isset($data['bio'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Bio parameter missing']);
    exit;
}

$bio = $data['bio'];

// Update bio in the Profiles table
$sql_update_bio = "UPDATE Profiles SET bio = ? WHERE user_id = ?";
$stmt_update_bio = $conn->prepare($sql_update_bio);
$stmt_update_bio->bind_param('si', $bio, $user_id);

if ($stmt_update_bio->execute()) {
    echo json_encode(['success' => 'Bio updated successfully']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to update bio']);
}
?>
