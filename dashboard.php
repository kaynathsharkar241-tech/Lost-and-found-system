<?php include 'db_connect.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard - Lost & Found</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .stats-container { display: flex; gap: 20px; margin-bottom: 30px; flex-wrap: wrap; }
        .stat-box { background: white; padding: 20px; border-radius: 10px; flex: 1; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); min-width: 150px; }
        .stat-box h2 { font-size: 30px; margin: 10px 0; }
        .lost-text { color: #e74c3c; }
        .found-text { color: #2ecc71; }
        .pending-text { color: #f39c12; }
        .matched-text { color: #3498db; }
        .activity-table { width: 100%; background: white; border-collapse: collapse; border-radius: 10px; overflow: hidden; }
        .activity-table th, .activity-table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        .activity-table th { background-color: #34495e; color: white; }
        .btn { display: inline-block; padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; margin-top: 10px; }
        .cat-table { width: 100%; background: white; border-collapse: collapse; margin-top: 30px; border-radius: 10px; overflow: hidden; }
        .cat-table th, .cat-table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        .cat-table th { background-color: #34495e; color: white; }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<?php
// Total lost items
$lost_result = $conn->query("SELECT COUNT(*) as total FROM items WHERE status = 'lost'");
$lost_data = $lost_result->fetch_assoc();

// Total found items
$found_result = $conn->query("SELECT COUNT(*) as total FROM items WHERE status = 'found'");
$found_data = $found_result->fetch_assoc();

// Pending claims
$pending_result = $conn->query("SELECT COUNT(*) as total FROM claims WHERE status = 'pending'");
$pending_data = $pending_result->fetch_assoc();

// Successful matches
$matched_result = $conn->query("SELECT COUNT(*) as total FROM claims WHERE status = 'approved'");
$matched_data = $matched_result->fetch_assoc();

// Items by category
$cat_result = $conn->query("SELECT c.category_name, COUNT(i.item_id) AS total_items
    FROM categories c
    LEFT JOIN items i ON c.category_id = i.category_id
    GROUP BY c.category_id, c.category_name
    ORDER BY total_items DESC");

// Recent activity
$recent_result = $conn->query("SELECT title, status, date_reported 
    FROM items 
    ORDER BY date_reported DESC 
    LIMIT 5");
?>

<div style="max-width: 900px; margin: 40px auto; padding: 0 20px;">

    <h1>Welcome to the User Portal</h1>

    <!-- Stats -->
    <div class="stats-container">
        <div class="stat-box">
            <h3>Total Lost</h3>
            <h2 class="lost-text"><?php echo $lost_data['total']; ?></h2>
        </div>
        <div class="stat-box">
            <h3>Total Found</h3>
            <h2 class="found-text"><?php echo $found_data['total']; ?></h2>
        </div>
        <div class="stat-box">
            <h3>Pending Claims</h3>
            <h2 class="pending-text"><?php echo $pending_data['total']; ?></h2>
        </div>
        <div class="stat-box">
            <h3>Successful Matches</h3>
            <h2 class="matched-text"><?php echo $matched_data['total']; ?></h2>
        </div>
    </div>

    <!-- Buttons -->
    <div>
        <a href="report_item.php" class="btn" style="background-color: #e67e22;">Report New Item</a>
        <a href="search.php" class="btn">Browse All Items</a>
    </div>

    <br>

    <!-- Recent Activity -->
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

    <!-- Items by Category -->
    <h3 style="margin-top: 30px;">Items by Category</h3>
    <table class="cat-table">
        <thead>
            <tr>
                <th>Category</th>
                <th>Total Items</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $cat_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['category_name']; ?></td>
                <td><?php echo $row['total_items']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</div>

</body>
</html>
