<?php
include('db.php');
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if (isset($_GET['id'])) {
    $db = getDbConnection();
    
    // Get current status
    $stmt = $db->prepare('SELECT status FROM tickets WHERE id = ?');
    $stmt->bindValue(1, $_GET['id'], SQLITE3_INTEGER);
    $result = $stmt->execute();
    $ticket = $result->fetchArray();
    
    // Toggle status
    $newStatus = $ticket['status'] == 'open' ? 'closed' : 'open';
    
    // Update status
    $stmt = $db->prepare('UPDATE tickets SET status = ?, updated_at = DATETIME("now") WHERE id = ?');
    $stmt->bindValue(1, $newStatus, SQLITE3_TEXT);
    $stmt->bindValue(2, $_GET['id'], SQLITE3_INTEGER);
    $stmt->execute();
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit();
}
