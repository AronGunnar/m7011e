<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('db_connection.php'); // Include your database connection

// Handle user signup
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['signup'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password

    // Insert into Users table
    $sql = "INSERT INTO Users (username, email, password, role) VALUES (?, ?, ?, 'user')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sss', $username, $email, $password);

    if ($stmt->execute()) {
        // After successful signup, get user ID and create a profile
        $user_id = $stmt->insert_id;

        // Insert initial empty bio for the user
        $sql_profile = "INSERT INTO Profiles (user_id, bio) VALUES (?, ?)";
        $stmt_profile = $conn->prepare($sql_profile);
        $bio = ''; // Empty bio on signup
        $stmt_profile->bind_param('is', $user_id, $bio);
        $stmt_profile->execute();

        // Redirect to the login page after signup
        header('Location: login.php?signup_success=true');
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Handle user login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM Users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            // Set session variables for the logged-in user
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect to user dashboard or profile
            header('Location: dashboard.php');
            exit;
        } else {
            $login_error = "Incorrect password!";
        }
    } else {
        $login_error = "User not found!";
    }
}

// Display user session information with a plain text "Dashboard" link
function displayUserSession() {
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
        /* General Styles */
        body {
            font-family: 'Arial', sans-serif;
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
            margin-top: 20px;
            font-size: 1.5em;
            color: #333;
        }

        /* Form Styles */
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1em;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            font-size: 1.1em;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        /* Success/Error Message Styles */
        .message {
            text-align: center;
            margin-top: 20px;
            font-size: 1em;
        }

        .message.success {
            color: green;
        }

        .message.error {
            color: red;
        }

        /* Session Info Styles */
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

        /* Back to Home Button */
        .back-home {
            text-decoration: none;
            font-size: 16px;
            color: #4CAF50;
            margin-top: 10px;
            display: inline-block;
        }

        .back-home:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="container">
        <?php displayUserSession(); ?>

        <a href="index.php" class="back-home">Back to Home</a>

        <h1>Login / Sign Up</h1>

        <!-- Display success message if signup is successful -->
        <?php if (isset($_GET['signup_success']) && $_GET['signup_success'] == 'true'): ?>
            <div class="message success">
                Signup successful! Please log in.
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <h2>Login</h2>
        <?php if (isset($login_error)): ?>
            <div class="message error">
                <?php echo $login_error; ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit" name="login">Login</button>
        </form>

        <hr>

        <!-- Sign Up Form -->
        <h2>Sign Up</h2>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit" name="signup">Sign Up</button>
        </form>
    </div>

</body>
</html>
