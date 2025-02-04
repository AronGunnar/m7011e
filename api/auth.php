<?php
// Include Composer's autoloader
require_once '../../vendor/autoload.php'; // Make sure the path is correct based on your project structure

use \Firebase\JWT\JWT;  // Use the Firebase JWT class
use \Firebase\JWT\Key;  // Use the Key class for the new decoding method

// Secret key to encode and decode JWT token
define('SECRET_KEY', 'e56f7d587d9b8e05ad83d3d292adbf6b6bc9e1b74f2b5adcb5b249ffb8f680d7');

// Function to generate a JWT token
function generate_jwt($user_id, $username, $role) {
    $issued_at = time();
    $expiration_time = $issued_at + 3600;  // 1 hour expiration
    $payload = array(
        "iat" => $issued_at,
        "exp" => $expiration_time,
        "user_id" => $user_id,
        "username" => $username,  // Pass username here
        "role" => $role
    );
    
    // Make sure to pass the algorithm as the third argument ('HS256')
    return JWT::encode($payload, SECRET_KEY, 'HS256');
}

// Function to validate the JWT token from cookies or Authorization header
function validate_jwt() {
    // Check Authorization header first
    $authorization_header = apache_request_headers()['Authorization'] ?? '';
    
    if (strpos($authorization_header, 'Bearer ') !== false) {
        $jwt = str_replace('Bearer ', '', $authorization_header);  // Extract JWT from header
    } else {
        // Fallback to cookies if not found in header
        $jwt = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;
    }

    if ($jwt) {
        try {
            // Decode JWT using the secret key and Key class for algorithm
            $decoded = JWT::decode($jwt, new Key(SECRET_KEY, 'HS256'));  // Corrected line
            return (object) $decoded;  // Return decoded JWT object
        } catch (Exception $e) {
            return null;  // Invalid or expired token
        }
    }
    return null;  // No JWT token found
}
?>
