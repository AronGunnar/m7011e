<?php
// Include database connection
include('db_connection.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Overview</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Database Overview</h1>

        <!-- Users Table -->
        <h2>Users</h2>
        <table id="users-table">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                </tr>
            </thead>
            <tbody>
                <!-- User data will be inserted here -->
            </tbody>
        </table>

        <!-- Profiles Table -->
        <h2>Profiles</h2>
        <table id="profiles-table">
            <thead>
                <tr>
                    <th>Profile ID</th>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Bio</th>
                </tr>
            </thead>
            <tbody>
                <!-- Profile data will be inserted here -->
            </tbody>
        </table>

        <!-- Posts Table -->
        <h2>Posts</h2>
        <table id="posts-table">
            <thead>
                <tr>
                    <th>Post ID</th>
                    <th>Title</th>
                    <th>Content</th>
                    <th>Author</th>
                    <th>Created At</th>
                    <th>Tags</th>
                </tr>
            </thead>
            <tbody>
                <!-- Posts data will be inserted here -->
            </tbody>
        </table>

        <!-- Tags Table -->
        <h2>Tags</h2>
        <table id="tags-table">
            <thead>
                <tr>
                    <th>Tag ID</th>
                    <th>Tag Name</th>
                </tr>
            </thead>
            <tbody>
                <!-- Tags data will be inserted here -->
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            // Fetch and display users
            fetchUsers();

            // Fetch and display profiles
            fetchProfiles();

            // Fetch and display posts
            fetchPosts();

            // Fetch and display tags
            fetchTags();
        });

        function fetchUsers() {
            $.ajax({
                url: 'api/user/get_all_user.php', // Path to your get all users API
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const usersTable = $('#users-table tbody');
                        usersTable.empty(); // Clear the table
                        response.data.forEach(user => {
                            usersTable.append(`
                                <tr>
                                    <td>${user.user_id}</td>
                                    <td>${user.username}</td>
                                    <td>${user.email}</td>
                                    <td>${user.role}</td>
                                </tr>
                            `);
                        });
                    } else {
                        alert('Error fetching users: ' + response.error);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error: ' + error);
                }
            });
        }

        function fetchProfiles() {
            $.ajax({
                url: 'api/profile/get_all_bios.php', // Path to your get all profiles API
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const profilesTable = $('#profiles-table tbody');
                        profilesTable.empty(); // Clear the table
                        response.data.forEach(profile => {
                            profilesTable.append(`
                                <tr>
                                    <td>${profile.profile_id}</td>
                                    <td>${profile.user_id}</td>
                                    <td>${profile.username}</td>
                                    <td>${profile.email}</td>
                                    <td>${profile.bio}</td>
                                </tr>
                            `);
                        });
                    } else {
                        alert('Error fetching profiles: ' + response.error);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error: ' + error);
                }
            });
        }

        function fetchPosts() {
            $.ajax({
                url: 'api/post/get_posts.php', // Path to your get all posts API
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const postsTable = $('#posts-table tbody');
                        postsTable.empty(); // Clear the table
                        response.posts.forEach(post => {
                            postsTable.append(`
                                <tr>
                                    <td>${post.post_id}</td>
                                    <td>${post.title}</td>
                                    <td>${post.content}</td>
                                    <td>${post.username}</td>
                                    <td>${post.created_at}</td>
                                    <td>${post.tags.join(', ')}</td>
                                </tr>
                            `);
                        });
                    } else {
                        alert('Error fetching posts: ' + response.error);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error: ' + error);
                }
            });
        }

        function fetchTags() {
            $.ajax({
                url: 'api/tag/get_all_tags.php', // Path to your get all tags API
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const tagsTable = $('#tags-table tbody');
                        tagsTable.empty(); // Clear the table
                        response.data.forEach(tag => {
                            tagsTable.append(`
                                <tr>
                                    <td>${tag.tag_id}</td>
                                    <td>${tag.tag_name}</td>
                                </tr>
                            `);
                        });
                    } else {
                        alert('Error fetching tags: ' + response.error);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error: ' + error);
                }
            });
        }
    </script>
</body>
</html>
