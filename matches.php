<?php
session_start();
include 'db_connect.php';

$current_user_id = 1; 

//  Match by same category only
$category_match_query = "SELECT 
    lost.item_id AS lost_id,
    lost.title AS lost_title,
    lost.description AS lost_desc,
    lost.date_reported AS lost_date,
    found.item_id AS found_id,
    found.title AS found_title,
    found.description AS found_desc,
    found.date_reported AS found_date,
    c.category_name
FROM items lost
JOIN items found ON lost.category_id = found.category_id
JOIN categories c ON lost.category_id = c.category_id
WHERE lost.status = 'lost'
  AND found.status = 'found'
  AND lost.item_id != found.item_id
ORDER BY c.category_name";

$category_result = $conn->query($category_match_query);

//  Match by same category AND same location 
$location_match_query = "SELECT 
    lost.item_id AS lost_id,
    lost.title AS lost_title,
    lost.description AS lost_desc,
    lost.date_reported AS lost_date,
    found.item_id AS found_id,
    found.title AS found_title,
    found.description AS found_desc,
    found.date_reported AS found_date,
    c.category_name,
    l.place_name AS common_location
FROM items lost
JOIN items found ON lost.category_id = found.category_id
JOIN categories c ON lost.category_id = c.category_id
JOIN reports r_lost ON lost.item_id = r_lost.item_id
JOIN reports r_found ON found.item_id = r_found.item_id
JOIN locations l ON r_lost.location_id = l.location_id
WHERE lost.status = 'lost'
  AND found.status = 'found'
  AND r_lost.location_id = r_found.location_id
ORDER BY c.category_name";

$location_result = $conn->query($location_match_query);

// Count results for display
$strong_count = $location_result ? $location_result->num_rows : 0;
$weak_count   = $category_result ? $category_result->num_rows : 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Possible Matches - Lost & Found</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        h1 { color: #2c3e50; }
        h2 { color: #34495e; margin-top: 30px; border-bottom: 2px solid #ddd; padding-bottom: 8px; }
        .match-card { background: white; border-radius: 10px; padding: 20px; margin-bottom: 15px; box-shadow: 0 2px 6px rgba(0,0,0,0.08); display: flex; gap: 20px; align-items: stretch; }
        .match-card.strong { border-left: 5px solid #2ecc71; }
        .match-card.weak   { border-left: 5px solid #f39c12; }
        .item-side { flex: 1; padding: 12px; border-radius: 8px; }
        .lost-side  { background: #fff5f5; border: 1px solid #ffdada; }
        .found-side { background: #f5fff5; border: 1px solid #d4f8d4; }
        .vs { display: flex; align-items: center; font-weight: bold; color: #888; font-size: 18px; }
        .tag { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 12px; font-weight: bold; margin-bottom: 8px; }
        .tag-lost  { background: #ffdada; color: #cc0000; }
        .tag-found { background: #d4f8d4; color: #006600; }
        .category-badge { display: inline-block; background: #eaf0fb; color: #185FA5; padding: 3px 10px; border-radius: 4px; font-size: 12px; margin-bottom: 10px; }
        .location-badge { display: inline-block; background: #e8f8f0; color: #0F6E56; padding: 3px 10px; border-radius: 4px; font-size: 12px; margin-left: 6px; margin-bottom: 10px; }
        .strength-label { font-size: 12px; font-weight: bold; margin-bottom: 6px; }
        .strong-label { color: #27ae60; }
        .weak-label   { color: #e67e22; }
        .stat-row { display: flex; gap: 15px; margin-bottom: 20px; }
        .stat-box { background: white; padding: 15px 20px; border-radius: 8px; flex: 1; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.06); }
        .stat-box h3 { font-size: 26px; margin: 6px 0; }
        .stat-box p  { font-size: 13px; color: #888; margin: 0; }
        .nav-links { margin-bottom: 20px; }
        .nav-links a { color: #3498db; text-decoration: none; }
        .empty-msg { color: #888; font-style: italic; padding: 20px 0; }
        small { color: #888; }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div style="max-width: 960px; margin: 40px auto; padding: 0 20px;">

    <div class="nav-links">
        <a href="dashboard.php">⬅ Back to Dashboard</a>
    </div>

    <h1>Possible Matches</h1>
    <p style="color:#666;">The system automatically compares lost and found items to find potential matches.</p>

    <!-- Stats -->
    <div class="stat-row">
        <div class="stat-box">
            <p>Strong matches</p>
            <h3 style="color:#27ae60;"><?php echo $strong_count; ?></h3>
            <p>Same category + location</p>
        </div>
        <div class="stat-box">
            <p>Possible matches</p>
            <h3 style="color:#e67e22;"><?php echo $weak_count; ?></h3>
            <p>Same category only</p>
        </div>
    </div>

    
    <h2>Strong Matches — Same Category & Location</h2>
    <p style="color:#666; font-size:14px;">These items were lost and found in the same place and belong to the same category — highest chance of being a match.</p>

    <?php if ($location_result && $location_result->num_rows > 0): ?>
        <?php while($row = $location_result->fetch_assoc()): ?>
        <div class="match-card strong">
            <div class="item-side lost-side">
                <span class="tag tag-lost">LOST</span>
                <h3 style="margin:0 0 6px;"><?php echo $row['lost_title']; ?></h3>
                <p style="margin:0 0 6px; font-size:14px;"><?php echo $row['lost_desc']; ?></p>
                <small>Reported: <?php echo $row['lost_date']; ?></small>
            </div>
            <div class="vs">⇄</div>
            <div class="item-side found-side">
                <span class="tag tag-found">FOUND</span>
                <h3 style="margin:0 0 6px;"><?php echo $row['found_title']; ?></h3>
                <p style="margin:0 0 6px; font-size:14px;"><?php echo $row['found_desc']; ?></p>
                <small>Reported: <?php echo $row['found_date']; ?></small>
            </div>
        </div>
        <div style="margin:-10px 0 15px; padding: 0 5px;">
            <span class="category-badge">Category: <?php echo $row['category_name']; ?></span>
            <span class="location-badge">Location: <?php echo $row['common_location']; ?></span>
            <span class="strength-label strong-label">Strong match</span>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="empty-msg">No strong matches found yet.</p>
    <?php endif; ?>

    
    <h2>Possible Matches — Same Category</h2>
    <p style="color:#666; font-size:14px;">These items share the same category but may be from different locations.</p>

    <?php if ($category_result && $category_result->num_rows > 0): ?>
        <?php while($row = $category_result->fetch_assoc()): ?>
        <div class="match-card weak">
            <div class="item-side lost-side">
                <span class="tag tag-lost">LOST</span>
                <h3 style="margin:0 0 6px;"><?php echo $row['lost_title']; ?></h3>
                <p style="margin:0 0 6px; font-size:14px;"><?php echo $row['lost_desc']; ?></p>
                <small>Reported: <?php echo $row['lost_date']; ?></small>
            </div>
            <div class="vs">⇄</div>
            <div class="item-side found-side">
                <span class="tag tag-found">FOUND</span>
                <h3 style="margin:0 0 6px;"><?php echo $row['found_title']; ?></h3>
                <p style="margin:0 0 6px; font-size:14px;"><?php echo $row['found_desc']; ?></p>
                <small>Reported: <?php echo $row['found_date']; ?></small>
            </div>
        </div>
        <div style="margin:-10px 0 15px; padding: 0 5px;">
            <span class="category-badge">Category: <?php echo $row['category_name']; ?></span>
            <span class="strength-label weak-label">Possible match</span>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="empty-msg">No possible matches found yet.</p>
    <?php endif; ?>

</div>
</body>
</html>