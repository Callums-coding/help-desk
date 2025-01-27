<?php
// login.php

include('db.php');
session_start();

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = getDbConnection();
    $email = $_POST['email'] ?? '';
    
    // Get user from database
    $stmt = $db->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->bindValue(1, $email, SQLITE3_TEXT);
    $result = $stmt->execute();
    $user = $result->fetchArray(SQLITE3_ASSOC);

    if ($user && password_verify($_POST['password'], $user['password'])) {
        // Store user data in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['is_admin'] = $user['is_admin'];

        // Debug log
        error_log("User logged in - ID: " . $user['id'] . ", Email: " . $user['email']);

        header('Location: index.php');
        exit();
    } else {
        $error = 'Invalid email or password';
    }
}

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Support System</title>
    <link rel="icon" href="https://bedfordcollegegroup.ac.uk/hideout-app/themes/the-hideout-theme-group/img/themes/bedford-college/favicon.png">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-card card">
            <div class="auth-header">
                <h1>Welcome Back</h1>
                <p>Sign in to your account</p>
            </div>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Invalid email or password</span>
                </div>
            <?php endif; ?>

            <form action="authenticate.php" method="POST" class="auth-form">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" class="form-input" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" class="form-input" required>
                    </div>
                </div>

                <button type="submit" class="button button-primary button-block">
                    <i class="fas fa-sign-in-alt"></i>
                    Sign In
                </button>
                <a href="register.php" class="button button-primary button-block">
                    Register
                </a>
            </form>
        </div>
    </div>
</body>
</html>
