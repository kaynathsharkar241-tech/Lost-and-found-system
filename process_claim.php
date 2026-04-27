<?php
include 'admin_check.php';
include 'db_connect.php';

$claim_id = (int)$_GET['id'];
$item_id  = (int)$_GET['item'];
$action   = $_GET['action'];
$admin_id = $_SESSION['user_id'];

if ($action == 'approve') {
    $status = 'approved';
    $action_type = 'Approved';
        $conn->query("UPDATE items SET status = 'closed' WHERE item_id = $item_id");
} else {
    $status = 'rejected';
    $action_type = 'Rejected';
}

// 1. Update the claim status
$conn->query("UPDATE claims SET status = '$status' WHERE claim_id = $claim_id");

// 2. Log the action in the adminactions table as per your ERD
$log_sql = "INSERT INTO adminactions (admin_id, item_id, action_type, action_date) 
            VALUES ($admin_id, $item_id, '$action_type', CURDATE())";
$conn->query($log_sql);

header("Location: admin_dashboard.php?status=success");
exit();
?>