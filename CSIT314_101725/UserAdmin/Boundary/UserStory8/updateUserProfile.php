<?php
require_once __DIR__ . '/../../Controller/UserStory8/UpdateUserProfileCon.php';

$controller = new UpdateUserProfileCon();
$message = "";
$selectedProfile = null;

// fetch all profiles for display
$profiles = $controller->getAllProfiles();

// fetch a single profile from the fetched array
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    foreach ($profiles as $p) {
        if ($p['profileID'] == (int)$_GET['id']) {
            $selectedProfile = $p;
            break;
        }
    }
}

// sends form info to contoller
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['profileID'])) {
    $id = (int)$_POST['profileID'];
    $role = $_POST['role'] ?? '';
    $permissions = $_POST['permissions'] ?? '';
    $description = $_POST['description'] ?? '';
    $message = $controller->updateProfile($id, $role, $permissions, $description);

    // overwrite the text data to what was updated within the boundry to avoid refetching data
    foreach ($profiles as &$p) {
        if ($p['profileID'] == $id) {
            $p['role'] = $role;
            $p['permissions'] = $permissions;
            $p['description'] = $description;
            $selectedProfile = $p;
            break;
        }
    }
    unset($p);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update User Profiles</title>
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
        textarea { width: 100%; }
        .back-link { display: block; text-align: center; margin-top: 15px; text-decoration: none; color: blue; }
        .back-link:hover { text-decoration: underline; }
        .message { text-align: center; color: green; }
    </style>
</head>
<body>
<h2>Update User Profiles</h2>
<?php if ($message): ?>
    <p class="message"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<?php if ($selectedProfile): ?>
    <div class="container">
        <form method="POST">
            <input type="hidden" name="profileID" value="<?= htmlspecialchars($selectedProfile['profileID']) ?>">

            <label>Role:</label><br>
            <input type="text" name="role" value="<?= htmlspecialchars($selectedProfile['role']) ?>" required><br><br>

            <label>Permissions:</label><br>
            <textarea name="permissions" rows="3"><?= htmlspecialchars($selectedProfile['permissions']) ?></textarea><br><br>

            <label>Description:</label><br>
            <textarea name="description" rows="5"><?= htmlspecialchars($selectedProfile['description']) ?></textarea><br><br>

            <button type="submit">Save Changes</button>
        </form>
        <a href="updateUserProfile.php" class="back-link">Back</a>
    </div>
<?php else: ?>
    <table>
        <tr>
            <th>Role</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php if (empty($profiles)): ?>
            <tr><td colspan="3">No profiles found.</td></tr>
        <?php else: ?>
            <?php foreach ($profiles as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['role']) ?></td>
                    <td><?= htmlspecialchars($p['status']) ?></td>
                    <td><a class="edit-link" href="updateUserProfile.php?id=<?= urlencode($p['profileID']) ?>">Edit</a></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
<?php endif; ?>
</body>
</html>