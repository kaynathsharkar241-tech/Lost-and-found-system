<?php
session_start();
include 'db_connect.php';

$current_user_id = 1; 

// Fetch all notifications for this user 
$notif_query = "SELECT notification_id, message, is_read, created_at
                FROM notifications
                WHERE user_id = $current_user_id
                ORDER BY is_read ASC, created_at DESC";
$notif_result = $conn->query($notif_query);

// Count unread notifications
$unread_query = "SELECT COUNT(*) as total FROM notifications 
                 WHERE user_id = $current_user_id AND is_read = 0";
$unread_result = $conn->query($unread_query);
$unread_data = $unread_result->fetch_assoc();

// Mark all as read when user visits this page
$conn->query("UPDATE notifications SET is_read = 1 WHERE user_id = $current_user_id");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Notifications - Lost & Found</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { max-width: 800px; margin: 40px auto; padding: 0 20px; }
        h1 { color: #2c3e50; }
        .badge { display: inline-block; background: #e74c3c; color: white; 
                 border-radius: 50%; padding: 2px 8px; font-size: 13px; margin-left: 8px; }
        .notif-card { background: white; padding: 15px 20px; margin-bottom: 10px; 
                      border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.08);
                      border-left: 5px solid #bdc3c7; }
        .notif-card.unread { border-left: 5px solid #3498db; background: #f0f8ff; }
        .notif-message { font-size: 15px; color: #2c3e50; margin-bottom: 6px; }
        .notif-time { font-size: 12px; color: #888; }
        .unread-label { font-size: 11px; background: #3498db; color: white; 
                        padding: 2px 8px; border-radius: 4px; margin-left: 8px; }
        .empty-msg { color: #888; font-style: italic; text-align: center; 
                     padding: 40px 0; }
        .nav-links { margin-bottom: 20px; }
        .nav-links a { color: #3498db; text-decoration: none; }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">

    <div class="nav-links">
        <a href="dashboard.php">⬅ Back to Dashboard</a>
    </div>

    <h1>
        Notifications
        <?php if ($unread_data['total'] > 0): ?>
            <span class="badge"><?php echo $unread_data['total']; ?></span>
        <?php endif; ?>
    </h1>

    <?php if ($notif_result && $notif_result->num_rows > 0): ?>
        <?php while($row = $notif_result->fetch_assoc()): ?>
            <div class="notif-card <?php echo $row['is_read'] == 0 ? 'unread' : ''; ?>">
                <div class="notif-message">
                    <?php echo $row['message']; ?>
                    <?php if ($row['is_read'] == 0): ?>
                        <span class="unread-label">New</span>
                    <?php endif; ?>
                </div>
                <div class="notif-time">
                    <?php echo date('d M Y, h:i A', strtotime($row['created_at'])); ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="empty-msg">You have no notifications yet.</div>
    <?php endif; ?>

</div>

</body>
</html>