<?php
session_start();
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'PIN') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../Controller/SearchRequestCon.php';

$pinID = $_SESSION['userID'];
$controller = new SearchRequestCon();

$results = [];
$message = "";

// store search input so it stays after searching
$title = $_GET['title'] ?? '';
$description = $_GET['description'] ?? '';
$category = $_GET['category'] ?? '';
$status = $_GET['status'] ?? '';

// calls controller for search
if ($_SERVER['REQUEST_METHOD'] === 'GET' && (isset($_GET['search']) || $title || $description || $category || $status)) {
    $results = $controller->searchRequests($pinID, $title, $description, $category, $status);

    if (empty($results)) {
        $message = "No matching requests found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Requests</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f6f7fb; margin: 30px; }
        h2 { text-align: center; }
        form { width: 80%; margin: 20px auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        label { font-weight: bold; display: inline-block; width: 120px; vertical-align: top; }
        input[type=text], select { width: 40%; padding: 5px; margin-bottom: 10px; }
        textarea { width: 60%; padding: 5px; margin-bottom: 10px; font-family: Arial, sans-serif; resize: vertical; }
        button { padding: 6px 15px; background-color: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #2980b9; }
        table { border-collapse: collapse; width: 80%; margin: 20px auto; background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .message { text-align: center; color: red; }
    </style>
</head>
<body>
<h2>Search Requests</h2>

<form method="GET">
    <label>Title:</label>
    <input type="text" name="title" value="<?= htmlspecialchars($title) ?>"><br>

    <label>Description:</label>
    <textarea name="description" rows="4"><?= htmlspecialchars($description) ?></textarea><br>

    <label>Category:</label>
    <input type="text" name="category" value="<?= htmlspecialchars($category) ?>"><br>

    <label>Status:</label>
    <select name="status">
        <option value="">All</option>
        <option value="Active" <?= $status === 'Active' ? 'selected' : '' ?>>Active</option>
        <option value="Completed" <?= $status === 'Completed' ? 'selected' : '' ?>>Completed</option>
        <option value="Cancelled" <?= $status === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
    </select><br>

    <button type="submit" name="search">Search</button>
</form>

<?php if ($message): ?>
    <p class="message"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<?php if (!empty($results)): ?>
<table>
    <tr>
        <th>Title</th>
        <th>Request Date</th>
        <th>Description</th>
        <th>Category</th>
        <th>Status</th>
    </tr>
    <?php foreach ($results as $r): ?>
        <tr>
            <td><?= htmlspecialchars($r['title']) ?></td>
            <td><?= htmlspecialchars(date('Y-m-d', strtotime($r['dateCreated']))) ?></td>
            <td><?= htmlspecialchars($r['description']) ?></td>
            <td><?= htmlspecialchars($r['categoryName'] ?? 'N/A') ?></td>
            <td><?= htmlspecialchars($r['status']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>
</body>
</html>
