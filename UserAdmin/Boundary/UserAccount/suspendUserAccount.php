<?php
session_start();

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'User Admin') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../../Controller/UserProfile/SuspendUserProfileCon.php';

$controller = new SuspendUserProfileCon($conn);
$message = "";

// fetch all profiles
$profiles = $controller->getAllProfiles();

// calls controller to change status or delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['profileID'] ?? 0);

    if (isset($_POST['toggle'])) {
        $message = $controller->toggleStatus($id);
    }

    // overwrite the status to show what was updated within the boundry to avoid refetching data
    if (isset($_POST['toggle'])) {
        foreach ($profiles as &$p) {
            if ($p['profileID'] == $id) {
                $p['status'] = ($p['status'] === 'Active') ? 'Inactive' : 'Active';
                break;
            }
        }
        unset($p);
    } elseif (isset($_POST['delete'])) {
        $profiles = array_filter($profiles, fn($p) => $p['profileID'] != $id);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage User Profiles</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f6f7fb; margin: 30px; }
        h2 { text-align: center; }
        table { border-collapse: collapse; width: 80%; margin: 20px auto; background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        form { display: inline; }
        button { padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer; }
        .activate { background-color: #4CAF50; color: white; }
        .suspend { background-color: #f39c12; color: white; }
        .delete { background-color: #e74c3c; color: white; }
        .message { text-align: center; color: green; margin-bottom: 15px; }
    </style>
</head>
<body>
    <h2>Manage User Profiles</h2>
    <?php if ($message): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <table>
        <tr>
            <th>Role</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php if (empty($profiles)): ?>
            <tr><td colspan="3">No profiles found.</td></tr>
        <?php else: ?>
            <?php foreach ($profiles as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['role']) ?></td>
                    <td><?= htmlspecialchars($p['status']) ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="profileID" value="<?= htmlspecialchars($p['profileID']) ?>">
                            <?php if ($p['status'] === 'Active'): ?>
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
