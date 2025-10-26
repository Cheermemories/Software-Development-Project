<?php
session_start();

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'User Admin') {
    header("Location: ../../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../../Controller/UserAccount/UpdateUserAccountCon.php';

$controller = new UpdateUserAccountCon($conn);
$message = "";
$selectedUser = null;

// fetch all users for display
$users = $controller->getAllUsers();

// fetch all profiles for dropdown options
$profiles = $controller->getAllProfiles();

// fetch a single profile from the fetched array
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    foreach ($users as $u) {
        if ($u['userID'] == (int)$_GET['id']) {
            $selectedUser = $u;
            break;
        }
    }
}

// sends form info to contoller
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['userID'])) {
    $id = (int)$_POST['userID'];
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    $message = $controller->updateUser($id, $name, $email, $password, $role);

    // overwrite the text data to what was updated within the boundry to avoid refetching data
    foreach ($users as &$u) {
        if ($u['userID'] == $id) {
            $u['name'] = $name;
            $u['email'] = $email;
            $u['password'] = $password;
            $u['role'] = $role;
            $selectedUser = $u;
            break;
        }
    }
    unset($u);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update User Accounts</title>
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
        input, select { width: 95%; padding: 6px; margin-top: 5px; margin-bottom: 15px; }
        button { background-color: #3498db; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #2980b9; }
        .back-link { display: block; text-align: center; margin-top: 15px; text-decoration: none; color: blue; }
        .back-link:hover { text-decoration: underline; }
        .message { text-align: center; color: green; }
    </style>
</head>
<body>
<h2>Update User Accounts</h2>
<?php if ($message): ?>
    <p class="message"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<?php if ($selectedUser): ?>
    <div class="container">
        <form method="POST">
            <input type="hidden" name="userID" value="<?= htmlspecialchars($selectedUser['userID']) ?>">

            <label>Name:</label><br>
            <input type="text" name="name" value="<?= htmlspecialchars($selectedUser['name']) ?>" required><br>

            <label>Email:</label><br>
            <input type="email" name="email" value="<?= htmlspecialchars($selectedUser['email']) ?>" required><br>

            <label>Password:</label><br>
            <input type="text" name="password" value="<?= htmlspecialchars($selectedUser['password']) ?>" required><br>

            <label>Role:</label><br>
            <select name="role" required>
                <?php foreach ($profiles as $p): ?>
                    <option value="<?= htmlspecialchars($p['role']) ?>" <?= $p['role'] === $selectedUser['role'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['role']) ?>
                    </option>
                <?php endforeach; ?>
            </select><br>

            <button type="submit">Save Changes</button>
        </form>
        <a href="updateUserAccount.php" class="back-link">Back</a>
    </div>
<?php else: ?>
    <table>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php if (empty($users)): ?>
            <tr><td colspan="5">No users found.</td></tr>
        <?php else: ?>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['name']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['role']) ?></td>
                    <td><?= htmlspecialchars($u['status']) ?></td>
                    <td><a class="edit-link" href="updateUserAccount.php?id=<?= urlencode($u['userID']) ?>">Edit</a></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
<?php endif; ?>
</body>
</html>
