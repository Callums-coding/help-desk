<?php
session_start();
$error_code = $_GET['code'] ?? '404';
$error_message = '';
$error_description = '';
$error_action = '';

switch ($error_code) {
    case 'missing_fields':
        $error_message = 'Required Fields Missing';
        $error_description = 'Please ensure all required fields are filled out before submitting.';
        $error_action = 'Go back and complete all fields.';
        break;
    case 'database_error':
        $error_message = 'Database Error';
        $error_description = 'There was an error processing your request in our database.';
        $error_action = 'Please try again or contact support if the problem persists.';
        break;
    case 'system_error':
        $error_message = 'System Error';
        $error_description = 'An unexpected system error has occurred.';
        $error_action = 'Please try again later or contact support if the problem persists.';
        break;
    case '403':
        $error_message = 'Access Denied';
        $error_description = 'You do not have permission to access this resource.';
        $error_action = 'Please log in with appropriate credentials.';
        break;
    case '404':
    default:
        $error_message = 'Page Not Found';
        $error_description = 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.';
        $error_action = 'Please check the URL or navigate back to the dashboard.';
        break;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - Support System</title>
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
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="header-actions">
                        <a href="index.php" class="header-button">
                            <i class="fas fa-home"></i>
                            <span>Back to Dashboard</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </header>

        <main class="main-content">
            <div class="error-container">
                <div class="error-icon">
                    <?php if ($error_code === '404'): ?>
                        <i class="fas fa-search"></i>
                    <?php elseif ($error_code === '403'): ?>
                        <i class="fas fa-lock"></i>
                    <?php elseif ($error_code === 'database_error'): ?>
                        <i class="fas fa-database"></i>
                    <?php elseif ($error_code === 'missing_fields'): ?>
                        <i class="fas fa-exclamation-circle"></i>
                    <?php else: ?>
                        <i class="fas fa-exclamation-triangle"></i>
                    <?php endif; ?>
                </div>

                <h2 class="error-title"><?php echo htmlspecialchars($error_message); ?></h2>
                <p class="error-description"><?php echo htmlspecialchars($error_description); ?></p>
                <p class="error-action"><?php echo htmlspecialchars($error_action); ?></p>

                <div class="error-buttons">
                    <?php if (isset($_SERVER['HTTP_REFERER'])): ?>
                        <a href="<?php echo htmlspecialchars($_SERVER['HTTP_REFERER']); ?>" class="button button-secondary">
                            <i class="fas fa-arrow-left"></i>
                            <span>Go Back</span>
                        </a>
                    <?php endif; ?>
                    
                    <a href="index.php" class="button button-primary">
                        <i class="fas fa-home"></i>
                        <span>Return to Dashboard</span>
                    </a>
                </div>
            </div>
        </main>
    </div>
</body>
</html> 