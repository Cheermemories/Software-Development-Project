<?php
session_start();
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'CSR Rep') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../../CSRRep/Controller/SearchShortlistRequestCsrCon.php';

$csrID = $_SESSION['userID'];
$controller = new SearchShortlistRequestCsrCon($conn);

$results = [];
$message = "";

$title = $_GET['title'] ?? '';
$description = $_GET['description'] ?? '';
$category = $_GET['category'] ?? '';
$pinName = $_GET['pinName'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && (isset($_GET['search']) || $title || $description || $category || $pinName)) {
    $results = $controller->searchShortlistedRequests($csrID, $title, $description, $category, $pinName);
    if (empty($results)) {
        $message = "No matching shortlisted requests found.";
    }
}

$selectedRequest = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    foreach ($results as $r) {
        if ($r['requestID'] == (int)$_GET['id']) {
            $selectedRequest = $r;
            break;
        }
    }

    if ($selectedRequest) {
        $controller->incrementViewCount($selectedRequest['requestID']);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search My Shortlist</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f6f7fb; margin: 30px; }
        h2 { text-align: center; }
        form { width: 80%; margin: 20px auto; background: #fff; padding: 20px;
               border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        label { font-weight: bold; display: inline-block; width: 120px; vertical-align: top; }
        input[type=text], textarea { width: 60%; padding: 5px; margin-bottom: 10px; }
        textarea { resize: vertical; font-family: Arial, sans-serif; }
        button { padding: 6px 15px; background-color: #3498db; color: white; border: none;
                 border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #2980b9; }
        table { border-collapse: collapse; width: 90%; margin: 20px auto; background: #fff;
                box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; vertical-align: top; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .message { text-align: center; color: red; }
    </style>
</head>
<body>

<h2>Search My Shortlist</h2>

<form method="GET">
    <label>Title:</label>
    <input type="text" name="title" value="<?= htmlspecialchars($title) ?>"><br>

    <label>Name of PIN:</label>
    <input type="text" name="pinName" value="<?= htmlspecialchars($pinName) ?>"><br>

    <label>Description:</label>
    <textarea name="description" rows="3"><?= htmlspecialchars($description) ?></textarea><br>

    <label>Category:</label>
    <input type="text" name="category" value="<?= htmlspecialchars($category) ?>"><br>

    <button type="submit" name="search">Search</button>
</form>

<?php if ($message): ?>
    <p class="message"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<?php if (!empty($results)): ?>
<table>
    <tr>
        <th>Title</th>
        <th>PIN Name</th>
        <th>Category</th>
        <th>Date Created</th>
        <th>Description</th>
    </tr>
    <?php foreach ($results as $r): ?>
        <tr>
            <td><?= htmlspecialchars($r['title']) ?></td>
            <td><?= htmlspecialchars($r['pinName'] ?? 'Unknown') ?></td>
            <td><?= htmlspecialchars($r['categoryName'] ?? 'N/A') ?></td>
            <td><?= htmlspecialchars($r['dateCreated']) ?></td>
            <td><?= nl2br(htmlspecialchars($r['description'])) ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>

</body>
</html>
