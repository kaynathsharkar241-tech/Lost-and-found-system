<?php include 'header.php'; ?>

<div style="max-width: 900px; margin: 40px auto; padding: 0 20px;">
    <div class="card"></div>
<?php
include 'db_connect.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // A. DATA IMPLEMENTATION: Get text data from form
    $title = $_POST['title'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $category_id = $_POST['category_id']; // For now, you'll enter a number (1, 2, or 3)

    // B. IMAGE HANDLING: Process the file upload
    $target_dir = "uploads/";
    $file_name = time() . "_" . basename($_FILES["item_image"]["name"]); 
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($_FILES["item_image"]["tmp_name"], $target_file)) {
        
        // C. SQL LOGIC: Save item details to 'items' table
        $sql = "INSERT INTO items (title, description, category_id, status, date_reported) 
                VALUES ('$title', '$description', '$category_id', '$status', CURDATE())";

        if ($conn->query($sql) === TRUE) {
            $last_id = $conn->insert_id; // Get the ID of the item we just saved

            // Save the image path to the 'itemimages' table
            $img_sql = "INSERT INTO itemimages (item_id, image_url) VALUES ('$last_id', '$file_name')";
            $conn->query($img_sql);

            echo "<p style='color:green;'>Success! Item reported with image.</p>";
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Report Item - User Portal</title>
</head>
<body>
    <h2>Report a Lost or Found Item</h2>
    
    <form action="report_item.php" method="POST" enctype="multipart/form-data">
        
        <label>Item Name:</label><br>
        <input type="text" name="title" required><br><br>

        <label>Category ID (e.g., 1 for Electronics):</label><br>
        <input type="number" name="category_id" required><br><br>

        <label>Description:</label><br>
        <textarea name="description" required></textarea><br><br>

        <label>Status:</label><br>
        <select name="status">
            <option value="lost">I Lost This</option>
            <option value="found">I Found This</option>
        </select><br><br>

        <label>Upload Photo:</label><br>
        <input type="file" name="item_image" required><br><br>

        <button type="submit">Submit Report</button>
    </form>
</body>
</html>
<br><a href="dashboard.php">⬅ Back to Dashboard</a>
</div> </div>
        </div> 
</div>