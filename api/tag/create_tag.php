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
    echo json_encode(['error' => 'Only admins can create tags']);
    exit;
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if tag_name is provided
    $data = json_decode(file_get_contents('php://input'), true); // Decode the incoming JSON data

    if (isset($data['tag_name'])) {
        $tag_name = $data['tag_name'];

        // Validate tag name
        if (empty($tag_name)) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Tag name is required']);
            exit;
        }

        // Insert the tag into the database
        $sql_insert_tag = "INSERT INTO Tags (tag_name) VALUES (?)";
        $stmt_insert_tag = $conn->prepare($sql_insert_tag);
        $stmt_insert_tag->bind_param('s', $tag_name);

        if ($stmt_insert_tag->execute()) {
            http_response_code(201); // Created
            echo json_encode(['success' => 'Tag created successfully']);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => 'Failed to create tag']);
        }
    } else {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Tag name parameter missing']);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Incorrect request method, expected POST']);
}
?>
