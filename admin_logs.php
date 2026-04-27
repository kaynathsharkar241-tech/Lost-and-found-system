<?php
include 'admin_check.php';
include 'db_connect.php';


$log_sql = "SELECT a.action_type, a.action_date, u.full_name as admin_name, i.title as item_name 
            FROM adminactions a 
            JOIN users u ON a.admin_id = u.user_id 
            JOIN items i ON a.item_id = i.item_id 
            ORDER BY a.action_id DESC";
$logs = $conn->query($log_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Logs - FoundIT</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="card" style="margin:20px;">
        <h2>Admin Audit Trail</h2>
        <table style="width:100%; border-collapse: collapse;">
            <tr style="text-align:left; background:#f4f4f4;">
                <th>Date</th><th>Admin</th><th>Action</th><th>Item Affected</th>
            </tr>
            <?php while($row = $logs->fetch_assoc()): ?>
            <tr style="border-bottom:1px solid #ddd;">
                <td><?php echo $row['action_date']; ?></td>
                <td><?php echo $row['admin_name']; ?></td>
                <td><strong><?php echo $row['action_type']; ?></strong></td>
                <td><?php echo $row['item_name']; ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
        <br>
        <a href="admin_dashboard.php" class="btn">Back to Dashboard</a>
    </div>
</body>
</html>