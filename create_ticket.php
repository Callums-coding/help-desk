<?php
session_start();
include('db.php');

// create_ticket.php

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$db = getDbConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $userId = $_SESSION['user_id'];

    $stmt = $db->prepare('INSERT INTO tickets (user_id, title, description, status, created_at, updated_at) VALUES (?, ?, ?, "open", DATETIME("now"), DATETIME("now"))');
    $stmt->bindValue(1, $userId, SQLITE3_INTEGER);
    $stmt->bindValue(2, $title, SQLITE3_TEXT);
    $stmt->bindValue(3, $description, SQLITE3_TEXT);
    $stmt->execute();

    echo "Ticket created successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Ticket - Support System</title>
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
                    <a href="index.php" class="header-button">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back to Dashboard</span>
                    </a>
                </div>
            </div>
        </header>

        <main class="main-content">
            <div class="content-header">
                <h2>Create New Ticket</h2>
            </div>

            <div class="form-container">
                <form action="submit_ticket.php" method="POST" class="ticket-form">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" id="title" name="title" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label for="computer_id">Select Computer</label>
                        <div class="select-wrapper">
                            <select id="computer_id" name="computer_id" class="form-input" required>
                                <option value="">Select a computer</option>
                                <?php
                                $computers = $db->query('SELECT * FROM computers WHERE status = "active" ORDER BY name');
                                while ($computer = $computers->fetchArray(SQLITE3_ASSOC)) {
                                    echo '<option value="' . $computer['id'] . '">' . htmlspecialchars($computer['name']) . '</option>';
                                }
                                ?>
                            </select>
                            <i class="fas fa-chevron-down select-arrow"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" class="form-input" rows="6" required></textarea>
                    </div>

                    <div class="form-actions">
                        <a href="index.php" class="button button-secondary">Cancel</a>
                        <button type="submit" class="button button-primary">Create Ticket</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
