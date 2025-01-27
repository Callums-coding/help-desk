<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id'])) {
    $userId = $_GET['id'];
    $db = getDbConnection();
    
    // Get user email for the form
    $stmt = $db->prepare('SELECT email FROM users WHERE id = ?');
    $stmt->bindValue(1, $userId);
    $result = $stmt->execute();
    $user = $result->fetchArray(SQLITE3_ASSOC);
    
    if (!$user) {
        $_SESSION['error'] = "User not found.";
        header('Location: manage_users.php');
        exit();
    }
?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Reset Password</title>
        <link rel="stylesheet" href="style.css">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
        <link rel="icon" href="https://bedfordcollegegroup.ac.uk/hideout-app/themes/the-hideout-theme-group/img/themes/bedford-college/favicon.png">
    </head>
    <body>
        <div class="container">
            <div class="form-card">
                <h2>Reset Password for <?php echo htmlspecialchars($user['email']); ?></h2>
                <form action="process_reset_password.php" method="POST">
                    <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
                    <div class="form-group">
                        <label for="new_password">New Password:</label>
                        <input type="password" id="new_password" name="new_password" required class="form-input">
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password:</label>
                        <input type="password" id="confirm_password" name="confirm_password" required class="form-input">
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Reset Password</button>
                        <a href="manage_users.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </body>
    </html>
<?php
    exit();
}

header('Location: manage_users.php');
?> 