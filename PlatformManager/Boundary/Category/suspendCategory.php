<?php
session_start();

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'Platform Manager') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../../../PlatformManager/Controller/Category/SuspendCategoryCon.php';

$controller = new SuspendCategoryCon($conn);
$message = "";

// get all categories
$categories = $controller->getAllCategories();

// call to suspend or active the category
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['categoryID'] ?? 0);

    if (isset($_POST['toggle'])) {
        $message = $controller->toggleStatus($id);

        // update the value without needing to refetch data
        foreach ($categories as &$c) {
            if ($c['categoryID'] == $id) {
                $c['status'] = ($c['status'] === 'Active') ? 'Inactive' : 'Active';
                break;
            }
        }
        unset($c);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Categories</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f6f7fb; margin: 30px; }
        h2 { text-align: center; }
        table { border-collapse: collapse; width: 80%; margin: 20px auto; background: #fff;
                box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        form { display: inline; }
        button { padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer; }
        .activate { background-color: #4CAF50; color: white; }
        .suspend { background-color: #f39c12; color: white; }
        .message { text-align: center; color: green; margin-bottom: 15px; }
    </style>
</head>
<body>
    <h2>Manage Volunteer Service Categories</h2>

    <?php if ($message): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <table>
        <tr>
            <th>Category Name</th>
            <th>Description</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>

        <?php if (empty($categories)): ?>
            <tr><td colspan="4">No categories found.</td></tr>
        <?php else: ?>
            <?php foreach ($categories as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c['name']) ?></td>
                    <td><?= htmlspecialchars($c['description']) ?></td>
                    <td><?= htmlspecialchars($c['status']) ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="categoryID" value="<?= htmlspecialchars($c['categoryID']) ?>">
                            <?php if ($c['status'] === 'Active'): ?>
                                <button type="submit" name="toggle" class="suspend">Suspend</button>
                            <?php else: ?>
                                <button type="submit" name="toggle" class="activate">Activate</button>
                            <?php endif; ?>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</body>
</html>
