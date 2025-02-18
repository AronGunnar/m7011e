<?php
// Include necessary files
include('../../db_connection.php');
include('../auth.php');

// Validate the JWT token
$user = validate_jwt();

// If token is invalid or expired
if (!$user) {
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'Unauthorized: Invalid or expired token']);
    exit;
}

$user_id = $user->user_id;


$data = json_decode(file_get_contents('php://input'), true);

// Check if bio is provided in the request body
if (!isset($data['bio'])) {
    http_response_code(400); // Bad Request
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
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Failed to update bio']);
}
?>
