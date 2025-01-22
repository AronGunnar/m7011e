<?php
// Set headers to return JSON response
header('Content-Type: application/json');

// Include database connection
include('../../db_connection.php');

try {
    // Prepare the SQL query to fetch all tags
    $sql_fetch_tags = "SELECT tag_id, tag_name FROM Tags";
    $stmt = $conn->prepare($sql_fetch_tags);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if tags are found
    if ($result->num_rows > 0) {
        $tags = [];
        while ($row = $result->fetch_assoc()) {
            $tags[] = $row;
        }

        // Respond with success and tag data
        echo json_encode([
            'success' => true,
            'message' => 'Tags fetched successfully.',
            'data' => $tags
        ]);
    } else {
        // No tags found, return a response
        echo json_encode([
            'success' => false,
            'message' => 'No tags found.',
            'data' => []
        ]);
    }
} catch (Exception $e) {
    // Handle any errors
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while fetching tags: ' . $e->getMessage(),
        'data' => []
    ]);
}
