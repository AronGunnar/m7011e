<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php'); // Redirect to login page if not logged in or not an admin
    exit;
}

// Include database connection
include('db_connection.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
</head>
<body>
    <h1>Welcome to the Admin Page</h1>

    <h2>Manage Users</h2>
    <!-- Table to list all users -->
    <table border="1" id="usersTable">
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Users will be populated dynamically using JavaScript -->
        </tbody>
    </table>

    <hr>

    <h2>Manage Tags</h2>

    <!-- Form to create a new tag -->
    <form id="createTagForm">
        <label for="tag_name">Create Tag:</label>
        <input type="text" id="tag_name" name="tag_name" required>
        <button type="submit">Create Tag</button>
    </form>

    <hr>

    <!-- Display existing tags -->
    <h3>Existing Tags</h3>
    <table id="tagsTable" border="1">
        <thead>
            <tr>
                <th>Tag Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Tags will be loaded here dynamically using AJAX -->
        </tbody>
    </table>

    <hr>
    <a href="view_all_data.php" style="text-decoration: none; color: black; font-size: 16px;">View All Data</a>
    <p><a href="dashboard.php">Back to Dashboard</a></p>

    <script>
        // Function to fetch all users from the get_all_user.php API
        async function fetchUsers() {
            try {
                const response = await fetch('/m7011e/api/user/get_all_user.php');
                const data = await response.json();

                if (data.success) {
                    const usersTable = document.getElementById('usersTable').getElementsByTagName('tbody')[0];
                    usersTable.innerHTML = '';  // Clear existing rows

                    data.data.forEach(user => {
                        const row = usersTable.insertRow();
                        row.insertCell(0).textContent = user.username;
                        row.insertCell(1).textContent = user.email;

                        const roleCell = row.insertCell(2);
                        const roleSelect = document.createElement('select');
                        roleSelect.id = 'role_' + user.user_id;
                        roleSelect.onchange = function() { changeUserRole(user.user_id); };
                        roleSelect.innerHTML = `
                            <option value="user" ${user.role === 'user' ? 'selected' : ''}>User</option>
                            <option value="editor" ${user.role === 'editor' ? 'selected' : ''}>Editor</option>
                            <option value="admin" ${user.role === 'admin' ? 'selected' : ''}>Admin</option>
                        `;
                        roleCell.appendChild(roleSelect);

                        const actionCell = row.insertCell(3);
                        const deleteButton = document.createElement('button');
                        deleteButton.textContent = 'Delete';
                        deleteButton.onclick = function() { deleteUser(user.user_id); };
                        actionCell.appendChild(deleteButton);
                    });
                } else {
                    console.error('Error fetching users:', data.error);
                }
            } catch (error) {
                console.error('An error occurred while fetching users:', error);
            }
        }

        // Function to change a user's role
        async function changeUserRole(userId) {
            const newRole = document.getElementById('role_' + userId).value; // Get the new role

            try {
                const response = await fetch('/m7011e/api/user/update_user.php', {
                    method: 'POST',
                    body: new URLSearchParams({
                        'user_id': userId,
                        'new_role': newRole
                    }),
                });

                const data = await response.json();

                if (data.success) {
                    alert('User role updated successfully');
                } else {
                    alert('Error: ' + data.error);
                }
            } catch (error) {
                console.error('An error occurred while changing user role:', error);
            }
        }

        // Function to delete a user
        async function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user?')) {
                try {
                    // Send a DELETE request to the API with the user_id
                    const response = await fetch(`/m7011e/api/user/delete_user.php?user_id=${userId}`, { 
                        method: 'DELETE' 
                    });

                    const data = await response.json();

                    if (response.ok) {
                        // If the response is successful, show success message
                        alert('User deleted successfully');
                        fetchUsers();  // Refresh users list
                    } else {
                        // If the response is an error, show the error message
                        alert('Error: ' + (data.error || 'An unknown error occurred.'));
                    }
                } catch (error) {
                    console.error('Error occurred while deleting user:', error);
                    alert('An error occurred while deleting the user.');
                }
            }
        }

        // Function to create a new tag
        document.getElementById('createTagForm').addEventListener('submit', async function(event) {
            event.preventDefault();
            const tagName = document.getElementById('tag_name').value;

            try {
                const response = await fetch('/m7011e/api/tag/create_tag.php', {
                    method: 'POST',
                    body: new URLSearchParams({ 'tag_name': tagName }),
                });
                const data = await response.json();

                if (data.success) {
                    alert('Tag created successfully');
                    fetchTags();  // Refresh tags list
                } else {
                    alert('Error: ' + data.error);
                }
            } catch (error) {
                console.error('An error occurred while creating tag:', error);
            }
        });

        // Function to fetch all tags from the get_tag.php API
        async function fetchTags() {
            try {
                const response = await fetch('/m7011e/api/tag/get_tag.php');
                const data = await response.json();

                if (data.success) {
                    const tagsTable = document.getElementById('tagsTable').getElementsByTagName('tbody')[0];
                    tagsTable.innerHTML = '';  // Clear existing tags

                    data.data.forEach(tag => {
                        const row = tagsTable.insertRow();
                        row.insertCell(0).textContent = tag.tag_name;
                        const deleteCell = row.insertCell(1);
                        const deleteButton = document.createElement('button');
                        deleteButton.textContent = 'Delete';
                        deleteButton.onclick = () => deleteTag(tag.tag_id);
                        deleteCell.appendChild(deleteButton);
                    });
                } else {
                    console.error('Error fetching tags:', data.error);
                }
            } catch (error) {
                console.error('An error occurred while fetching tags:', error);
            }
        }

        // Function to delete a tag
        async function deleteTag(tagId) {
            try {
                const response = await fetch(`/m7011e/api/tag/delete_tag.php?tag_id=${tagId}`, { method: 'DELETE' });
                const data = await response.json();

                if (data.success) {
                    alert('Tag deleted successfully');
                    fetchTags();  // Refresh tags list
                } else {
                    alert('Error: ' + data.error);
                }
            } catch (error) {
                console.error('An error occurred while deleting tag:', error);
            }
        }

        // Initial call to load users and tags
        fetchUsers();
        fetchTags();
    </script>

</body>
</html>
