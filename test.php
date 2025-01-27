<?php
session_start();
include('db.php');

$db = getDbConnection();
$user_id = $_SESSION['user_id'];

// Basic HTML structure with minimal styling
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Page</title>
    <style>
        .ticket {
            border: 1px solid black;
            margin: 10px;
            padding: 10px;
        }
    </style>
</head>
<body>
    <h1>Test Page</h1>
    
    <h2>Session Data:</h2>
    <pre><?php print_r($_SESSION); ?></pre>
    
    <h2>Tickets:</h2>
    <?php
    $query = "SELECT * FROM tickets WHERE user_id = " . $user_id;
    $result = $db->query($query);
    
    while ($ticket = $result->fetchArray(SQLITE3_ASSOC)) {
        echo "<div class='ticket'>";
        echo "<h3>Ticket ID: " . $ticket['id'] . "</h3>";
        echo "<p>Title: " . $ticket['title'] . "</p>";
        echo "<p>Description: " . $ticket['description'] . "</p>";
        echo "<p>Status: " . $ticket['status'] . "</p>";
        echo "</div>";
    }
    ?>
</body>
</html> 