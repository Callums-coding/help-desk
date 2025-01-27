<?php
// index.php

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include('db.php');
$db = getDbConnection();
$user_id = $_SESSION['user_id'];

// Get tickets with proper joins - without debug output
$query = "
    SELECT 
        t.*,
        c.name as computer_name
    FROM tickets t
    LEFT JOIN computers c ON t.computer_id = c.id
    WHERE t.user_id = " . $user_id . "
    ORDER BY t.created_at DESC";

$result = $db->query($query);
$tickets = array();

// Fetch all tickets into an array
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $tickets[] = $row;
}

// Remove all debug output and error logging
$totalTickets = count($tickets);

// Start the HTML output without any debug information
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Dashboard</title>
    <link rel="icon" href="https://bedfordcollegegroup.ac.uk/hideout-app/themes/the-hideout-theme-group/img/themes/bedford-college/favicon.png">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Simplified Header -->
    <nav class="admin-header">
        <h1>Support Dashboard</h1>
        <div class="header-actions">
            <?php if ($_SESSION['is_admin']): ?>
                <a href="admin.php" class="btn btn-secondary">
                    <i class="fas fa-cog"></i>
                    Admin Panel
                </a>
            <?php endif; ?>
            <a href="create_ticket.php" class="btn btn-secondary">
                <i class="fas fa-plus"></i>
                New Ticket
            </a>
            <a href="logout.php" class="btn btn-secondary">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </div>
    </nav>
    <div class="app-container">
        <main class="main-content">
            <div class="page-header">
                <div class="header-title">
                    <h2>Your Tickets</h2>
                    <span class="ticket-count"><?php echo $totalTickets; ?> total</span>
                </div>
            </div>

            <div class="ticket-container">
                <?php if ($totalTickets > 0): ?>
                    <table>
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                        <?php foreach ($tickets as $ticket): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($ticket['title']); ?></td>
                                <td><?php echo htmlspecialchars($ticket['description']); ?></td>
                                <td><?php echo htmlspecialchars($ticket['status']); ?></td>
                                <td><a href="view_ticket.php?id=<?php echo $ticket['id']; ?>">View Ticket</a></td>
                            </tr>  
                        <?php endforeach; ?>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-ticket-alt"></i>
                        <h3>No Tickets Found</h3>
                        <p>You haven't created any support tickets yet.</p>
                        <a href="create_ticket.php" class="nav-button primary">
                            Create Your First Ticket
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
