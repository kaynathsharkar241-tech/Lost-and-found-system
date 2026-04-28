<?php
session_start();
include 'db_connect.php';

// 1. Security: Ensure only logged-in users can report items
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];
$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Sanitize inputs
    $title       = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $status      = $conn->real_escape_string($_POST['status']); // 'lost' or 'found'
    $category_id = (int)$_POST['category_id'];
    $location_id = (int)$_POST['location_id'];

    // 2. Image upload handling
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_ext    = strtolower(pathinfo($_FILES["item_image"]["name"], PATHINFO_EXTENSION));
    $file_name   = time() . "_" . uniqid() . "." . $file_ext; 
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($_FILES["item_image"]["tmp_name"], $target_file)) {

        // 3. Database Transaction: Insert into Items
        $sql = "INSERT INTO items (title, description, category_id, reported_by, status, date_reported) 
                VALUES ('$title', '$description', '$category_id', '$current_user_id', '$status', CURDATE())";

        if ($conn->query($sql) === TRUE) {
            $last_item_id = $conn->insert_id;

            // 4. Insert into ItemImages table
            $img_sql = "INSERT INTO itemimages (item_id, image_url) 
                        VALUES ('$last_item_id', '$file_name')";
            $conn->query($img_sql);

            // 5. Insert into Reports table (linking item to location)
            $report_sql = "INSERT INTO reports (item_id, location_id, report_type, report_date, details)
                           VALUES ('$last_item_id', '$location_id', '$status', CURDATE(), '$description')";
            $conn->query($report_sql);

            $success = "Item reported successfully! It is now pending review.";
        } else {
            $error = "Database Error: " . $conn->error;
        }
    } else {
        $error = "Failed to upload image. Check folder permissions.";
    }
}

// Fetch data for dropdowns
$cat_result = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_name");
$loc_result = $conn->query("SELECT location_id, place_name FROM locations ORDER BY place_name");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Report Item - FoundIT</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .report-container { max-width: 600px; margin: 30px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; color: #1a2a6c; }
        input, select, textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
        textarea { height: 80px; }
        .btn-submit { width: 100%; padding: 12px; background: #1a2a6c; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; transition: 0.3s; }
        .btn-submit:hover { background: #b21f1f; }
        .msg { padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

<div class="navbar" style="background:#1a2a6c; padding: 15px; color: white; text-align:center;">
    <a href="dashboard.php" style="color:white; text-decoration:none; margin-right: 20px;">Home Dashboard</a>
    <strong>Report New Activity</strong>
</div>

<div class="report-container">
    <h2>Report Lost/Found Item</h2>

    <?php if ($success): ?>
        <div class="msg success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="msg error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Item Name</label>
            <input type="text" name="title" placeholder="e.g. Silver Key, Blue Wallet" required>
        </div>

        <div class="form-group">
            <label>Category</label>
            <select name="category_id" required>
                <option value="">-- Choose Category --</option>
                <?php while($cat = $cat_result->fetch_assoc()): ?>
                    <option value="<?php echo $cat['category_id']; ?>"><?php echo $cat['category_name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Location Found/Lost</label>
            <select name="location_id" required>
                <option value="">-- Choose Location --</option>
                <?php while($loc = $loc_result->fetch_assoc()): ?>
                    <option value="<?php echo $loc['location_id']; ?>"><?php echo $loc['place_name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Detailed Description</label>
            <textarea name="description" placeholder="Provide details like color, brand, or unique marks..." required></textarea>
        </div>

        <div class="form-group">
            <label>Are you reporting this as Lost or Found?</label>
            <select name="status">
                <option value="found">I Found This (Available for Claim)</option>
                <option value="lost">I Lost This (Help me find it)</option>
            </select>
        </div>

        <div class="form-group">
            <label>Item Photo</label>
            <input type="file" name="item_image" accept="image/*" required>
        </div>

        <button type="submit" class="btn-submit">Submit Report</button>
    </form>
</div>

</body>
</html>
