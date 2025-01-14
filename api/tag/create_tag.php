<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include('../../db_connection.php');

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

// Check if tag name is provided in the POST request
if (isset($_POST['tag_name'])) {
    $tag_name = $_POST['tag_name'];

    // Validate tag name (you can add more validation checks here)
    if (empty($tag_name)) {
        echo json_encode(['error' => 'Tag name is required']);
        exit;
    }

    // Insert the tag into the database
    $sql_insert_tag = "INSERT INTO Tags (tag_name) VALUES (?)";
    $stmt_insert_tag = $conn->prepare($sql_insert_tag);
    $stmt_insert_tag->bind_param('s', $tag_name);

    if ($stmt_insert_tag->execute()) {
        echo json_encode(['success' => 'Tag created successfully']);
    } else {
        echo json_encode(['error' => 'Failed to create tag']);
    }
} else {
    echo json_encode(['error' => 'Tag name parameter missing']);
}
?>
