<?php
session_start();
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'CSR Rep') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../../CSRRep/Controller/ShortlistRequestCsrCon.php';

$csrID = $_SESSION['userID'];
$controller = new ShortlistRequestCsrCon($conn);
$message = "";

// get all active requests
$requests = $controller->getActiveRequests($csrID);

// add or remove from shortlist
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['requestID'])) {
    $requestID = (int)$_POST['requestID'];
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $message = $controller->addToShortlist($csrID, $requestID);
    } elseif ($action === 'remove') {
        $message = $controller->removeFromShortlist($csrID, $requestID);
    }

    // get all active requests again to reflect changes
    $requests = $controller->getActiveRequests($csrID);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Shortlist Requests</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f6f7fb; margin: 30px; }
        h2 { text-align: center; }
        table { border-collapse: collapse; width: 85%; margin: 20px auto; background: #fff;
                box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        form { display: inline; }
        button { padding: 6px 15px; border: none; border-radius: 4px; cursor: pointer; }
        .add { background-color: #3498db; color: white; }
        .remove { background-color: #e74c3c; color: white; }
        .message { text-align: center; color: green; font-weight: bold; }
    </style>
</head>
<body>
<h2>Active Requests</h2>

<?php if ($message): ?>
    <p class="message"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<table>
    <tr>
        <th>Title</th>
        <th>Description</th>
        <th>Category</th>
        <th>Date Created</th>
        <th>Shortlist</th>
    </tr>

    <?php if (empty($requests)): ?>
        <tr><td colspan="5">No active requests found.</td></tr>
    <?php else: ?>
        <?php foreach ($requests as $r): ?>
            <tr>
                <td><?= htmlspecialchars($r['title']) ?></td>
                <td><?= htmlspecialchars($r['description']) ?></td>
                <td><?= htmlspecialchars($r['categoryName'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($r['dateCreated']) ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="requestID" value="<?= htmlspecialchars($r['requestID']) ?>">
                        <?php if ($r['isShortlisted']): ?>
                            <button type="submit" name="action" value="remove" class="remove">Remove</button>
                        <?php else: ?>
                            <button type="submit" name="action" value="add" class="add">Add</button>
                        <?php endif; ?>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</table>
</body>
</html>
