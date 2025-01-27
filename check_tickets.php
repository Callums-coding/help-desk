<?php
include('db.php');
$db = getDbConnection();

echo "<h2>Checking Database Tables</h2>";

// Check tickets table
echo "<h3>Tickets Table:</h3>";
$tickets = $db->query('SELECT * FROM tickets');
echo "<pre>";
while ($ticket = $tickets->fetchArray(SQLITE3_ASSOC)) {
    print_r($ticket);
}
echo "</pre>";

// Check users table
echo "<h3>Users Table:</h3>";
$users = $db->query('SELECT * FROM users');
echo "<pre>";
while ($user = $users->fetchArray(SQLITE3_ASSOC)) {
    print_r($user);
}
echo "</pre>";

// Check computers table
echo "<h3>Computers Table:</h3>";
$computers = $db->query('SELECT * FROM computers');
echo "<pre>";
while ($computer = $computers->fetchArray(SQLITE3_ASSOC)) {
    print_r($computer);
}
echo "</pre>";

// Check current session
echo "<h3>Current Session:</h3>";
session_start();
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
?> 