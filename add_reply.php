<?php
session_start();
require 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ticket_id = (int) $_POST['ticket_id'];
    $reply = $_POST['reply'];
    $user_id = $_SESSION['user_id'];

    // Insert the reply into the database
    $query = "INSERT INTO replies (ticket_id, user_id, reply, created_at) 
              VALUES (?, ?, ?, datetime('now'))";
    $stmt = $db->prepare($query);
    $stmt->bindValue(1, $ticket_id, SQLITE3_INTEGER);
    $stmt->bindValue(2, $user_id, SQLITE3_INTEGER);
    $stmt->bindValue(3, $reply, SQLITE3_TEXT);
    $stmt->execute();

    // Redirect back to the ticket details page
    header("Location: ticket_details.php?ticket_id=" . $ticket_id);
    exit();
}
?>
