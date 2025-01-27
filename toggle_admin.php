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
    
    // First get current admin status
    $stmt = $db->prepare('SELECT is_admin FROM users WHERE id = ?');
    $stmt->bindValue(1, $userId);
    $result = $stmt->execute();
    $user = $result->fetchArray(SQLITE3_ASSOC);
    
    // Toggle the is_admin status
    $newStatus = $user['is_admin'] ? 0 : 1;
    
    $stmt = $db->prepare('UPDATE users SET is_admin = ? WHERE id = ?');
    $stmt->bindValue(1, $newStatus);
    $stmt->bindValue(2, $userId);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "User admin status updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update user admin status.";
    }
}

header('Location: manage_users.php');
exit(); 