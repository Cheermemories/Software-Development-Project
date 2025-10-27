<?php
session_start();

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'User Admin') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../../Controller/UserAccount/ViewUserAccountCon.php';

$controller = new ViewUserAccountCon($conn);

// calls getAllUsers function in the controller
$users = $controller->getAllUsers();

// pulls from the fetched array to select a specific user
$selectedUser = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    foreach ($users as $u) {
        if ($u['userID'] == (int)$_GET['id']) {
            $selectedUser = $u;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View User Accounts</title>
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
    <?php if ($selectedUser): ?>
        <div class="container">
            <h2>User Account Details</h2>
            <p><strong>Name:</strong> <?= htmlspecialchars($selectedUser['name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($selectedUser['email']) ?></p>
            <p><strong>Role:</strong> <?= htmlspecialchars($selectedUser['role']) ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($selectedUser['status']) ?></p>
            <a href="viewUserAccount.php" class="back-link">Back</a>
        </div>
    <?php else: ?>
        <h2>User Accounts</h2>
        <table>
            <tr>
                <th>Name</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php if (empty($users)): ?>
                <tr><td colspan="4">No users found.</td></tr>
            <?php else: ?>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['name']) ?></td>
                        <td><?= htmlspecialchars($u['status']) ?></td>
                        <td>
                            <a class="view-link" href="viewUserAccount.php?id=<?= urlencode($u['userID']) ?>">View Details</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
    <?php endif; ?>
</body>
</html>