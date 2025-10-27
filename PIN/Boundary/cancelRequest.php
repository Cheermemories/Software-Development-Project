<?php
session_start();
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'PIN') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../Controller/CancelRequestCon.php';

$pinID = $_SESSION['userID'];
$controller = new CancelRequestCon($conn);
$message = "";

// fetch active requests
$requests = $controller->getActiveRequests($pinID);

// calls controller to change status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel'])) {
    $id = (int)($_POST['requestID'] ?? 0);
    $message = $controller->cancelRequest($id);

    // update status on page
    foreach ($requests as &$r) {
        if ($r['requestID'] == $id) {
            $r['status'] = 'Cancelled';
        }
    }
    unset($r);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Request</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f6f7fb; margin: 30px; }
        h2 { text-align: center; }
        table { border-collapse: collapse; width: 90%; margin: 20px auto; background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; vertical-align: top; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        form { display: inline; }
        button.cancel { padding: 6px 12px; border: none; border-radius: 4px; background-color: #e74c3c; color: white; cursor: pointer; }
        button.cancel:hover { background-color: #c0392b; }
        .message { text-align: center; color: green; margin-bottom: 15px; }
        .desc-cell { text-align: left; white-space: pre-wrap; }
    </style>
</head>
<body>
    <h2>Manage Request</h2>

    <?php if ($message): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <table>
        <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php if (empty($requests)): ?>
            <tr><td colspan="4">You have no active requests.</td></tr>
        <?php else: ?>
            <?php foreach ($requests as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['title']) ?></td>
                    <td class="desc-cell"><?= nl2br(htmlspecialchars($r['description'])) ?></td>
                    <td><?= htmlspecialchars($r['status']) ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="requestID" value="<?= htmlspecialchars($r['requestID']) ?>">
                            <?php if ($r['status'] === 'Active'): ?>
                                <button type="submit" name="cancel" class="cancel" onclick="return confirm('Are you sure you want to cancel this request?');">
                                    Cancel
                                </button>
                            <?php endif; ?>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</body>
</html>
