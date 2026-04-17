<?php include 'header.php'; ?>

<div style="max-width: 900px; margin: 40px auto; padding: 0 20px;">
    <div class="card">
<?php
include 'db_connect.php';


// --- DATA IMPLEMENTATION: Dashboard Statistics ---
// 1. Count Total Lost Items
$lost_count_query = "SELECT COUNT(*) as total FROM items WHERE status = 'lost'";
$lost_result = $conn->query($lost_count_query);
$lost_data = $lost_result->fetch_assoc();

// 2. Count Total Found Items
$found_count_query = "SELECT COUNT(*) as total FROM items WHERE status = 'found'";
$found_result = $conn->query($found_count_query);
$found_data = $found_result->fetch_assoc();

// 3. Fetch Recent Activity (Last 5 items reported)
$recent_query = "SELECT title, status, date_reported FROM items ORDER BY date_reported DESC LIMIT 5";
$recent_result = $conn->query($recent_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard - Lost & Found</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .stats-container { display: flex; gap: 20px; margin-bottom: 30px; }
        .stat-box { background: white; padding: 20px; border-radius: 10px; flex: 1; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .stat-box h2 { font-size: 30px; margin: 10px 0; }
        .lost-text { color: #e74c3c; }
        .found-text { color: #2ecc71; }
        .activity-table { width: 100%; background: white; border-collapse: collapse; border-radius: 10px; overflow: hidden; }
        .activity-table th, .activity-table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        .activity-table th { background-color: #34495e; color: white; }
        .btn { display: inline-block; padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; margin-top: 10px; }
    </style>
</head>
<body>

    <h1>Welcome to the User Portal</h1>
    
    <div class="stats-container">
        <div class="stat-box">
            <h3>Total Lost Items</h3>
            <h2 class="lost-text"><?php echo $lost_data['total']; ?></h2>
        </div>
        <div class="stat-box">
            <h3>Total Found Items</h3>
            <h2 class="found-text"><?php echo $found_data['total']; ?></h2>
        </div>
    </div>

    <div>
        <a href="report_item.php" class="btn" style="background-color: #e67e22;">Report New Item</a>
        <a href="search.php" class="btn">Browse All Items</a>
    </div>

    <br>

    <h3>Recent Community Activity</h3>
    <table class="activity-table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $recent_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['title']; ?></td>
                <td><strong><?php echo ucfirst($row['status']); ?></strong></td>
                <td><?php echo $row['date_reported']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>
</html>
</div> </div> ```
        </div> 
</div>