<?php
session_start();
include('db.php');

// Redirect if not an admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit();
}

// Handle Add User request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'addUser') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email address.";
        exit();
    }

    if (strlen($password) < 8) {
        echo "Password must be at least 8 characters long.";
        exit();
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert the new user into the database
    $db = getDbConnection();
    $stmt = $db->prepare('INSERT INTO users (email, password, is_admin, created_at) VALUES (?, ?, ?, ?)');
    $stmt->bindValue(1, $email, SQLITE3_TEXT);
    $stmt->bindValue(2, $hashedPassword, SQLITE3_TEXT);
    $stmt->bindValue(3, $is_admin, SQLITE3_INTEGER);
    $stmt->bindValue(4, date('Y-m-d H:i:s'), SQLITE3_TEXT);

    if ($stmt->execute()) {
        header('Location: manage_users.php?message=User added successfully');
        exit();
    } else {
        echo "Error adding user.";
        exit();
    }
}

// Handle Delete User request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'deleteUser') {
    $user_id = $_POST['user_id'];

    // Check if the user ID is valid
    if (empty($user_id) || !is_numeric($user_id)) {
        echo "Invalid user ID.";
        exit();
    }

    // Delete user from database
    $db = getDbConnection();
    $stmt = $db->prepare('DELETE FROM users WHERE id = ?');
    $stmt->bindValue(1, $user_id, SQLITE3_INTEGER);

    if ($stmt->execute()) {
        header('Location: manage_users.php?message=User deleted successfully');
        exit();
    } else {
        echo "Error deleting user.";
        exit();
    }
}

// Handle Change Password request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'changePassword') {
    $user_id = $_POST['user_id'];
    $new_password = $_POST['new_password'];

    if (empty($user_id) || !is_numeric($user_id)) {
        echo "Invalid user ID.";
        exit();
    }

    if (strlen($new_password) < 8) {
        echo "Password must be at least 8 characters long.";
        exit();
    }

    // Hash the new password
    $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);

    // Update the user's password in the database
    $db = getDbConnection();
    $stmt = $db->prepare('UPDATE users SET password = ? WHERE id = ?');
    $stmt->bindValue(1, $hashedPassword, SQLITE3_TEXT);
    $stmt->bindValue(2, $user_id, SQLITE3_INTEGER);

    if ($stmt->execute()) {
        header('Location: manage_users.php?message=Password changed successfully');
        exit();
    } else {
        echo "Error changing password.";
        exit();
    }
}

// Handle Change User Role request (make user admin or normal)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'changeRole') {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['new_role']; // 1 for Admin, 0 for User

    if (empty($user_id) || !is_numeric($user_id)) {
        echo "Invalid user ID.";
        exit();
    }

    // Update the user's role in the database
    $db = getDbConnection();
    $stmt = $db->prepare('UPDATE users SET is_admin = ? WHERE id = ?');
    $stmt->bindValue(1, $new_role, SQLITE3_INTEGER);
    $stmt->bindValue(2, $user_id, SQLITE3_INTEGER);

    if ($stmt->execute()) {
        header('Location: manage_users.php?message=User role updated successfully');
        exit();
    } else {
        echo "Error updating user role.";
        exit();
    }
}

$db = getDbConnection();
$users = $db->query('SELECT * FROM users ORDER BY created_at DESC');

$pageTitle = 'Manage Users';
include 'admin_header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="admin-body">
    <div class="admin-container">
        <div class="admin-layout">
            <?php include('admin_sidebar.php'); ?>
            <main class="admin-content">
                <div class="content-section">
                    <div class="section-header">
                        <h2>User Management</h2>
                        <a href="#" onclick="showAddUserModal()" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i>
                        </a>
                    </div>

                    <!-- Add User Modal -->
                    <div id="addUserModal" class="modal" style="display: none;">
                        <div class="modal-content">
                            <h2>Create New User</h2>
                            <form id="addUserForm" method="POST">
                                <input type="hidden" name="action" value="addUser">
                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="email" id="email" name="email" required class="form-input">
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" id="password" name="password" required class="form-input">
                                </div>
                                <div class="form-group">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="is_admin" id="is_admin">
                                        Grant Admin Privileges
                                    </label>
                                </div>
                                <div class="form-actions">
                                    <button type="button" class="btn btn-secondary" onclick="closeAddUserModal()">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Create User</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- User Table -->
                    <div class="users-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($user = $users->fetchArray(SQLITE3_ASSOC)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo $user['is_admin'] ? 'Admin' : 'User'; ?></td>
                                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                        <td>
                                            <!-- Icons only -->
                                            <button onclick="showChangePasswordModal(<?php echo $user['id']; ?>)" class="btn btn-sm" title="Reset Password">
                                                <i class="fas fa-key"></i>
                                            </button>
                                            <button onclick="changeUserRole(<?php echo $user['id']; ?>, <?php echo $user['is_admin'] ? 0 : 1; ?>)" class="btn btn-sm" title="Change Role">
                                                <i class="fas fa-user-shield"></i>
                                            </button>
                                            <button onclick="deleteUser(<?php echo $user['id']; ?>)" class="btn btn-sm" title="Delete User">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        function showAddUserModal() {
            document.getElementById('addUserModal').style.display = 'block';
        }

        function closeAddUserModal() {
            document.getElementById('addUserModal').style.display = 'none';
        }

        function showChangePasswordModal(userId) {
            const newPassword = prompt("Enter new password:");
            if (newPassword && newPassword.length >= 8) {
                const formData = new FormData();
                formData.append('action', 'changePassword');
                formData.append('user_id', userId);
                formData.append('new_password', newPassword);

                fetch('manage_users.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    if (data.includes('Password changed successfully')) {
                        window.location.reload(); // Reload the page to reflect changes
                    } else {
                        alert('Error changing password');
                    }
                })
                .catch(error => {
                    alert('There was an error processing your request.');
                });
            } else {
                alert("Password must be at least 8 characters long.");
            }
        }

        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user?')) {
                const formData = new FormData();
                formData.append('action', 'deleteUser');
                formData.append('user_id', userId);

                fetch('manage_users.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    if (data.includes('User deleted successfully')) {
                        window.location.reload(); // Reload the page to reflect changes
                    } else {
                        alert('Error deleting user');
                    }
                })
                .catch(error => {
                    alert('There was an error processing your request.');
                });
            }
        }

        function changeUserRole(userId, newRole) {
            if (confirm('Are you sure you want to change the user role?')) {
                const formData = new FormData();
                formData.append('action', 'changeRole');
                formData.append('user_id', userId);
                formData.append('new_role', newRole);

                fetch('manage_users.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    if (data.includes('User role updated successfully')) {
                        window.location.reload(); // Reload the page to reflect changes
                    } else {
                        alert('Error updating user role');
                    }
                })
                .catch(error => {
                    alert('There was an error processing your request.');
                });
            }
        }
    </script>
</body>
</html>
