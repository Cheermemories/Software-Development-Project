<?php
session_start();

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'User Admin') {
    header("Location: ../../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../../Controller/UserAccount/SearchUserAccountCon.php';

$controller = new SearchUserAccountCon($conn);
$results = [];
$message = "";

// store search input so it stays after searching
$name = $_GET['name'] ?? '';
$email = $_GET['email'] ?? '';
$role = $_GET['role'] ?? '';
$status = $_GET['status'] ?? '';

// pull roles from profile table
$roles = $controller->getAllRoles();

// calls controller for search
if ($_SERVER['REQUEST_METHOD'] === 'GET' && (isset($_GET['search']) || !empty($name) || !empty($email) || !empty($role) || !empty($status))) {
    $results = $controller->searchUsers($name, $email, $role, $status);

    if (empty($results)) {
        $message = "No users found matching your search criteria.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search User Accounts</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f6f7fb; margin: 30px; }
        h2 { text-align: center; }
        form { width: 80%; margin: 20px auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        label { font-weight: bold; display: block; margin-top: 10px; margin-bottom: 5px; }
        input[type=text], select { width: 40%; padding: 5px; margin-bottom: 10px; }
        button { padding: 6px 15px; background-color: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #2980b9; }
        table { border-collapse: collapse; width: 80%; margin: 20px auto; background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .message { text-align: center; color: red; }
    </style>
</head>
<body>
    <h2>Search User Accounts</h2>

    <form method="GET">
        <label>Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($name) ?>"><br>

        <label>Email:</label>
        <input type="text" name="email" value="<?= htmlspecialchars($email) ?>"><br>

        <label>Role:</label>
        <select name="role">
            <option value="">All Roles</option>
            <?php foreach ($roles as $r): ?>
                <option value="<?= htmlspecialchars($r['role']) ?>" <?= $role === $r['role'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($r['role']) ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <label>Status:</label>
        <select name="status">
            <option value="">All</option>
            <option value="Active" <?= $status === 'Active' ? 'selected' : '' ?>>Active</option>
            <option value="Inactive" <?= $status === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
        </select><br>

        <button type="submit" name="search">Search</button>
    </form>

    <?php if ($message): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <?php if (!empty($results)): ?>
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
            </tr>
            <?php foreach ($results as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['name']) ?></td>
                    <td><?= htmlspecialchars($r['email']) ?></td>
                    <td><?= htmlspecialchars($r['role']) ?></td>
                    <td><?= htmlspecialchars($r['status']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>
