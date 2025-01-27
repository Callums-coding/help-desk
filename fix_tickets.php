<?php
include('db.php');
$db = getDbConnection();

try {
    // Update all tickets to be associated with user ID 1
    $query = "UPDATE tickets SET user_id = 1 WHERE user_id = 3";
    $result = $db->exec($query);
    
    if ($result !== false) {
        echo "<h2>Ticket Update Results:</h2>";
        echo "Successfully updated tickets to user ID 1<br>";
        
        // Verify the update
        $tickets = $db->query('SELECT * FROM tickets WHERE user_id = 1');
        echo "<h3>Updated Tickets:</h3>";
        echo "<pre>";
        while ($ticket = $tickets->fetchArray(SQLITE3_ASSOC)) {
            print_r($ticket);
        }
        echo "</pre>";
    } else {
        echo "Error updating tickets: " . $db->lastErrorMsg();
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?> 