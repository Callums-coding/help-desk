<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit();
}

$db = getDbConnection();
$computers = $db->query('SELECT * FROM computers ORDER BY name ASC');
$totalComputers = $db->querySingle('SELECT COUNT(*) FROM computers');
$totalTickets = $db->querySingle('SELECT COUNT(*) FROM tickets');
$totalUsers = $db->querySingle('SELECT COUNT(*) FROM users');

$pageTitle = 'Manage Computers';
include 'admin_header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Computers - Admin Dashboard</title>
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
                <div class="content-section">
                    <div class="section-header">
                        <div class="section-title">
                            <h2>Computer List</h2>
                            <span class="count-badge"><?php echo $totalComputers; ?> total</span>
                        </div>
                        <a href="#" onclick="showAddComputerForm()" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Add Computer
                        </a>
                    </div>

                    <!-- Add Computer Form (Hidden by default) -->
                    <div id="addComputerForm" class="add-computer-form" style="display: none;">
                        <form action="add_computer.php" method="POST" class="form-container">
                            <div class="form-group">
                                <label for="computerName">Computer Name:</label>
                                <input type="text" id="computerName" name="name" required 
                                       placeholder="Enter computer name (e.g., B126B-1)" 
                                       class="form-input">
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Add Computer</button>
                                <button type="button" onclick="hideAddComputerForm()" class="button button-secondary">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="computers-grid">
                        <?php while ($computer = $computers->fetchArray(SQLITE3_ASSOC)): ?>
                            <div class="computer-card">
                                <div class="computer-info">
                                    <i class="fas fa-desktop"></i>
                                    <h3><?php echo htmlspecialchars($computer['name']); ?></h3>
                                </div>
                                <div class="computer-actions">
                                    <button onclick="removeComputer(<?php echo $computer['id']; ?>, '<?php echo htmlspecialchars($computer['name']); ?>')" 
                                            class="btn btn-danger">
                                        <i class="fas fa-trash"></i>
                                        Remove
                                    </button>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
    function showAddComputerForm() {
        document.getElementById('addComputerForm').style.display = 'block';
    }

    function hideAddComputerForm() {
        document.getElementById('addComputerForm').style.display = 'none';
    }

    function removeComputer(computerId, computerName) {
        if (confirm(`Are you sure you want to remove ${computerName}? This action cannot be undone.`)) {
            window.location.href = `remove_computer.php?id=${computerId}`;
        }
    }
    </script>
</body>
</html> 