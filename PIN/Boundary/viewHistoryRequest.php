<?php
session_start();

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'PIN') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../Controller/ViewHistoryRequestCon.php';

$pinID = $_SESSION['userID'];
$controller = new ViewHistoryRequestCon($conn);

$completedRequests = $controller->getCompletedRequests($pinID);

// store data for filters
$categories = [];
foreach ($completedRequests as $r) {
    if (!empty($r['categoryID']) && !isset($categories[$r['categoryID']])) {
        $categories[$r['categoryID']] = $r['categoryName'] ?? 'Unknown';
    }
}

if (!empty($_GET['category'])) {
    $completedRequests = array_filter($completedRequests, fn($r) => $r['categoryID'] == $_GET['category']);
}

if (!empty($_GET['startDate'])) {
    $completedRequests = array_filter($completedRequests, fn($r) => $r['dateCompleted'] >= $_GET['startDate']);
}

if (!empty($_GET['endDate'])) {
    $completedRequests = array_filter($completedRequests, fn($r) => $r['dateCompleted'] <= $_GET['endDate']);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Request History</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f6f7fb; margin: 30px; }
        h2 { text-align: center; }
        form.filter { text-align: center; margin-bottom: 20px; }
        table { border-collapse: collapse; width: 85%; margin: 0 auto; background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        select, input[type="date"] { padding: 6px; margin: 0 5px; }
        button { padding: 8px 15px; border: none; background: #3498db; color: white; border-radius: 4px; cursor: pointer; }
        button:hover { background: #2980b9; }
        .back { display: block; text-align: center; margin-top: 15px; color: blue; text-decoration: none; }
        .back:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h2>Request History</h2>

    <form method="GET" class="filter">
        <label>Filter by Service Type:</label>
        <select name="category">
            <option value="">All Categories</option>
            <?php foreach ($categories as $id => $name): ?>
                <option value="<?= htmlspecialchars($id) ?>" <?= (isset($_GET['category']) && $_GET['category'] == $id) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($name) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>From:</label>
        <input type="date" name="startDate" value="<?= htmlspecialchars($startDate) ?>">

        <label>To:</label>
        <input type="date" name="endDate" value="<?= htmlspecialchars($endDate) ?>">

        <button type="submit">Apply Filters</button>
    </form>

    <table>
        <tr>
            <th>Title</th>
            <th>Category</th>
            <th>Description</th>
            <th>Date Completed</th>
        </tr>
        <?php if (empty($completedRequests)): ?>
            <tr><td colspan="4">No completed requests found for the selected filters.</td></tr>
        <?php else: ?>
            <?php foreach ($completedRequests as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['title']) ?></td>
                    <td><?= htmlspecialchars($r['categoryName'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($r['description']) ?></td>
                    <td><?= htmlspecialchars($r['dateCompleted'] ?? '-') ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <a href="../../Misc/Dashboards/pinDash.php" class="back">Back</a>
</body>
</html>
