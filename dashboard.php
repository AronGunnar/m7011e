<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
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
            align-items: flex-start;
            min-height: 100vh;
        }

        .container {
            width: 80%;
            max-width: 900px;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
        }

        h1, h2 {
            text-align: center;
            color: #4CAF50;
        }

        .message {
            text-align: center;
            margin-top: 10px;
            font-size: 1em;
        }

        .message.success {
            color: green;
        }

        .message.error {
            color: red;
        }

        /* Form Styles */
        textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1em;
            resize: vertical;
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

        /* Links Styling */
        a {
            text-decoration: none;
            color: #4CAF50;
            font-weight: bold;
            display: block;
            text-align: center;
            margin: 15px 0;
        }

        a:hover {
            color: #45a049;
            text-decoration: underline;
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

        /* Admin Button */
        .admin-link {
            font-size: 1.1em;
            font-weight: bold;
            text-align: center;
            margin-top: 20px;
            display: block;
            color: black;
        }

        .admin-link:hover {
            color: #4CAF50;
        }

        .back-home {
            font-size: 16px;
            color: #4CAF50;
            display: inline-block;
            margin-top: 10px;
            text-align: left;
        }

        .back-home:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        // Display logged in user information
        if (isset($_SESSION['username'])) {
            echo '<div class="session-info">';
            echo '<p>Logged in as: ' . htmlspecialchars($_SESSION['username']) . '</p>';
            echo '<a href="dashboard.php" class="dashboard-link">Dashboard</a>';
            echo '</div>';
        }
        ?>

        <a href="index.php" class="back-home">Back to Home</a>

        <h1>Welcome, <span id="username"></span>!</h1>
        <h2>Your Profile</h2>

        <!-- Display User Information -->
        <div id="userInfo">
            <p><strong>Username:</strong> <span id="displayUsername"></span></p>
            <p><strong>Email:</strong> <span id="displayEmail"></span></p>
            <p><strong>Role:</strong> <span id="displayRole"></span></p>
        </div>

        <!-- Form to update bio -->
        <form id="bioForm">
            <textarea name="bio" placeholder="Enter your bio" id="bioText"></textarea><br>
            <button type="submit" id="updateBioButton">Update Bio</button>
        </form>

        <hr>

        <h2>Your Information</h2>
        <p><strong>Bio:</strong> <span id="displayBio"></span></p>

        <a href="make_post.php">Make a Post</a>
        <a href="view_posts.php">View All Posts</a>

        <hr>

        <!-- Admin button (only visible to admins) -->
        <a href="admin.php" class="admin-link" id="adminLink" style="display: none;">Admin Page</a>

        <hr>

        <h2>Logout</h2>
        <a href="logout.php">Logout</a>
    </div>

    <script>
        // Fetch user details from the correct API endpoint
        async function getUserDetails() {
            try {
                const response = await fetch('/m7011e/api/user/get_user.php');
                const data = await response.json();

                if (data.success) {
                    // Populate the user information dynamically
                    document.getElementById('username').innerText = data.data.username;
                    document.getElementById('displayUsername').innerText = data.data.username;
                    document.getElementById('displayEmail').innerText = data.data.email;
                    document.getElementById('displayRole').innerText = data.data.role;

                    // If user is an admin, show admin link
                    if (data.data.role === 'admin') {
                        document.getElementById('adminLink').style.display = 'block';
                    } else {
                        document.getElementById('adminLink').style.display = 'none';
                    }
                } else {
                    console.error('Error fetching user details:', data.error);
                }
            } catch (error) {
                console.error('An error occurred while fetching user details:', error);
            }
        }

        // Fetch bio from the correct API endpoint
        async function getBio() {
            try {
                const response = await fetch('/m7011e/api/profile/get_bio.php');
                const data = await response.json();

                if (data.success) {
                    // Display the bio
                    document.getElementById('displayBio').innerText = data.data.bio;
                    document.getElementById('bioText').value = data.data.bio; // Pre-fill bio field
                } else {
                    console.error('Error fetching bio:', data.error);
                }
            } catch (error) {
                console.error('An error occurred while fetching bio:', error);
            }
        }

        // Handle bio update form submission
        document.getElementById('bioForm').addEventListener('submit', async function(event) {
            event.preventDefault();

            const bio = document.getElementById('bioText').value;

            try {
                const response = await fetch('/m7011e/api/profile/update_bio.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ bio: bio })
                });
                const data = await response.json();

                if (data.success) {
                    document.getElementById('displayBio').innerText = bio;
                    alert('Bio updated successfully');
                } else {
                    console.error('Error updating bio:', data.error);
                    alert('Failed to update bio');
                }
            } catch (error) {
                console.error('An error occurred while updating bio:', error);
                alert('An error occurred');
            }
        });

        // Call the functions to load the data
        getUserDetails();
        getBio();
    </script>
</body>
</html>
