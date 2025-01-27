<?php
// db.php

// Connect to SQLite Database
function getDbConnection() {
    $db = new SQLite3(__DIR__ . '/tickets.db'); // Use __DIR__ for the absolute path
    return $db;
}

// Create users and tickets tables if they don't exist
function createTables() {
    $db = getDbConnection();

    // Drop existing users table
    $db->exec("DROP TABLE IF EXISTS users");

    // Create users table with is_admin column
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        email TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        is_admin INTEGER DEFAULT 0
    )");

    // Create tickets table
    $db->exec("CREATE TABLE IF NOT EXISTS tickets (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        title TEXT NOT NULL,
        description TEXT NOT NULL,
        status TEXT NOT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");

    // Create comments table
    $db->exec("CREATE TABLE IF NOT EXISTS comments (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        ticket_id INTEGER NOT NULL,
        user_id INTEGER NOT NULL,
        comment TEXT NOT NULL,
        created_at DATETIME NOT NULL,
        FOREIGN KEY (ticket_id) REFERENCES tickets(id),
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");
}

// Add this function to create an admin user
function createAdminUser($email, $password) {
    $db = getDbConnection();
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $db->prepare('INSERT OR IGNORE INTO users (email, password, is_admin) VALUES (?, ?, 1)');
    $stmt->bindValue(1, $email, SQLITE3_TEXT);
    $stmt->bindValue(2, $hashedPassword, SQLITE3_TEXT);
    $stmt->execute();
}
?>
