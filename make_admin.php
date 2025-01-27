<?php
include('db.php');

$userEmail = '708003@student.centralbeds.ac.uk';

$db = getDbConnection();

// Update your user to be an admin
$stmt = $db->prepare('UPDATE users SET is_admin = 1 WHERE email = ?');
$stmt->bindValue(1, $userEmail, SQLITE3_TEXT);
$stmt->execute();

echo "Admin privileges granted to $userEmail";
?> 