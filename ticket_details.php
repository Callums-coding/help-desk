<?php
session_start();
require 'db.php';

if (!isset($_GET['ticket_id'])) {
    die("Ticket ID is required");
}

$ticket_id = (int) $_GET['ticket_id'];

// Fetch the ticket details
$query = "SELECT title, description FROM tickets WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->bindValue(1, $ticket_id, SQLITE3_INTEGER);
$ticket = $stmt->execute()->fetchArray();

// Fetch the replies for the ticket
$query = "SELECT r.reply, r.created_at, u.email 
          FROM replies r
          JOIN users u ON r.user_id = u.id
          WHERE r.ticket_id = ?
          ORDER BY r.created_at";
$stmt = $db->prepare($query);
$stmt->bindValue(1, $ticket_id, SQLITE3_INTEGER);
$replies = $stmt->execute();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Ticket Details</title>
    <link rel="icon" href="https://bedfordcollegegroup.ac.uk/hideout-app/themes/the-hideout-theme-group/img/themes/bedford-college/favicon.png">
</head>
<body>

<h1><?php echo htmlspecialchars($ticket['title']); ?></h1>
<p><?php echo htmlspecialchars($ticket['description']); ?></p>

<h2>Replies</h2>
<?php while ($reply = $replies->fetchArray()): ?>
    <div>
        <strong><?php echo htmlspecialchars($reply['email']); ?>:</strong>
        <p><?php echo htmlspecialchars($reply['reply']); ?></p>
        <small><?php echo htmlspecialchars($reply['created_at']); ?></small>
    </div>
<?php endwhile; ?>

<h3>Reply to Ticket</h3>
<form action="add_reply.php" method="POST">
    <input type="hidden" name="ticket_id" value="<?php echo $ticket_id; ?>" />
    <textarea name="reply" required></textarea><br>
    <button type="submit">Submit Reply</button>
</form>

</body>
</html>
