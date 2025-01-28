<?php
session_start();
include('db.php');
$db = getDbConnection();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if ticket ID is provided
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$ticketId = $_GET['id'];

// Fetch the ticket details
$stmt = $db->prepare('
    SELECT t.*, u.email as user_email, c.name as computer_name 
    FROM tickets t
    LEFT JOIN users u ON t.user_id = u.id
    LEFT JOIN computers c ON t.computer_id = c.id
    WHERE t.id = ?
');
$stmt->bindValue(1, $ticketId, SQLITE3_INTEGER);
$result = $stmt->execute();

$ticket = $result->fetchArray(SQLITE3_ASSOC);

// Check if the ticket exists
if (!$ticket) {
    header('Location: index.php');
    exit();
}

// Check if the user is allowed to view the ticket
if (!$_SESSION['is_admin'] && $ticket['user_id'] != $_SESSION['user_id']) {
    header('Location: index.php');
    exit();
}

// Get comments
$commentsStmt = $db->prepare('
    SELECT comments.*, users.email as user_email 
    FROM comments 
    JOIN users ON comments.user_id = users.id 
    WHERE ticket_id = ? 
    ORDER BY created_at DESC
');
$commentsStmt->bindValue(1, $ticketId, SQLITE3_INTEGER);
$comments = $commentsStmt->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Ticket - Support System</title>
    <link rel="icon" href="https://bedfordcollegegroup.ac.uk/hideout-app/themes/the-hideout-theme-group/img/themes/bedford-college/favicon.png">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="admin-header">
        <h1>Ticket</h1>
        <div class="header-actions">
            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                <a href="admin.php" class="btn btn-secondary">
                    <i class="fas fa-cog"></i>
                    Admin Panel
                </a>
            <?php endif; ?>
            <a href="/" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Go Back
            </a>
        </div>
    </nav>
    <div class="app-container">

        <main class="main-container">
            <div class="ticket-container">
                <div class="ticket-details card">
                    <div class="ticket-header">
                        <h2><?php echo htmlspecialchars($ticket['title']); ?></h2>
                        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                            <form method="POST" action="update_status.php" class="status-form">
                                <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                                <select name="status" class="form-select status-select" onchange="this.form.submit()">
                                    <option value="open" <?php echo $ticket['status'] == 'Open' ? 'selected' : ''; ?>>Open</option>
                                    <option value="in_progress" <?php echo $ticket['status'] == 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                                    <option value="pending" <?php echo $ticket['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="resolved" <?php echo $ticket['status'] == 'Resolved' ? 'selected' : ''; ?>>Resolved</option>
                                    <option value="closed" <?php echo $ticket['status'] == 'Closed' ? 'selected' : ''; ?>>Closed</option>
                                </select>
                            </form>
                        <?php else: ?>
                            <span class="status-badge <?php echo $ticket['status']; ?>">
                                <?php echo ucwords(str_replace('_', ' ', $ticket['status'])); ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="ticket-meta">
                        <div class="meta-item">
                            <i class="fas fa-user"></i>
                            <span>Created by: <?php echo htmlspecialchars($ticket['user_email']); ?></span>
                        </div>
                        <div class="meta-item">
                            <i class="far fa-clock"></i>
                            <span>Created: <?php echo date('M d, Y H:i', strtotime($ticket['created_at'])); ?></span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-clock"></i>
                            <span>Last updated: <?php echo date('M d, Y H:i', strtotime($ticket['updated_at'])); ?></span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-desktop"></i>
                            <span>Computer: <?php echo htmlspecialchars($ticket['computer_name']); ?></span>
                        </div>
                    </div>

                    <div class="ticket-description">
                        <h3>Description</h3>
                        <p><?php echo nl2br(htmlspecialchars($ticket['description'])); ?></p>
                    </div>
                </div>

                <div class="comments-section card">
                    <h3>Comments</h3>
                    
                    <form method="POST" action="add_comment.php" class="comment-form">
                        <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                        <div class="form-group">
                            <textarea name="comment" class="form-input" rows="3" placeholder="Add a comment..." required></textarea>
                        </div>
                        <button type="submit" class="button button-primary">
                            <i class="fas fa-paper-plane"></i>
                            Add Comment
                        </button>
                    </form>

                    <div class="comments-list">
                        <?php 
                        $hasComments = false;
                        while ($comment = $comments->fetchArray(SQLITE3_ASSOC)):
                            $hasComments = true;
                        ?>
                            <div class="comment-card">
                                <div class="comment-header">
                                    <div class="comment-author">
                                        <i class="fas fa-user-circle"></i>
                                        <span><?php echo htmlspecialchars($comment['user_email']); ?></span>
                                    </div>
                                    <div class="comment-date">
                                        <i class="far fa-clock"></i>
                                        <span><?php echo date('M d, Y H:i', strtotime($comment['created_at'])); ?></span>
                                    </div>
                                </div>
                                <div class="comment-content">
                                    <?php echo nl2br(htmlspecialchars($comment['comment'])); ?>
                                </div>
                            </div>
                        <?php endwhile; ?>

                        <?php if (!$hasComments): ?>
                            <div class="no-comments">
                                <i class="far fa-comments"></i>
                                <p>No comments yet</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
