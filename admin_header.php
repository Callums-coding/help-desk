<?php
// If page title isn't set, default to 'Admin Dashboard'
if (!isset($pageTitle)) {
    $pageTitle = 'Admin Dashboard';
}
?>
<div class="admin-header">
    <h1><?php echo $pageTitle; ?></h1>
    <div class="header-actions">
        <a href="/" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Back to Home
        </a>
        <a href="logout.php" class="btn btn-secondary">
            <i class="fas fa-sign-out-alt"></i>
            Logout
        </a>
    </div>
</div> 