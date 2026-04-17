<?php
include 'db_connect.php';

// --- DATA IMPLEMENTATION: Specific User Filter ---

$current_user_id = 1; 

// Query to get ONLY items reported by this specific user
$my_items_query = "SELECT * FROM items WHERE user_id = $current_user_id ORDER BY date_reported DESC";
$result = $conn->query($my_items_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Activities - Lost & Found</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background-color: #f9f9f9; }
        .post-card { background: white; border-left: 5px solid #3498db; padding: 15px; margin-bottom: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .status-tag { padding: 5px 10px; border-radius: 4px; font-size: 12px; text-transform: uppercase; }
        .lost { background: #ffdada; color: #cc0000; }
        .found { background: #d4f8d4; color: #006600; }
        .nav-links { margin-bottom: 20px; }
    </style>
</head>
<body>

    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a> | <b>My Activities</b>
    </div>

    <h1>My Reported Items</h1>
    <p>This page shows all the items you personally have posted to the system.</p>

    <div class="posts-list">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="post-card">
                    <h3><?php echo $row['title']; ?></h3>
                    <p>
                        <span class="status-tag <?php echo $row['status']; ?>">
                            <?php echo $row['status']; ?>
                        </span>
                        | Reported on: <?php echo $row['date_reported']; ?>
                    </p>
                    <p><?php echo $row['description']; ?></p>
                    
                    <button onclick="alert('Edit feature coming soon!')">Edit Post</button>
                    <button style="color:red;">Delete</button>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>You haven't reported any items yet.</p>
            <a href="report_item.php">Click here to report your first item!</a>
        <?php endif; ?>
    </div>

</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <title>Register - FoundIT</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div style="display: flex; justify-content: center; align-items: center; min-height: 100vh;">
        
        
    </div>
</body>
</html