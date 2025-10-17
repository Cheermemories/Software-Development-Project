<?php
require_once __DIR__ . '/../../Controller/UserStory7/ViewUserProfileCon.php';

$controller = new ViewUserProfileCon($conn);

// calls getAllProfiles function in the controller
$profiles = $controller->getAllProfiles();

// pulls from the fetched array to select a specific profile
$selectedProfile = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    foreach ($profiles as $p) {
        if ($p['profileID'] == (int)$_GET['id']) {
            $selectedProfile = $p;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View User Profiles</title>
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
        .description-box, .permissions-box { white-space: pre-wrap; text-align: left; }
    </style>
</head>
<body>
<?php if ($selectedProfile): ?>
    <div class="container">
        <h2>Profile Details</h2>
        <p><strong>Role:</strong> <?= htmlspecialchars($selectedProfile['role']) ?></p>
        <p><strong>Permissions:</strong></p>
        <div class="permissions-box"><?= nl2br(htmlspecialchars($selectedProfile['permissions'])) ?></div>
        <p><strong>Description:</strong></p>
        <div class="description-box"><?= nl2br(htmlspecialchars($selectedProfile['description'])) ?></div>
        <p><strong>Status:</strong> <?= htmlspecialchars($selectedProfile['status']) ?></p>
        <a href="viewUserProfile.php" class="back-link">Back</a>
    </div>
<?php else: ?>
    <h2>User Profiles</h2>
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
                    <td><a class="view-link" href="viewUserProfile.php?id=<?= urlencode($p['profileID']) ?>">View Details</a></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
<?php endif; ?>
</body>
</html>
