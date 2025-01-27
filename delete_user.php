<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id'])) {
    $userId = $_GET['id'];
    
    // Don't allow self-deletion
    if ($userId == $_SESSION['user_id']) {
        $_SESSION['error'] = "You cannot delete your own account.";
        header('Location: manage_users.php');
        exit();
    }
    
    $db = getDbConnection();
    
    // Delete the user
    $stmt = $db->prepare('DELETE FROM users WHERE id = ?');
    $stmt->bindValue(1, $userId);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "User deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete user.";
    }
}

header('Location: manage_users.php');
exit(); 