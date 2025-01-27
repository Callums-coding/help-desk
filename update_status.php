<?php
session_start();
include('db.php');

// Check if user is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php');
    exit();
}

if (isset($_POST['ticket_id']) && isset($_POST['status'])) {
    $db = getDbConnection();
    
    // Validate status
    $validStatuses = ['open', 'in_progress', 'pending', 'resolved', 'closed'];
    if (!in_array($_POST['status'], $validStatuses)) {
        header('Location: admin.php');
        exit();
    }
    
    $stmt = $db->prepare('UPDATE tickets SET status = ?, updated_at = DATETIME("now") WHERE id = ?');
    $stmt->bindValue(1, $_POST['status'], SQLITE3_TEXT);
    $stmt->bindValue(2, $_POST['ticket_id'], SQLITE3_INTEGER);
    $stmt->execute();
}

header('Location: admin.php');
exit(); 