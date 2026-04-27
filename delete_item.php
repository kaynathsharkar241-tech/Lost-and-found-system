<?php
include 'admin_check.php';
include 'db_connect.php';

if (isset($_GET['id'])) {
    $item_id = (int)$_GET['id'];
    
    // Delete the image record first (Foreign Key constraint)
    $conn->query("DELETE FROM itemimages WHERE item_id = $item_id");
    // Delete from reports
    $conn->query("DELETE FROM reports WHERE item_id = $item_id");
    // Finally, delete the item
    $conn->query("DELETE FROM items WHERE item_id = $item_id");

    header("Location: admin_dashboard.php?msg=deleted");
}
?>