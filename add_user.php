<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $isAdmin = isset($_POST['is_admin']) ? 1 : 0;

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Email and password are required.";
        header('Location: manage_users.php');
        exit();
    }

    $db = getDbConnection();
    
    // Check if email already exists
    $stmt = $db->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->bindValue(1, $email);
    $result = $stmt->execute();
    
    if ($result->fetchArray()) {
        $_SESSION['error'] = "Email already exists.";
        header('Location: manage_users.php');
        exit();
    }

    // Add new user
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare('INSERT INTO users (email, password, is_admin, created_at) VALUES (?, ?, ?, datetime("now"))');
    $stmt->bindValue(1, $email);
    $stmt->bindValue(2, $hashedPassword);
    $stmt->bindValue(3, $isAdmin);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "User added successfully.";
    } else {
        $_SESSION['error'] = "Failed to add user.";
    }
}

header('Location: manage_users.php');
exit(); 