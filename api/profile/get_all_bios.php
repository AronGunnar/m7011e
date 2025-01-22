<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include('../../db_connection.php');

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'Access denied: Admins only']);
    exit;
}

// SQL query to fetch all profiles
$sql_profiles = "
    SELECT 
        Profiles.profile_id, 
        Profiles.user_id, 
        Profiles.bio, 
        Users.username, 
        Users.email 
    FROM Profiles
    INNER JOIN Users ON Profiles.user_id = Users.user_id
";

$result_profiles = $conn->query($sql_profiles);

// Check if profiles are found
if ($result_profiles->num_rows > 0) {
    $profiles = [];
    while ($row = $result_profiles->fetch_assoc()) {
        $profiles[] = [
            'profile_id' => $row['profile_id'],
            'user_id' => $row['user_id'],
            'bio' => $row['bio'],
            'username' => $row['username'],
            'email' => $row['email'],
        ];
    }
    echo json_encode(['success' => true, 'data' => $profiles]);
} else {
    echo json_encode(['error' => 'No profiles found']);
}
?>
