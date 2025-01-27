<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id'])) {
    $db = getDbConnection();
    $ticket_id = (int)$_GET['id'];
    
    // Delete the ticket
    $db->exec("DELETE FROM tickets WHERE id = $ticket_id");
    
    header('Location: admin.php');
    exit();
}

header('Location: admin.php');
exit(); 