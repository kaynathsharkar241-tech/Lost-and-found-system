<?php
session_start();
include 'db_connect.php';


$current_user_id = 1; 

// Items reported by this user
$my_items_query = "SELECT i.item_id, i.title, i.description, 
                          i.status, i.date_reported, c.category_name
                   FROM items i
                   JOIN categories c ON i.category_id = c.category_id
                   WHERE i.reported_by = $current_user_id
                   ORDER BY i.date_reported DESC";
$items_result = $conn->query($my_items_query);

// Claims made by this user
$my_claims_query = "SELECT cl.claim_id, cl.claim_date, cl.status AS claim_status,
                           cl.proof_description, i.title AS item_title
                    FROM claims cl
                    JOIN items i ON cl.item_id = i.item_id
                    WHERE cl.claimed_by = $current_user_id
                    ORDER BY cl.claim_date DESC";
$claims_result = $conn->query($my_claims_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Activities - Lost & Found</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background-color: #f9f9f9; }
        h1 { color: #2c3e50; }
        h2 { color: #34495e; margin-top: 30px; border-bottom: 2px solid #ddd; padding-bottom: 8px; }
        .post-card { background: white; border-left: 5px solid #3498db; padding: 15px; margin-bottom: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border-radius: 5px; }
        .claim-card { background: white; border-left: 5px solid #e67e22; padding: 15px; margin-bottom: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border-radius: 5px; }
        .status-tag { padding: 5px 10px; border-radius: 4px; font-size: 12px; text-transform: uppercase; font-weight: bold; }
        .lost { background: #ffdada; color: #cc0000; }
        .found { background: #d4f8d4; color: #006600; }
        .pending { background: #fff3cd; color: #856404; }
        .approved { background: #d4f8d4; color: #006600; }
        .rejected { background: #ffdada; color: #cc0000; }
        .nav-links { margin-bottom: 20px; }
        .nav-links a { color: #3498db; text-decoration: none; }
        .btn { display: inline-block; padding: 6px 14px; border-radius: 5px; font-size: 13px; cursor: pointer; border: none; }
        .btn-edit { background: #3498db; color: white; }
        .btn-delete { background: #e74c3c; color: white; }
        .empty-msg { color: #888; font-style: italic; }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div style="max-width: 900px; margin: 40px auto; padding: 0 20px;">

    <div class="nav-links">
        <a href="dashboard.php">⬅ Dashboard</a> | <b>My Activities</b>
    </div>

    <h1>My Activities</h1>
    <p>Everything you have personally posted or claimed on the system.</p>

    <!-- My Reported Items -->
    <h2>My Reported Items</h2>
    <div class="posts-list">
        <?php if ($items_result && $items_result->num_rows > 0): ?>
            <?php while($row = $items_result->fetch_assoc()): ?>
                <div class="post-card">
                    <h3><?php echo $row['title']; ?></h3>
                    <p>
                        <span class="status-tag <?php echo $row['status']; ?>">
                            <?php echo ucfirst($row['status']); ?>
                        </span>
                        &nbsp;| Category: <?php echo $row['category_name']; ?>
                        &nbsp;| Reported on: <?php echo $row['date_reported']; ?>
                    </p>
                    <p><?php echo $row['description']; ?></p>
                    <button class="btn btn-edit" onclick="alert('Edit feature coming soon!')">Edit</button>
                    <button class="btn btn-delete" onclick="alert('Delete feature coming soon!')">Delete</button>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="empty-msg">You haven't reported any items yet. 
                <a href="report_item.php">Report your first item!</a>
            </p>
        <?php endif; ?>
    </div>

    <!-- My Claims -->
    <h2>My Claims</h2>
    <div class="claims-list">
        <?php if ($claims_result && $claims_result->num_rows > 0): ?>
            <?php while($row = $claims_result->fetch_assoc()): ?>
                <div class="claim-card">
                    <h3><?php echo $row['item_title']; ?></h3>
                    <p>
                        Claim Status: 
                        <span class="status-tag <?php echo $row['claim_status']; ?>">
                            <?php echo ucfirst($row['claim_status']); ?>
                        </span>
                        &nbsp;| Claimed on: <?php echo $row['claim_date']; ?>
                    </p>
                    <p>Proof submitted: <?php echo $row['proof_description']; ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="empty-msg">You haven't made any claims yet.</p>
        <?php endif; ?>
    </div>

</div>

</body>
</html>