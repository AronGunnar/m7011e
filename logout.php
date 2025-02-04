<?php
// Clear the JWT token by setting its expiry time to a past time
setcookie('token', '', time() - 3600, '/', '', false, true);

echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
?>
