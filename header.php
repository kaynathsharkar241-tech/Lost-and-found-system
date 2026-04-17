<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<link rel="stylesheet" href="style.css">
<style>
    .nav-links {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
    }
    .nav-links a {
        display: inline-block;
    }
</style>
<nav class="navbar">
    <div class="logo">
        <span style="color: #90caf9;">Found</span>IT
    </div>
    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="search.php">Search</a>
        <a href="report_item.php">Report</a>
        <a href="my_activities.php">My Posts</a>
        <a href="notifications.php">Notification</a>
        <a href="matches.php" style="background-color: #27ae60; color: white; padding: 6px 12px; border-radius: 5px; text-decoration: none; font-size: 13px;">View Matches</a>
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="logout.php" style="background-color: #e74c3c; color: white; padding: 6px 12px; border-radius: 5px; text-decoration: none; font-size: 13px;">Logout</a>
        <?php else: ?>
            <a href="login.php" style="background-color: #1a2a6c; color: white; padding: 6px 12px; border-radius: 5px; text-decoration: none; font-size: 13px;">Login</a>
        <?php endif; ?>
    </div>
</nav>