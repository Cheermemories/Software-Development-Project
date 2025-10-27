<?php
session_start();
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'PIN') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../Controller/UpdateRequestCon.php';

$controller = new UpdateRequestCon($conn);
$message = "";
$selectedRequest = null;

$pinID = $_SESSION['userID'];

// get all requests from this PIN
$requests = $controller->getAllRequests($pinID);

// get all categories for dropdown
$categories = $controller->getAllCategories();

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    foreach ($requests as $r) {
        if ($r['requestID'] == (int)$_GET['id']) {
            $selectedRequest = $r;
            break;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['requestID'])) {
    $id = (int)$_POST['requestID'];
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $categoryID = (int)($_POST['categoryID'] ?? 0);
    
    $message = $controller->updateRequest($id, $title, $description, $categoryID);

    // updates the values so you don't need to refresh to see changes after update
    foreach ($requests as &$r) {
        if ($r['requestID'] == $id) {
            $r['title'] = $title;
            $r['description'] = $description;
            $r['categoryID'] = $categoryID;
            $selectedRequest = $r;
            break;
        }
    }
    unset($r);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Requests</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f6f7fb; margin: 30px; }
        h2 { text-align: center; }
        table { border-collapse: collapse; width: 80%; margin: 20px auto; background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        a.edit-link { text-decoration: none; color: blue; }
        a.edit-link:hover { text-decoration: underline; }
        .container { width: 60%; margin: 30px auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        textarea, input, select { width: 95%; padding: 6px; margin-top: 5px; margin-bottom: 15px; }
        button { background-color: #3498db; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #2980b9; }
        .back-link { display: block; text-align: center; margin-top: 15px; text-decoration: none; color: blue; }
        .back-link:hover { text-decoration: underline; }
        .message { text-align: center; color: green; }
    </style>
</head>
<body>
<h2>Update Requests</h2>
<?php if ($message): ?>
    <p class="message"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<?php if ($selectedRequest): ?>
    <div class="container">
        <form method="POST">
            <input type="hidden" name="requestID" value="<?= htmlspecialchars($selectedRequest['requestID']) ?>">

            <label>Title:</label><br>
            <input type="text" name="title" value="<?= htmlspecialchars($selectedRequest['title']) ?>" required><br>

            <label>Description:</label><br>
            <textarea name="description" rows="4" required><?= htmlspecialchars($selectedRequest['description']) ?></textarea><br>

            <label>Category:</label><br>
            <select name="categoryID" required>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= $c['categoryID'] ?>" <?= $c['categoryID'] == $selectedRequest['categoryID'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select><br>

            <button type="submit">Save Changes</button>
        </form>
        <a href="updateRequest.php" class="back-link">Back</a>
    </div>
<?php else: ?>
    <table>
        <tr>
            <th>Title</th>
            <th>Category</th>
            <th>Description</th>
            <th>Action</th>
        </tr>
        <?php if (empty($requests)): ?>
            <tr><td colspan="4">No requests found.</td></tr>
        <?php else: ?>
            <?php foreach ($requests as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['title']) ?></td>
                    <td><?= htmlspecialchars($r['categoryName'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($r['description']) ?></td>
                    <td><a class="edit-link" href="updateRequest.php?id=<?= urlencode($r['requestID']) ?>">Edit</a></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
<?php endif; ?>
</body>
</html>
