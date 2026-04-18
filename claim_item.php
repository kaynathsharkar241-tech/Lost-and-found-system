<?php
session_start();
include 'db_connect.php';

$current_user_id = 1; // replace with $_SESSION['user_id'] later

$message = "";
$error   = "";

// Get item_id from URL
$item_id = isset($_GET['item_id']) ? (int)$_GET['item_id'] : 0;

// If no valid item_id redirect back to search
if ($item_id === 0) {
    header("Location: search.php");
    exit();
}

// Fetch the item details
$item_result = $conn->query("SELECT i.item_id, i.title, i.description, 
                                    i.status, i.date_reported, c.category_name
                             FROM items i
                             JOIN categories c ON i.category_id = c.category_id
                             WHERE i.item_id = $item_id");

// If item doesn't exist redirect back
if ($item_result->num_rows === 0) {
    header("Location: search.php");
    exit();
}

$item = $item_result->fetch_assoc();

// Check if user already claimed this item
$already_claimed_result = $conn->query("SELECT claim_id FROM claims 
                                        WHERE item_id = $item_id 
                                        AND claimed_by = $current_user_id");
$already_claimed_count  = $already_claimed_result ? $already_claimed_result->num_rows : 0;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if ($already_claimed_count > 0) {
        $error = "You have already submitted a claim for this item!";
    } else {
        $proof    = $conn->real_escape_string($_POST['proof_description']);
        $img_name = "";

        // Handle optional image upload
        if (isset($_FILES['claim_image']) && $_FILES['claim_image']['error'] === 0) {
            $target_dir  = "uploads/";
            if (!is_dir($target_dir)) mkdir($target_dir);
            $img_name    = time() . "_" . basename($_FILES['claim_image']['name']);
            $target_file = $target_dir . $img_name;

            if (!move_uploaded_file($_FILES['claim_image']['tmp_name'], $target_file)) {
                $error = "Image upload failed. Please try again.";
            }
        }

        if (empty($error)) {
            $sql = "INSERT INTO claims (item_id, claimed_by, claim_date, status, proof_description)
                    VALUES ($item_id, $current_user_id, CURDATE(), 'pending', '$proof')";

            if ($conn->query($sql) === TRUE) {
                $message = "Your claim has been submitted successfully! We will review it shortly.";
                $already_claimed_count = 1; // update count so form hides after success
            } else {
                $error = "Error submitting claim: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Claim Item - FoundIT</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { max-width: 600px; margin: 40px auto; padding: 0 20px; }
        .item-preview { background: white; border-left: 5px solid #3498db; 
                        padding: 15px 20px; border-radius: 8px; margin-bottom: 25px;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.08); }
        .item-preview h3 { margin: 0 0 8px; color: #2c3e50; }
        .form-box { background: white; padding: 25px; border-radius: 10px;
                    box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
        label { display: block; font-weight: bold; color: #34495e; 
                margin-bottom: 6px; font-size: 14px; }
        textarea { width: 100%; padding: 12px; border: 1px solid #ccc; 
                   border-radius: 6px; font-size: 14px; height: 130px; 
                   resize: vertical; box-sizing: border-box; }
        .file-input { display: block; width: 100%; padding: 10px; 
                      border: 1px solid #ccc; border-radius: 6px; 
                      font-size: 14px; box-sizing: border-box; margin-bottom: 6px; }
        .hint { font-size: 13px; color: #888; margin-top: 6px; margin-bottom: 16px; }
        .btn-submit { display: block; width: 100%; padding: 12px; 
                      background: #3498db; color: white; border: none; 
                      border-radius: 6px; font-size: 15px; cursor: pointer; 
                      margin-top: 10px; }
        .btn-submit:hover { background: #2980b9; }
        .success { background: #d4f8d4; color: #006600; padding: 12px 16px; 
                   border-radius: 6px; margin-bottom: 20px; }
        .error-msg { background: #ffdada; color: #cc0000; padding: 12px 16px; 
                     border-radius: 6px; margin-bottom: 20px; }
        .tag-found { background: #d4f8d4; color: #006600; padding: 3px 10px; 
                     border-radius: 4px; font-size: 12px; font-weight: bold; }
        .nav-links { margin-bottom: 20px; }
        .nav-links a { color: #3498db; text-decoration: none; }
        .already-claimed { background: #fff3cd; color: #856404; padding: 15px; 
                           border-radius: 8px; text-align: center; font-size: 15px; }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">

    <div class="nav-links">
        <a href="search.php">⬅ Back to Search</a>
    </div>

    <h1 style="color:#2c3e50;">Claim an Item</h1>
    <p style="color:#666;">Describe how this item belongs to you. 
       Your claim will be reviewed and you will be notified.</p>

    <!-- Item Preview -->
    <div class="item-preview">
        <span class="tag-found">FOUND</span>
        <h3><?php echo $item['title']; ?></h3>
        <p style="margin:6px 0; color:#555;"><?php echo $item['description']; ?></p>
        <small style="color:#888;">
            Category: <?php echo $item['category_name']; ?> 
            | Reported on: <?php echo $item['date_reported']; ?>
        </small>
    </div>

    <?php if ($message): ?>
        <div class="success">
            <?php echo $message; ?>
            <br><br>
            <a href="my_activities.php" style="color:#006600; font-weight:bold;">
                View your claims in My Activities →
            </a>
        </div>

    <?php elseif ($already_claimed_count > 0 && empty($message)): ?>
        <div class="already-claimed">
            You have already submitted a claim for this item. 
            Check the status in <a href="my_activities.php">My Activities</a>.
        </div>

    <?php else: ?>

        <?php if ($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Claim Form -->
        <div class="form-box">
            <form method="POST" enctype="multipart/form-data">

                <label>Proof of Ownership</label>
                <textarea name="proof_description" 
                          placeholder="Describe why this item belongs to you. For example: the brand, color, any unique marks, what was inside it, where you lost it, etc." 
                          required></textarea>
                <p class="hint">The more detail you provide, the faster your claim will be reviewed.</p>

                <label>Upload Proof Image (optional)</label>
                <input type="file" name="claim_image" accept="image/*" class="file-input">
                <p class="hint">Upload a photo as proof — e.g. a picture of the item, 
                   your ID, or anything that proves ownership.</p>

                <button type="submit" class="btn-submit">Submit Claim</button>

            </form>
        </div>

    <?php endif; ?>

</div>

</body>
</html>
