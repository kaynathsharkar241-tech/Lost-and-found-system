<?php
include 'admin_check.php'; 
include 'db_connect.php';

// --- READ: Fetch Stats ---
$total_items_query = $conn->query("SELECT COUNT(*) as count FROM items");
$total_items = $total_items_query ? $total_items_query->fetch_assoc()['count'] : 0;

$pending_claims_query = $conn->query("SELECT COUNT(*) as count FROM claims WHERE status = 'pending'");
$pending_claims = $pending_claims_query ? $pending_claims_query->fetch_assoc()['count'] : 0;

// --- READ: Fetch Pending Claims ---
$claims_sql = "SELECT c.claim_id, c.proof_description, u.full_name as claimant, i.title, i.item_id, img.image_url 
               FROM claims c 
               JOIN users u ON c.claimed_by = u.user_id 
               JOIN items i ON c.item_id = i.item_id 
               LEFT JOIN itemimages img ON i.item_id = img.item_id 
               WHERE c.status = 'pending'";
$claims_result = $conn->query($claims_sql);

// --- READ: Fetch All Items ---
$all_items_sql = "SELECT i.*, c.category_name FROM items i 
                  JOIN categories c ON i.category_id = c.category_id 
                  ORDER BY i.date_reported DESC";
$all_items_result = $conn->query($all_items_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Lost & Found</title>
    <style>
        /* Background matches User Dashboard */
body {
    margin: 0;
    font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    /* This creates the dark blue to light blue to white transition */
    background: linear-gradient(180deg, #1a2a6c 0%, #b21f1f 0%, #fdbb2d 0%, #224488 0%, #3a7bd5 40%, #ffffff 100%);
    background-attachment: fixed; /* Keeps the gradient still while you scroll */
    min-height: 100vh;
    color: #333;
}

        /* Clean Header with ONLY Logout */
        header {
            background-color: #34495e;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }

        .header-logo { font-size: 20px; font-weight: bold; }

        .btn-logout {
            background-color: #e74c3c;
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 14px;
        }

        .btn-logout:hover { background-color: #c0392b; }

        .admin-container { 
            max-width: 1000px; 
            margin: 40px auto; 
            padding: 0 20px; 
        }

        /* Stats Styling */
        .stats-container { 
            display: flex; 
            gap: 20px; 
            margin-bottom: 30px; 
            flex-wrap: wrap; 
        }

        .stat-box { 
            background: white; 
            padding: 20px; 
            border-radius: 10px; 
            flex: 1; 
            text-align: center; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); 
            min-width: 150px; 
        }

        .stat-box h2 { font-size: 30px; margin: 10px 0; color: #1a2a6c; }
        .stat-box h3 { font-size: 16px; color: #7f8c8d; margin: 0; }

        /* Table Styling */
        .admin-table { 
            width: 100%; 
            background: white; 
            border-collapse: collapse; 
            border-radius: 10px; 
            overflow: hidden; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); 
            margin-bottom: 30px; 
        }

        .admin-table th, .admin-table td { 
            padding: 12px; 
            text-align: left; 
            border-bottom: 1px solid #ddd; 
        }

        .admin-table th { background-color: #34495e; color: white; }

        /* General Buttons */
        .btn { 
            display: inline-block; 
            padding: 8px 15px; 
            color: white; 
            text-decoration: none; 
            border-radius: 5px; 
            font-size: 14px; 
            font-weight: bold; 
        }

        .btn-approve { background-color: #2ecc71; margin-right: 5px; }
        .btn-reject { background-color: #f39c12; }
        
        /* PINK DELETE BUTTON */
        .btn-delete { background-color: #ff69b4; } 
        .btn-delete:hover { background-color: #ff1493; }

        .thumb { 
            width: 50px; 
            height: 50px; 
            object-fit: cover; 
            border-radius: 5px; 
        }

        .status-found { color: #2ecc71; font-weight: bold; }
        .status-lost { color: #e74c3c; font-weight: bold; }
        
        h1, h3 { color: #2c3e50; }
    </style>
</head>
<body>

<header>
    <div class="header-logo">Admin Portal</div>
    <nav>
        <a href="logout.php" class="btn-logout">Logout</a>
    </nav>
</header>

<div class="admin-container">
    <h1>Control Dashboard</h1>

    <div class="stats-container">
        <div class="stat-box">
            <h3>Total Database Items</h3>
            <h2><?php echo $total_items; ?></h2>
        </div>
        <div class="stat-box">
            <h3>Pending Claims</h3>
            <h2 style="color: #f39c12;"><?php echo $pending_claims; ?></h2>
        </div>
    </div>

    <h3>Pending Claim Requests</h3>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Item</th>
                <th>Claimant</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($claims_result && $claims_result->num_rows > 0): ?>
                <?php while($row = $claims_result->fetch_assoc()): ?>
                <tr>
                    <td>
                        <img src="uploads/<?php echo htmlspecialchars($row['image_url']); ?>" 
                             class="thumb" 
                             onerror="this.src='https://via.placeholder.com/50';">
                    </td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['claimant']); ?></td>
                    <td>
                        <a href="process_claim.php?id=<?php echo $row['claim_id']; ?>&item=<?php echo $row['item_id']; ?>&action=approve" class="btn btn-approve">Approve</a>
                        <a href="process_claim.php?id=<?php echo $row['claim_id']; ?>&item=<?php echo $row['item_id']; ?>&action=reject" class="btn btn-reject">Reject</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4" style="text-align:center; padding: 20px; color: #7f8c8d;">No pending claims found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h3>All Reported Items</h3>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Category</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($all_items_result && $all_items_result->num_rows > 0): ?>
                <?php while($item = $all_items_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['title']); ?></td>
                    <td><?php echo htmlspecialchars($item['category_name']); ?></td>
                    <td>
                        <span class="<?php echo ($item['status'] == 'found') ? 'status-found' : 'status-lost'; ?>">
                            <?php echo ucfirst($item['status']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="delete_item.php?id=<?php echo $item['item_id']; ?>" 
                           class="btn btn-delete" 
                           onclick="return confirm('Permanently delete this item?');">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4" style="text-align:center; padding: 20px; color: #7f8c8d;">No items in the database.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
