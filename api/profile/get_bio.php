<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include('../../db_connection.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Fetch user bio from the Profiles table
$sql_bio = "SELECT bio FROM Profiles WHERE user_id = ?";
$stmt_bio = $conn->prepare($sql_bio);
$stmt_bio->bind_param('i', $user_id);
$stmt_bio->execute();
$result_bio = $stmt_bio->get_result();

if ($result_bio->num_rows > 0) {
    $profile = $result_bio->fetch_assoc();
    echo json_encode(['success' => 'Bio fetched successfully', 'data' => $profile]);
} else {
    echo json_encode(['error' => 'Bio not found']);
}
?>
