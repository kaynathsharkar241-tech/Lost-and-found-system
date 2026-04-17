<?php
session_start();
include 'db_connect.php';

$current_user_id = 1; 

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title       = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $status      = $conn->real_escape_string($_POST['status']);
    $category_id = $conn->real_escape_string($_POST['category_id']);
    $location_id = $conn->real_escape_string($_POST['location_id']);

    // Image upload
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) mkdir($target_dir);

    $file_name   = time() . "_" . basename($_FILES["item_image"]["name"]);
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($_FILES["item_image"]["tmp_name"], $target_file)) {

        // Insert into items table
        $sql = "INSERT INTO items (title, description, category_id, reported_by, status, date_reported) 
                VALUES ('$title', '$description', '$category_id', '$current_user_id', '$status', CURDATE())";

        if ($conn->query($sql) === TRUE) {
            $last_item_id = $conn->insert_id;

            // Insert into itemimages table
            $img_sql = "INSERT INTO itemimages (item_id, image_url) 
                        VALUES ('$last_item_id', '$file_name')";
            $conn->query($img_sql);

            // Insert into reports table
            $report_sql = "INSERT INTO reports (item_id, location_id, report_type, report_date, details)
                           VALUES ('$last_item_id', '$location_id', '$status', CURDATE(), '$description')";
            $conn->query($report_sql);

            $success = "Item reported successfully!";
        } else {
            $error = "Database error: " . $conn->error;
        }
    } else {
        $error = "Error uploading image. Please try again.";
    }
}

// Load categories for dropdown
$cat_result = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_name");

// Load locations for dropdown
$loc_result = $conn->query("SELECT location_id, place_name FROM locations ORDER BY place_name");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Report Item - Lost & Found</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .form-container { max-width: 600px; margin: 40px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h2 { color: #2c3e50; margin-bottom: 20px; }
        label { font-weight: bold; display: block; margin-top: 15px; margin-bottom: 5px; color: #34495e; }
        input[type=text], textarea, select, input[type=file] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 14px; box-sizing: border-box; }
        textarea { height: 100px; resize: vertical; }
        .btn { margin-top: 20px; width: 100%; padding: 12px; background: #3498db; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; }
        .btn:hover { background: #2980b9; }
        .success { background: #d4f8d4; color: #006600; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        .error { background: #ffdada; color: #cc0000; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        .nav-links { margin-bottom: 20px; }
        .nav-links a { color: #3498db; text-decoration: none; }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="form-container">

    <div class="nav-links">
        <a href="dashboard.php">⬅ Back to Dashboard</a>
    </div>

    <h2>Report a Lost or Found Item</h2>

    <?php if ($success): ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="report_item.php" method="POST" enctype="multipart/form-data">

        <label>Item Name:</label>
        <input type="text" name="title" required>

        <label>Category:</label>
        <select name="category_id" required>
            <option value="">-- Select Category --</option>
            <?php while($row = $cat_result->fetch_assoc()): ?>
            <option value="<?php echo $row['category_id']; ?>">
                <?php echo $row['category_name']; ?>
            </option>
            <?php endwhile; ?>
        </select>

        <label>Location:</label>
        <select name="location_id" required>
            <option value="">-- Select Location --</option>
            <?php while($row = $loc_result->fetch_assoc()): ?>
            <option value="<?php echo $row['location_id']; ?>">
                <?php echo $row['place_name']; ?>
            </option>
            <?php endwhile; ?>
        </select>

        <label>Description:</label>
        <textarea name="description" required></textarea>

        <label>Status:</label>
        <select name="status">
            <option value="lost">I Lost This</option>
            <option value="found">I Found This</option>
        </select>

        <label>Upload Photo:</label>
        <input type="file" name="item_image" accept="image/*" required>

        <button type="submit" class="btn">Submit Report</button>

    </form>

</div>

</body>
</html>