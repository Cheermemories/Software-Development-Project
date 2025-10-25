<?php
session_start();

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'PIN') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../Controller/SearchRequestHistoryCon.php';

$pinID = $_SESSION['userID'];
$controller = new SearchRequestHistoryCon($conn);

$completedRequests = $controller->getCompletedRequests($pinID);

// turns category id into name
$categories = [];
foreach ($completedRequests as $r) {
    if (!empty($r['categoryID']) && !isset($categories[$r['categoryID']])) {
        $categories[$r['categoryID']] = $r['categoryName'] ?? 'Unknown';
    }
}

$title = $_GET['title'] ?? '';
$description = $_GET['description'] ?? '';
$category = $_GET['category'] ?? '';
$startDate = $_GET['startDate'] ?? '';
$endDate = $_GET['endDate'] ?? '';

$results = [];
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'GET' && (isset($_GET['search']) || $title || $description || $category || $startDate || $endDate)) {
    $results = $completedRequests;

    // title & description search from the fetched array
    if (!empty($title) || !empty($description)) {
        $results = array_filter($results, function($r) use ($title, $description) {
            $titleMatch = empty($title) ? true : (stripos($r['title'], $title) !== false);
            $descMatch = empty($description) ? true : (stripos($r['description'], $description) !== false);
            return $titleMatch && $descMatch;
        });
    }

    if (!empty($category)) {
        $results = array_filter($results, fn($r) => $r['categoryID'] == $category);
    }

    if (!empty($startDate)) {
        $results = array_filter($results, fn($r) => $r['dateCompleted'] >= $startDate);
    }

    if (!empty($endDate)) {
        $results = array_filter($results, fn($r) => $r['dateCompleted'] <= $endDate);
    }

    if (empty($results)) {
        $message = "No matching completed requests found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Request History</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f6f7fb; margin: 30px; }
        h2 { text-align: center; }
        form { width: 80%; margin: 20px auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        label { font-weight: bold; display: inline-block; width: 120px; }
        label2 { font-weight: bold; display: inline-block; width: 120px; vertical-align: top; }
        input[type=text], select, input[type=date] { width: 40%; padding: 5px; margin-bottom: 10px; }
        textarea { width: 60%; padding: 5px; margin-bottom: 10px; font-family: Arial, sans-serif; resize: vertical; }
        .date-row label, .date-row input { display: inline; width: auto; }
        .date-row { margin-top: 5px; margin-bottom: 10px; }
        button { padding: 6px 15px; background-color: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #2980b9; }
        table { border-collapse: collapse; width: 85%; margin: 20px auto; background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .message { text-align: center; color: red; }
        .back { display: block; text-align: center; margin-top: 15px; color: blue; text-decoration: none; }
        .back:hover { text-decoration: underline; }
    </style>
</head>
<body>
<h2>Search Request History</h2>

<form method="GET">
    <label>Title:</label>
    <input type="text" name="title" value="<?= htmlspecialchars($title) ?>"><br>

    <label2>Description:</label2>
    <textarea name="description" rows="4"><?= htmlspecialchars($description) ?></textarea><br>

    <label>Service Type:</label>
    <select name="category">
        <option value="">All Categories</option>
        <?php foreach ($categories as $id => $name): ?>
            <option value="<?= htmlspecialchars($id) ?>" <?= ($category == $id) ? 'selected' : '' ?>>
                <?= htmlspecialchars($name) ?>
            </option>
        <?php endforeach; ?>
    </select><br>

    <div class="date-row">
        <label>From:</label>
        <input type="date" name="startDate" value="<?= htmlspecialchars($startDate) ?>">
        &nbsp;&nbsp;
        <label>To:</label>
        <input type="date" name="endDate" value="<?= htmlspecialchars($endDate) ?>">
    </div>

    <button type="submit" name="search">Search</button>
</form>

<?php if ($message): ?>
    <p class="message"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<?php if (!empty($results)): ?>
<table>
    <tr>
        <th>Title</th>
        <th>Category</th>
        <th>Description</th>
        <th>Date Completed</th>
    </tr>
    <?php foreach ($results as $r): ?>
        <tr>
            <td><?= htmlspecialchars($r['title']) ?></td>
            <td><?= htmlspecialchars($r['categoryName'] ?? 'N/A') ?></td>
            <td><?= htmlspecialchars($r['description']) ?></td>
            <td><?= htmlspecialchars($r['dateCompleted'] ?? '-') ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>

<a href="../../Misc/Dashboards/pinDash.php" class="back">Back to Dashboard</a>
</body>
</html>