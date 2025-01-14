<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define API endpoints with the correct path
$api_signup_url = "http://localhost/m7011e/api/user/register.php";
$api_login_url = "http://localhost/m7011e/api/user/login.php";

// Handle user signup via API
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
    // Collect form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare data for API request
    $data = [
        'username' => $username,
        'email' => $email,
        'password' => $password,
    ];

    // Send POST request to API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_signup_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Handle API response
    $response_data = json_decode($response, true);
    if ($http_code === 201) {
        $signup_success = true;
    } else {
        $signup_error = $response_data['error'] ?? 'Signup failed. Please try again.';
    }
}

// Handle user login via API
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // Collect form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare data for API request
    $data = [
        'email' => $email,
        'password' => $password,
    ];

    // Send POST request to API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_login_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Handle API response
    $response_data = json_decode($response, true);
    if ($http_code === 200 && isset($response_data['success'])) {
        // Successful login - Set session variables
        $_SESSION['user_id'] = $response_data['user_id'];
        $_SESSION['username'] = $response_data['username'];
        $_SESSION['role'] = $response_data['role'];

        // Redirect to dashboard
        header('Location: dashboard.php');
        exit;
    } else {
        $login_error = $response_data['error'] ?? 'Login failed. Please try again.';
    }
}

// Display user session information
function displayUserSession()
{
    if (isset($_SESSION['username'])) {
        echo '<div class="session-info">';
        echo '<p>Logged in as: ' . htmlspecialchars($_SESSION['username']) . '</p>';
        echo '<a href="dashboard.php" class="dashboard-link">Dashboard</a>';
        echo '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login / Sign Up</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7f6;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            width: 100%;
            max-width: 500px;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #4CAF50;
            font-size: 2em;
        }
        h2 {
            font-size: 1.5em;
            margin-top: 20px;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            font-size: 1em;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .message {
            margin-top: 20px;
            text-align: center;
        }
        .message.success {
            color: green;
        }
        .message.error {
            color: red;
        }
        .session-info {
            position: fixed;
            top: 10px;
            right: 10px;
            text-align: right;
            font-size: 0.9em;
            background-color: #f4f7f6;
            padding: 10px;
            border-radius: 4px;
        }
        .dashboard-link {
            text-decoration: none;
            color: black;
            font-weight: bold;
        }
        .dashboard-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php displayUserSession(); ?>
        <h1>Login / Sign Up</h1>

        <!-- Display signup success message -->
        <?php if (isset($signup_success) && $signup_success === true): ?>
            <div class="message success">Signup successful! Please log in.</div>
        <?php endif; ?>

        <!-- Login Form -->
        <h2>Login</h2>
        <?php if (isset($login_error)): ?>
            <div class="message error"><?php echo htmlspecialchars($login_error); ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="email" name="email" placeholder="Email" required autocomplete="email">
            <input type="password" name="password" placeholder="Password" required autocomplete="current-password">
            <button type="submit" name="login">Login</button>
        </form>

        <hr>

        <!-- Signup Form -->
        <h2>Sign Up</h2>
        <?php if (isset($signup_error)): ?>
            <div class="message error"><?php echo htmlspecialchars($signup_error); ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required autocomplete="username">
            <input type="email" name="email" placeholder="Email" required autocomplete="email">
            <input type="password" name="password" placeholder="Password" required autocomplete="new-password">
            <button type="submit" name="signup">Sign Up</button>
        </form>
    </div>
</body>
</html>
