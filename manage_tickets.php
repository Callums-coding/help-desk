<?php
// manage_tickets.php

session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit();
}

include('db.php');
$db = getDbConnection();

$pageTitle = 'Manage Tickets';
include 'admin_header.php';

// Handle delete ticket request
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $ticketId = $_GET['id'];

    // Ensure the user has permission to delete (either an admin or the ticket owner)
    if ($_SESSION['is_admin']) {
        // Admin can delete any ticket
        $stmt = $db->prepare('DELETE FROM tickets WHERE id = ?');
        $stmt->bindValue(1, $ticketId, SQLITE3_INTEGER);
        
        if ($stmt->execute()) {
            // Redirect to the same page after successful deletion
            header('Location: manage_tickets.php?message=Ticket+deleted+successfully');
            exit();
        } else {
            echo "Error deleting ticket.";
        }
    } else {
        // Check if the logged-in user is the owner of the ticket
        $stmt = $db->prepare('SELECT user_id FROM tickets WHERE id = ?');
        $stmt->bindValue(1, $ticketId, SQLITE3_INTEGER);
        $ticket = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

        if ($ticket && $ticket['user_id'] == $_SESSION['user_id']) {
            // User is the owner of the ticket, they can delete it
            $stmt = $db->prepare('DELETE FROM tickets WHERE id = ?');
            $stmt->bindValue(1, $ticketId, SQLITE3_INTEGER);
            
            if ($stmt->execute()) {
                // Redirect after successful deletion
                header('Location: manage_tickets.php?message=Ticket+deleted+successfully');
                exit();
            } else {
                echo "Error deleting ticket.";
            }
        } else {
            // Redirect if the user is not the owner or an admin
            header('Location: manage_tickets.php?error=You+are+not+authorized+to+delete+this+ticket');
            exit();
        }
    }
}

// Get all tickets based on user role
if ($_SESSION['is_admin']) {
    // Admins can see all tickets
    $tickets = $db->query('
        SELECT t.*, u.email as user_email, c.name as computer_name 
        FROM tickets t
        LEFT JOIN users u ON t.user_id = u.id
        LEFT JOIN computers c ON t.computer_id = c.id
        ORDER BY t.created_at DESC
    ');
} else {
    // Regular users can only see their own tickets
    $tickets = $db->query('
        SELECT t.*, u.email as user_email, c.name as computer_name 
        FROM tickets t
        LEFT JOIN users u ON t.user_id = u.id
        LEFT JOIN computers c ON t.computer_id = c.id
        WHERE t.user_id = ? 
        ORDER BY t.created_at DESC
    ', [$_SESSION['user_id']]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tickets - Admin Dashboard</title>
    <link rel="icon" href="https://bedfordcollegegroup.ac.uk/hideout-app/themes/the-hideout-theme-group/img/themes/bedford-college/favicon.png">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="admin-body">
    <div class="admin-container">
        <div class="admin-layout">
            <?php include('admin_sidebar.php'); ?>

            <main class="admin-content">

                <?php if (isset($_GET['message'])): ?>
                    <div class="message success">
                        <?php echo htmlspecialchars($_GET['message']); ?>
                    </div>
                <?php endif; ?>

                <table>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    <?php while ($ticket = $tickets->fetchArray()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($ticket['title']); ?></td>
                        <td><?php echo htmlspecialchars($ticket['description']); ?></td>
                        <td><?php echo htmlspecialchars($ticket['status']); ?></td>
                        <td>
                            <!-- Allow admins to update all tickets -->
                            <a href="view_ticket.php?id=<?php echo $ticket['id']; ?>">Update</a>
                            <!-- Allow admins to delete any ticket, users can delete their own tickets -->
                            <?php if ($_SESSION['is_admin'] || $ticket['user_id'] == $_SESSION['user_id']): ?>
                                <a href="manage_tickets.php?action=delete&id=<?php echo $ticket['id']; ?>" class="delete-ticket" onclick="return confirm('Are you sure you want to delete this ticket?');">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </main>
        </div>
    </div>
</body>
</html>
