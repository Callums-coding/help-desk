<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$db = getDbConnection();
$user_id = $_SESSION['user_id'];
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
    <div class="app-container">
        <header class="header">
            <div class="header-content">
                <h1>Support Dashboard</h1>
                <div class="header-actions">
                    <?php if ($_SESSION['is_admin']): ?>
                        <a href="admin.php" class="button button-secondary">
                            <i class="fas fa-cog"></i>
                            Admin Panel
                        </a>
                    <?php endif; ?>
                    <a href="create_ticket.php" class="button button-primary">
                        <i class="fas fa-plus"></i>
                        New Ticket
                    </a>
                    <a href="logout.php" class="button button-secondary">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </div>
            </div>
        </header>

        <main class="main-content">
            <div class="content-header">
                <div class="content-title">
                    <h2>Your Tickets</h2>
                    <?php
                    $ticketCount = $db->querySingle("SELECT COUNT(*) FROM tickets WHERE user_id = " . $user_id);
                    ?>
                    <span class="ticket-count"><?php echo $ticketCount; ?> total</span>
                </div>
            </div>

            <div class="tickets-grid">
                <?php
                $query = "SELECT t.*, c.name as computer_name 
                         FROM tickets t 
                         LEFT JOIN computers c ON t.computer_id = c.id 
                         WHERE t.user_id = " . $user_id . "
                         ORDER BY t.created_at DESC";
                $result = $db->query($query);

                if ($result) {
                    while ($ticket = $result->fetchArray(SQLITE3_ASSOC)) {
                        ?>
                        <div class="ticket-card">
                            <div class="ticket-header">
                                <h3><?php echo htmlspecialchars($ticket['title']); ?></h3>
                                <span class="status-badge <?php echo $ticket['status']; ?>">
                                    <?php echo ucwords($ticket['status']); ?>
                                </span>
                            </div>
                            <div class="ticket-content">
                                <p><?php echo htmlspecialchars($ticket['description']); ?></p>
                            </div>
                            <div class="ticket-meta">
                                <span>
                                    <i class="fas fa-desktop"></i>
                                    <?php echo htmlspecialchars($ticket['computer_name']); ?>
                                </span>
                                <span>
                                    <i class="far fa-clock"></i>
                                    <?php echo date('M d, Y', strtotime($ticket['created_at'])); ?>
                                </span>
                            </div>
                            <div class="ticket-footer">
                                <a href="view_ticket.php?id=<?php echo $ticket['id']; ?>" class="button button-primary">
                                    View Details
                                </a>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    ?>
                    <div class="no-tickets">
                        <div class="no-tickets-content">
                            <i class="fas fa-ticket-alt"></i>
                            <h3>No Tickets Found</h3>
                            <p>You haven't created any support tickets yet.</p>
                            <a href="create_ticket.php" class="button button-primary">
                                Create Your First Ticket
                            </a>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </main>
    </div>
</body>
</html> 