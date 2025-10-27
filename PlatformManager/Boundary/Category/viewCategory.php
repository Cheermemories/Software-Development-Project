<?php
session_start();

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'Platform Manager') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../../../PlatformManager/Controller/Category/ViewCategoryCon.php';

$controller = new ViewCategoryCon($conn);
$categories = $controller->getAllCategories();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Categories</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f6f7fb; margin: 30px; }
        h2 { text-align: center; }
        table { border-collapse: collapse; width: 85%; margin: 20px auto; background: #fff;
                box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; vertical-align: top; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .message { text-align: center; color: red; }
        .back { display: block; text-align: center; margin-top: 15px; color: blue; text-decoration: none; }
        .back:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h2>All Volunteer Service Categories</h2>

    <table>
        <tr>
            <th>Category ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Status</th>
        </tr>
        <?php if (empty($categories)): ?>
            <tr><td colspan="4">No categories found.</td></tr>
        <?php else: ?>
            <?php foreach ($categories as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c['categoryID']) ?></td>
                    <td><?= htmlspecialchars($c['name']) ?></td>
                    <td><?= htmlspecialchars($c['description']) ?></td>
                    <td><?= htmlspecialchars($c['status'] ?? 'Active') ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <a href="../../Misc/Dashboards/pmDash.php" class="back">Back</a>
</body>
</html>
