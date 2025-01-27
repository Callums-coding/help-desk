<?php
session_start();
include('db.php');

$db = getDbConnection();
$user_id = $_SESSION['user_id'];

$query = "
    SELECT 
        t.*,
        c.name as computer_name
    FROM tickets t
    LEFT JOIN computers c ON t.computer_id = c.id
    WHERE t.user_id = " . $user_id . "
    ORDER BY t.created_at DESC";

$result = $db->query($query);

echo "<h1>Tickets Test Page</h1>";
echo "<pre>";
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    print_r($row);
}
echo "</pre>";
?> 