<?php
session_start();
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'CSR Rep') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../../CSRRep/Controller/ViewRequestCsrCon.php';

$controller = new ViewRequestCsrCon($conn);

$requests = $controller->getAllRequests();

$selectedRequest = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    foreach ($requests as $r) {
        if ($r['requestID'] == (int)$_GET['id']) {
            $selectedRequest = $r;
            break;
        }
    }

    // to call for the function of adding a value to views
    if ($selectedRequest) {
        $controller->incrementViewCount($selectedRequest['requestID']);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Requests</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f6f7fb; margin: 30px; }
        h2 { text-align: center; }
        table { border-collapse: collapse; width: 80%; margin: 20px auto; background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        a.view-link { text-decoration: none; color: blue; }
        a.view-link:hover { text-decoration: underline; }
        .container { width: 60%; margin: 30px auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        .back-link { display: block; text-align: center; margin-top: 15px; text-decoration: none; color: blue; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <?php if ($selectedRequest): ?>
        <div class="container">
            <h2>Request Details</h2>
            <p><strong>Title:</strong> <?= htmlspecialchars($selectedRequest['title']) ?></p>
            <p><strong>Name of PIN:</strong> <?= htmlspecialchars($selectedRequest['pinName'] ?? 'Unknown') ?></p>
            <p><strong>Request Date:</strong> <?= htmlspecialchars(date('Y-m-d')) ?></p>
            <p><strong>Description:</strong> <?= htmlspecialchars($selectedRequest['description']) ?></p>
            <p><strong>Category:</strong> <?= htmlspecialchars($selectedRequest['categoryName'] ?? 'N/A') ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($selectedRequest['status']) ?></p>
            <a href="viewRequestCSR.php" class="back-link">Back</a>
        </div>
    <?php else: ?>
        <h2>View Requests</h2>
        <table>
            <tr>
                <th>Title</th>
                <th>PIN Name</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php if (empty($requests)): ?>
                <tr><td colspan="4">No requests found.</td></tr>
            <?php else: ?>
                <?php foreach ($requests as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['title']) ?></td>
                        <td><?= htmlspecialchars($r['pinName'] ?? 'Unknown') ?></td>
                        <td><?= htmlspecialchars($r['status']) ?></td>
                        <td><a class="view-link" href="viewRequestCSR.php?id=<?= urlencode($r['requestID']) ?>">View Details</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
    <?php endif; ?>
</body>
</html>