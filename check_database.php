<?php
include('db.php');

$db = getDbConnection();

echo "<h2>Users Table Contents:</h2>";
$result = $db->query('SELECT * FROM users');
echo "<pre>";
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    // Hide password hash for security
    $row['password'] = '[HIDDEN]';
    print_r($row);
}
echo "</pre>";

echo "<h2>Database Tables:</h2>";
$tables = $db->query("SELECT name FROM sqlite_master WHERE type='table'");
echo "<pre>";
while ($table = $tables->fetchArray(SQLITE3_ASSOC)) {
    print_r($table);
}
echo "</pre>";
?> 