<?php
// register.php

include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert the new user into the database
    $db = getDbConnection();
    $stmt = $db->prepare('INSERT INTO users (email, password) VALUES (?, ?)');
    $stmt->bindValue(1, $email, SQLITE3_TEXT);
    $stmt->bindValue(2, $hashedPassword, SQLITE3_TEXT);
    $stmt->execute();
    
    echo "Registration successful! You can now <a href='/login'>login</a>.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="https://bedfordcollegegroup.ac.uk/hideout-app/themes/the-hideout-theme-group/img/themes/bedford-college/favicon.png">
</head>
<body>

<h1>Register</h1>
<form method="POST">
    <label for="email">Email:</label><br>
    <input type="email" id="email" name="email" required><br><br>

    <label for="password">Password:</label><br>
    <input type="password" id="password" name="password" required><br><br>

    <input type="submit" value="Register">
</form>

<a href="/login">Already have an account? Login here</a>

</body>
</html>
