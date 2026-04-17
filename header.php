<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<link rel="stylesheet" href="style.css">
<nav class="navbar">
    <div class="logo">
        <span style="color: #90caf9;">Found</span>IT
    </div>
    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="search.php">Search</a>
        <a href="report_item.php">Report</a>
        <a href="my_activities.php">My Posts</a>
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="logout.php" class="btn btn-primary" style="padding: 5px 15px; font-size: 11px;">Logout</a>
        <?php else: ?>
            <a href="login.php" class="btn btn-primary" style="padding: 5px 15px; font-size: 11px;">Login</a>
        <?php endif; ?>
    </div>
</nav>