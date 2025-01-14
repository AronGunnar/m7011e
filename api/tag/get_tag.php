<?php
// Include database connection
include('../../db_connection.php');

// Fetch all tags from the database
$sql_fetch_tags = "SELECT tag_id, tag_name FROM Tags";
$result_tags = $conn->query($sql_fetch_tags);

// Check if tags are found
if ($result_tags->num_rows > 0) {
    $tags = [];
    while ($row = $result_tags->fetch_assoc()) {
        $tags[] = $row;
    }
    echo json_encode(['success' => 'Tags fetched successfully', 'data' => $tags]);
} else {
    echo json_encode(['error' => 'No tags found']);
}
?>
