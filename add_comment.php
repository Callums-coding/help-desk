<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id']) || !isset($_POST['ticket_id']) || !isset($_POST['comment'])) {
    header('Location: index.php');
    exit();
}

$db = getDbConnection();
$stmt = $db->prepare('INSERT INTO comments (ticket_id, user_id, comment, created_at) VALUES (?, ?, ?, datetime("now"))');
$stmt->bindValue(1, $_POST['ticket_id'], SQLITE3_INTEGER);
$stmt->bindValue(2, $_SESSION['user_id'], SQLITE3_INTEGER);
$stmt->bindValue(3, $_POST['comment'], SQLITE3_TEXT);
$stmt->execute();

header('Location: view_ticket.php?id=' . $_POST['ticket_id']);
exit(); 