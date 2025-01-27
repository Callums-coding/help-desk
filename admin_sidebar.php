<?php
// Get counts for badges
$totalTickets = $db->querySingle('SELECT COUNT(*) FROM tickets');
$totalUsers = $db->querySingle('SELECT COUNT(*) FROM users');
$totalComputers = $db->querySingle('SELECT COUNT(*) FROM computers');

// Determine which page is active
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>

<aside class="sidebar">
    <nav class="sidebar-nav">
        <a href="admin.php" class="sidebar-link <?php echo $currentPage === 'admin' ? 'active' : ''; ?>">
            <i class="fas fa-chart-bar"></i>
            Overview
        </a>
        <a href="manage_tickets.php" class="sidebar-link <?php echo $currentPage === 'manage_tickets' ? 'active' : ''; ?>">
            <i class="fas fa-ticket-alt"></i>
            Tickets
            <?php if ($totalTickets > 0): ?>
                <span class="badge"><?php echo $totalTickets; ?></span>
            <?php endif; ?>
        </a>
        <a href="manage_users.php" class="sidebar-link <?php echo $currentPage === 'manage_users' ? 'active' : ''; ?>">
            <i class="fas fa-users"></i>
            Users
            <?php if ($totalUsers > 0): ?>
                <span class="badge"><?php echo $totalUsers; ?></span>
            <?php endif; ?>
        </a>
        <a href="manage_computers.php" class="sidebar-link <?php echo $currentPage === 'manage_computers' ? 'active' : ''; ?>">
            <i class="fas fa-desktop"></i>
            Computers
            <?php if ($totalComputers > 0): ?>
                <span class="badge"><?php echo $totalComputers; ?></span>
            <?php endif; ?>
        </a>
    </nav>
</aside> 