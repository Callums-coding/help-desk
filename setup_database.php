<?php
include('db.php');

try {
    $db = getDbConnection();
    
    // Drop existing tables to ensure clean setup
    $db->exec('DROP TABLE IF EXISTS comments');
    $db->exec('DROP TABLE IF EXISTS tickets');
    $db->exec('DROP TABLE IF EXISTS computers');
    $db->exec('DROP TABLE IF EXISTS users');
    
    // Create users table
    $db->exec('
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            is_admin INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ');

    // Create computers table
    $db->exec('
        CREATE TABLE IF NOT EXISTS computers (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL UNIQUE,
            status TEXT DEFAULT "active",
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ');

    // Create tickets table with computer_id
    $db->exec('
        CREATE TABLE IF NOT EXISTS tickets (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            computer_id INTEGER,
            title TEXT NOT NULL,
            description TEXT NOT NULL,
            status TEXT DEFAULT "open",
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id),
            FOREIGN KEY (computer_id) REFERENCES computers(id)
        )
    ');

    // Create comments table
    $db->exec('
        CREATE TABLE IF NOT EXISTS comments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            ticket_id INTEGER NOT NULL,
            user_id INTEGER NOT NULL,
            comment TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (ticket_id) REFERENCES tickets(id),
            FOREIGN KEY (user_id) REFERENCES users(id)
        )
    ');

    // Insert your admin account
    $password = password_hash('admin', PASSWORD_DEFAULT);
    $db->exec("
        INSERT INTO users (email, password, is_admin) 
        VALUES ('708003@student.centralbeds.ac.uk', '$password', 1)
    ");

    // Insert default computers
    $defaultComputers = [
        'B126B-1', 'B126B-2', 'B126B-3', 'B126B-4', 'B126B-5',
        'B126B-6', 'B126B-7', 'B126B-8', 'B126B-9', 'B126B-10',
        'B126B-11', 'B126B-12', 'B126B-13', 'B126B-14', 'B126B-15',
        'B126B-STAFF'
    ];

    foreach ($defaultComputers as $computer) {
        $db->exec("
            INSERT OR IGNORE INTO computers (name) 
            VALUES ('$computer')
        ");
    }

    echo "Database setup completed successfully!";
    echo "\nAdmin credentials:";
    echo "\nEmail: 708003@student.centralbeds.ac.uk";
    echo "\nPassword: admin";

} catch (Exception $e) {
    echo "Error setting up database: " . $e->getMessage();
}
?> 