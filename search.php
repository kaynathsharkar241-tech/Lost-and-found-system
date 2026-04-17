<?php include 'db_connect.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Lost & Found</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .item-card { border: 1px solid #ccc; padding: 15px; margin: 10px 0; border-radius: 8px; background: white; }
        .status-lost { color: red; font-weight: bold; }
        .status-found { color: green; font-weight: bold; }
        .search-form { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
        .search-form input, .search-form select { padding: 8px; margin: 5px; border: 1px solid #ccc; border-radius: 5px; }
        .btn { display: inline-block; padding: 8px 20px; background: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<?php
// Get filter values from URL
$keyword  = isset($_GET['search'])   ? $conn->real_escape_string($_GET['search'])   : '';
$status   = isset($_GET['status'])   ? $conn->real_escape_string($_GET['status'])   : '';
$category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';
$location = isset($_GET['location']) ? $conn->real_escape_string($_GET['location']) : '';
$from     = isset($_GET['from'])     ? $conn->real_escape_string($_GET['from'])     : '';
$to       = isset($_GET['to'])       ? $conn->real_escape_string($_GET['to'])       : '';

// Build query dynamically
$search_query = "SELECT i.item_id, i.title, i.description, i.status, i.date_reported,
                        c.category_name, l.place_name
                 FROM items i
                 JOIN categories c ON i.category_id = c.category_id
                 LEFT JOIN reports r ON i.item_id = r.item_id
                 LEFT JOIN locations l ON r.location_id = l.location_id
                 WHERE 1=1";

if ($keyword)  $search_query .= " AND (i.title LIKE '%$keyword%' OR i.description LIKE '%$keyword%')";
if ($status)   $search_query .= " AND i.status = '$status'";
if ($category) $search_query .= " AND c.category_name = '$category'";
if ($location) $search_query .= " AND l.place_name = '$location'";
if ($from && $to) $search_query .= " AND i.date_reported BETWEEN '$from' AND '$to'";

$search_query .= " ORDER BY i.date_reported DESC";
$result = $conn->query($search_query);

// Fetch categories and locations for dropdowns
$cat_result = $conn->query("SELECT category_name FROM categories ORDER BY category_name");
$loc_result = $conn->query("SELECT place_name FROM locations ORDER BY place_name");
?>

<div style="max-width: 900px; margin: 40px auto; padding: 0 20px;">

    <h1>Browse Items</h1>

    <!-- Search Form -->
    <div class="search-form">
        <form method="GET" action="search.php">

            <input type="text" name="search" placeholder="Search by keyword..." 
                   value="<?php echo $keyword; ?>">

            <select name="status">
                <option value="">All Statuses</option>
                <option value="lost"  <?php echo $status == 'lost'  ? 'selected' : ''; ?>>Lost</option>
                <option value="found" <?php echo $status == 'found' ? 'selected' : ''; ?>>Found</option>
            </select>

            <select name="category">
                <option value="">All Categories</option>
                <?php while($row = $cat_result->fetch_assoc()): ?>
                <option value="<?php echo $row['category_name']; ?>"
                    <?php echo $category == $row['category_name'] ? 'selected' : ''; ?>>
                    <?php echo $row['category_name']; ?>
                </option>
                <?php endwhile; ?>
            </select>

            <select name="location">
                <option value="">All Locations</option>
                <?php while($row = $loc_result->fetch_assoc()): ?>
                <option value="<?php echo $row['place_name']; ?>"
                    <?php echo $location == $row['place_name'] ? 'selected' : ''; ?>>
                    <?php echo $row['place_name']; ?>
                </option>
                <?php endwhile; ?>
            </select>

            <input type="date" name="from" value="<?php echo $from; ?>">
            <input type="date" name="to"   value="<?php echo $to; ?>">

            <button type="submit" class="btn">Search</button>
            <a href="search.php" class="btn" style="background: #95a5a6;">Clear</a>

        </form>
    </div>

    <hr>

    <!-- Results -->
    <div class="results-container">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="item-card">
                    <h3><?php echo $row['title']; ?></h3>
                    <p>Status: <span class="status-<?php echo $row['status']; ?>">
                        <?php echo ucfirst($row['status']); ?>
                    </span></p>
                    <p><?php echo $row['description']; ?></p>
                    <p>Category: <?php echo $row['category_name']; ?> 
                       | Location: <?php echo $row['place_name'] ?? 'N/A'; ?></p>
                    <small>Reported on: <?php echo $row['date_reported']; ?></small>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No items found.</p>
        <?php endif; ?>
    </div>

    <a href="dashboard.php">⬅ Back to Dashboard</a>

</div>
</body>
</html>