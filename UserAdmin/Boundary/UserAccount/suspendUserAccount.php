<?php
session_start();

// only allow logged-in User Admins
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'User Admin') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../../Controller/UserAccount/SuspendUserAccountCon.php';

$controller = new SuspendUserAccountCon($conn);
$message = "";

// fetch all users
$users = $controller->getAllUsers();

// calls controller to change status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['userID'] ?? 0);

    if (isset($_POST['toggle'])) {
        $message = $controller->toggleStatus($id);
    }

    // overwrite the status to show what was updated within the boundry to avoid refetching data
    foreach ($users as &$u) {
        if ($u['userID'] == $id) {
            $u['status'] = ($u['status'] === 'Active') ? 'Inactive' : 'Active';
            break;
        }
    }
    unset($u);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Suspend User Accounts</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f6f7fb; margin: 30px; }
        h2 { text-align: center; }
        table { border-collapse: collapse; width: 90%; margin: 20px auto; background: #fff;
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
    <h2>Manage User Accounts</h2>

    <?php if ($message): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <table>
        <tr>
            <th>User ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>

        <?php if (empty($users)): ?>
            <tr><td colspan="6">No users found.</td></tr>
        <?php else: ?>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['userID']) ?></td>
                    <td><?= htmlspecialchars($u['name']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['role']) ?></td>
                    <td><?= htmlspecialchars($u['status']) ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="userID" value="<?= htmlspecialchars($u['userID']) ?>">
                            <?php if ($u['status'] === 'Active'): ?>
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
