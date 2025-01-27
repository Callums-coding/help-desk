<?php
include('db.php');
$db = getDbConnection();

echo "<h2>Database Debug Information</h2>";

// Check users table
echo "<h3>Users Table:</h3>";
$users = $db->query('SELECT * FROM users');
echo "<pre>";
while ($row = $users->fetchArray(SQLITE3_ASSOC)) {
    print_r($row);
}
echo "</pre>";

// Check tickets table
echo "<h3>All Tickets (regardless of user):</h3>";
$tickets = $db->query('SELECT * FROM tickets');
echo "<pre>";
while ($row = $tickets->fetchArray(SQLITE3_ASSOC)) {
    print_r($row);
}
echo "</pre>";

// Check specific user's tickets
echo "<h3>Tickets for User ID 1:</h3>";
$userTickets = $db->query('SELECT * FROM tickets WHERE user_id = 1');
echo "<pre>";
while ($row = $userTickets->fetchArray(SQLITE3_ASSOC)) {
    print_r($row);
}
echo "</pre>";

// Check computers table
echo "<h3>Computers Table:</h3>";
$computers = $db->query('SELECT * FROM computers');
echo "<pre>";
while ($row = $computers->fetchArray(SQLITE3_ASSOC)) {
    print_r($row);
}
echo "</pre>";

// Check for any SQL errors
echo "<h3>Last SQL Error:</h3>";
echo $db->lastErrorMsg();
?> 