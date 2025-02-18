<?php
// Clear the JWT token
setcookie('token', '', time() - 3600, '/', '', false, true);

echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
?>
