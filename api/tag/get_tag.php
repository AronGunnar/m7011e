<?php
include('../../db_connection.php');

$sql = "SELECT tag_id, tag_name FROM Tags";
$result = $conn->query($sql);

$tags = [];
while ($row = $result->fetch_assoc()) {
    $tags[] = $row;
}

// Return tags as JSON
echo json_encode(['data' => $tags]);
?>
