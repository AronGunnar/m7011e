<?php
// Composers autoloader
require_once '../../vendor/autoload.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

// Secret key for JWT token
define('SECRET_KEY', 'e56f7d587d9b8e05ad83d3d292adbf6b6bc9e1b74f2b5adcb5b249ffb8f680d7');

// Generates JWT token
function generate_jwt($user_id, $username, $role) {
    $issued_at = time();
    $expiration_time = $issued_at + 3600;  // 1 hour expiration
    $payload = array(
        "iat" => $issued_at,
        "exp" => $expiration_time,
        "user_id" => $user_id,
        "username" => $username,
        "role" => $role
    );
    
    return JWT::encode($payload, SECRET_KEY, 'HS256');
}

// Validates JWT token from cookies or Authorization header
function validate_jwt() {
    $authorization_header = apache_request_headers()['Authorization'] ?? '';
    
    if (strpos($authorization_header, 'Bearer ') !== false) {
        $jwt = str_replace('Bearer ', '', $authorization_header);
    } else {
        // Fallback to cookies if not found in header, redundant i think and not ever used
        $jwt = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;
    }

    if ($jwt) {
        try {
            // Decode JWT using secret key
            $decoded = JWT::decode($jwt, new Key(SECRET_KEY, 'HS256'));
            return (object) $decoded;  // Return decoded JWT object
        } catch (Exception $e) {
            return null;  // Invalid or expired token
        }
    }
    return null;  // No JWT token found
}
?>
