<?php
// Set headers to return JSON response
header('Content-Type: application/json');

// Include database connection
include('../../db_connection.php');

// Get the input from the request (assuming JSON input)
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($input['tag_id']) || !isset($input['tag_name'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Tag ID and tag name are required.'
    ]);
    exit;
}

$tag_id = intval($input['tag_id']);
$tag_name = trim($input['tag_name']);

// Check if the tag name is empty after trimming
if (empty($tag_name)) {
    echo json_encode([
        'success' => false,
        'message' => 'Tag name cannot be empty.'
    ]);
    exit;
}

try {
    // Prepare the SQL query to update the tag
    $sql_update_tag = "UPDATE Tags SET tag_name = ? WHERE tag_id = ?";
    $stmt = $conn->prepare($sql_update_tag);
    $stmt->bind_param('si', $tag_name, $tag_id);

    if ($stmt->execute()) {
        // Check if any rows were affected
        if ($stmt->affected_rows > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Tag updated successfully.'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No changes made or tag not found.'
            ]);
        }
    } else {
        throw new Exception('Failed to execute the update query.');
    }
} catch (Exception $e) {
    // Handle unexpected errors
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while updating the tag: ' . $e->getMessage()
    ]);
}