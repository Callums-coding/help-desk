<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: login.php');
    exit();
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    header('Location: login.php?error=empty_fields');
    exit();
}

$db = getDbConnection();

// Prepare statement to prevent SQL injection
$stmt = $db->prepare('SELECT id, password, is_admin FROM users WHERE email = ?');
$stmt->bindValue(1, $email, SQLITE3_TEXT);
$result = $stmt->execute();
$user = $result->fetchArray(SQLITE3_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    // Login successful
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $email;
    $_SESSION['is_admin'] = $user['is_admin'];
    
    header('Location: index.php');
    exit();
} else {
    // Login failed
    header('Location: login.php?error=invalid_credentials');
    exit();
}
?> 