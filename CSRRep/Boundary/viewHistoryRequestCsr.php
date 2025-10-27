<?php
session_start();

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'CSR Rep') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../../CSRRep/Controller/ViewHistoryRequestCsrCon.php';

$controller = new ViewHistoryRequestCsrCon($conn);

// get all completed requests first
$completedRequests = $controller->getAllCompletedRequests();

// use fetched data to make dropdown box
$categories = [];
foreach ($completedRequests as $r) {
    if (!empty($r['categoryID']) && !isset($categories[$r['categoryID']])) {
        $categories[$r['categoryID']] = $r['categoryName'] ?? 'Unknown';
    }
}

$selectedCategory = $_GET['category'] ?? '';
$startDate = $_GET['startDate'] ?? '';
$endDate   = $_GET['endDate'] ?? '';

// applying the filters
if (!empty($selectedCategory)) {
    $completedRequests = array_filter($completedRequests, fn($r) => $r['categoryID'] == $selectedCategory);
}
if (!empty($startDate)) {
    $completedRequests = array_filter($completedRequests, fn($r) => $r['dateCompleted'] >= $startDate);
}
if (!empty($endDate)) {
    $completedRequests = array_filter($completedRequests, fn($r) => $r['dateCompleted'] <= $endDate);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Completed Matches History</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f6f7fb; margin: 30px; }
        h2 { text-align: center; }
        form.filter { text-align: center; margin-bottom: 20px; }
        table { border-collapse: collapse; width: 90%; margin: 0 auto; background: #fff;
                box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; vertical-align: top; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        select, input[type="date"] { padding: 6px; margin: 0 5px; }
        button { padding: 8px 15px; border: none; background: #3498db; color: white;
                 border-radius: 4px; cursor: pointer; }
        button:hover { background: #2980b9; }
        .back { display: block; text-align: center; margin-top: 15px; color: blue; text-decoration: none; }
        .back:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h2>Completed Matches History</h2>

    <form method="GET" class="filter">
        <label>Filter by Service Type:</label>
        <select name="category">
            <option value="">All Categories</option>
            <?php foreach ($categories as $id => $name): ?>
                <option value="<?= htmlspecialchars($id) ?>" <?= ($selectedCategory == $id) ? 'selected' : '' ?>>
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
            <th>PIN Name</th>
            <th>Date Completed</th>
        </tr>
        <?php if (empty($completedRequests)): ?>
            <tr><td colspan="5">No requests found.</td></tr>
        <?php else: ?>
            <?php foreach ($completedRequests as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['title']) ?></td>
                    <td><?= htmlspecialchars($r['categoryName'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($r['description']) ?></td>
                    <td><?= htmlspecialchars($r['pinName'] ?? 'Unknown') ?></td>
                    <td><?= htmlspecialchars($r['dateCompleted'] ?? '-') ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <a href="../../Misc/Dashboards/csrrepDash.php" class="back">Back</a>
</body>
</html>
