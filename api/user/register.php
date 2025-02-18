<?php
// Include necessary files
include('../../db_connection.php');
include('../auth.php');

$default_role = 'user';

if (isset($_POST['username'], $_POST['password'], $_POST['email'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $role = $default_role; // Default role is user

    $sql_check = "SELECT user_id FROM Users WHERE username = ? OR email = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param('ss', $username, $email);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['error' => 'Username or Email already exists']);
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into the table
    $sql_insert_user = "INSERT INTO Users (username, password, email, role) VALUES (?, ?, ?, ?)";
    $stmt_insert_user = $conn->prepare($sql_insert_user);
    $stmt_insert_user->bind_param('ssss', $username, $hashed_password, $email, $role);

    if ($stmt_insert_user->execute()) {
        $new_user_id = $stmt_insert_user->insert_id;

        $sql_insert_profile = "INSERT INTO Profiles (user_id, bio) VALUES (?, '')";
        $stmt_insert_profile = $conn->prepare($sql_insert_profile);
        $stmt_insert_profile->bind_param('i', $new_user_id);
        $stmt_insert_profile->execute();

        $jwt_token = generate_jwt($new_user_id, $username, $role);

        http_response_code(201); // Created
        echo json_encode([
            'success' => 'User registered successfully',
            'token' => $jwt_token
        ]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'Error registering user']);
    }
} else {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Missing required parameters']);
}
?>
