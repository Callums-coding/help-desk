<?php
session_start();
include('db.php');

// Check if user is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $isAdmin = isset($_POST['is_admin']) ? 1 : 0;

    if (empty($email) || empty($password)) {
        header('Location: admin.php?tab=users&error=missing_fields');
        exit();
    }

    $db = getDbConnection();

    // Check if email already exists
    $stmt = $db->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->bindValue(1, $email, SQLITE3_TEXT);
    $result = $stmt->execute();
    
    if ($result->fetchArray()) {
        header('Location: admin.php?tab=users&error=email_exists');
        exit();
    }

    // Create new user
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare('INSERT INTO users (email, password, is_admin) VALUES (?, ?, ?)');
    $stmt->bindValue(1, $email, SQLITE3_TEXT);
    $stmt->bindValue(2, $hashedPassword, SQLITE3_TEXT);
    $stmt->bindValue(3, $isAdmin, SQLITE3_INTEGER);
    $stmt->execute();

    header('Location: admin.php?tab=users&success=user_created');
    exit();
}

header('Location: admin.php?tab=users');
exit(); 