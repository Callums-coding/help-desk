<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: error.php?code=403');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: error.php?code=404');
    exit();
}

$db = getDbConnection();

// Verify user exists in database
$stmt = $db->prepare('SELECT id FROM users WHERE id = ? AND email = ?');
$stmt->bindValue(1, $_SESSION['user_id'], SQLITE3_INTEGER);
$stmt->bindValue(2, $_SESSION['email'], SQLITE3_TEXT);
$result = $stmt->execute();
$user = $result->fetchArray(SQLITE3_ASSOC);

if (!$user) {
    // User not found in database
    session_destroy();
    header('Location: login.php');
    exit();
}

// Get form data
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$computer_id = trim($_POST['computer_id'] ?? '');
$user_id = $user['id']; // Use verified user ID from database

// Validate input
if (empty($title) || empty($description) || empty($computer_id)) {
    header('Location: error.php?code=missing_fields');
    exit();
}

try {
    // Prepare the insert statement
    $stmt = $db->prepare('
        INSERT INTO tickets 
            (user_id, computer_id, title, description, status, created_at, updated_at) 
        VALUES 
            (?, ?, ?, ?, "open", datetime("now"), datetime("now"))
    ');

    // Bind the values
    $stmt->bindValue(1, $user_id, SQLITE3_INTEGER);
    $stmt->bindValue(2, $computer_id, SQLITE3_INTEGER);
    $stmt->bindValue(3, $title, SQLITE3_TEXT);
    $stmt->bindValue(4, $description, SQLITE3_TEXT);

    // Execute the statement
    $result = $stmt->execute();

    if ($result) {
        // Get the ID of the newly created ticket
        $ticket_id = $db->lastInsertRowID();
        
        // Redirect to the ticket view page
        header('Location: view_ticket.php?id=' . $ticket_id . '&success=created');
        exit();
    } else {
        // If there was an error, redirect to error page
        header('Location: error.php?code=database_error');
        exit();
    }
} catch (Exception $e) {
    error_log("Error creating ticket: " . $e->getMessage());
    header('Location: error.php?code=system_error');
    exit();
}
?> 