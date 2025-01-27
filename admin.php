<?php
// admin.php
$pageTitle = 'Admin Dashboard';
include 'admin_header.php';

include('db.php');
session_start();

// Check if user is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: /login');
    exit();
}

// Get the current active tab
$activeTab = $_GET['tab'] ?? 'dashboard';

// Get filters
$ticketStatus = $_GET['status'] ?? 'all';
$userType = $_GET['type'] ?? 'all';

$db = getDbConnection();

// Get statistics for dashboard
$totalTickets = $db->querySingle('SELECT COUNT(*) FROM tickets');
$totalUsers = $db->querySingle('SELECT COUNT(*) FROM users');
$totalComputers = $db->querySingle('SELECT COUNT(*) FROM computers WHERE status = "active"');
$openTickets = $db->querySingle('SELECT COUNT(*) FROM tickets WHERE status = "open"');

// Get tickets for tickets tab
$ticketQuery = '
    SELECT 
        tickets.*,
        computers.name as computer_name,
        users.email as user_email
    FROM tickets 
    LEFT JOIN computers ON tickets.computer_id = computers.id
    LEFT JOIN users ON tickets.user_id = users.id
';

if ($ticketStatus != 'all') {
    $ticketQuery .= ' WHERE tickets.status = "' . SQLite3::escapeString($ticketStatus) . '"';
}
$ticketQuery .= ' ORDER BY tickets.created_at DESC';
$tickets = $db->query($ticketQuery);

// Get users for users tab
$userQuery = 'SELECT * FROM users ORDER BY created_at DESC';
$users = $db->query($userQuery);

// Get computers for computers tab
$computerQuery = 'SELECT * FROM computers WHERE status = "active" ORDER BY name';
$computers = $db->query($computerQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="icon" href="https://bedfordcollegegroup.ac.uk/hideout-app/themes/the-hideout-theme-group/img/themes/bedford-college/favicon.png">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="admin-body">
    <div class="admin-container">
        <div class="admin-layout">
            <?php include 'admin_sidebar.php'; ?>

            <main class="admin-content">
                <div class="content-section">
                    <div class="stat-grid">
                        <div class="stat-card">
                            <h3>Total Tickets</h3>
                            <p class="stat-number"><?php echo $totalTickets; ?></p>
                        </div>
                        <div class="stat-card">
                            <h3>Total Users</h3>
                            <p class="stat-number"><?php echo $totalUsers; ?></p>
                        </div>
                        <div class="stat-card">
                            <h3>Total Computers</h3>
                            <p class="stat-number"><?php echo $totalComputers; ?></p>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        function showCreateUserModal() {
            document.getElementById('createUserModal').style.display = 'block';
        }

        function hideCreateUserModal() {
            document.getElementById('createUserModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('createUserModal');
            if (event.target === modal) {
                hideCreateUserModal();
            }
        }

        function deleteTicket(ticketId) {
            if (confirm('Are you sure you want to delete this ticket? This action cannot be undone.')) {
                window.location.href = `delete_ticket.php?id=${ticketId}`;
            }
        }

        function filterTickets(status) {
            const rows = document.querySelectorAll('.ticket-row');
            rows.forEach(row => {
                if (status === 'all' || row.dataset.status === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>
