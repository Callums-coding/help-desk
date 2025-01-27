<?php
session_start();
include('db.php');

// Set header to handle AJAX response
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    
    if (empty($userId) || empty($newPassword)) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit();
    }
    
    try {
        $db = getDbConnection();
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $stmt = $db->prepare('UPDATE users SET password = ? WHERE id = ?');
        $stmt->bindValue(1, $hashedPassword);
        $stmt->bindValue(2, $userId);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Password reset successful']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Database error']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Server error']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
exit(); 