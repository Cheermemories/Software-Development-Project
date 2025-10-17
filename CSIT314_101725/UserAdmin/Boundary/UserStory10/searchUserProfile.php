<?php
require_once __DIR__ . '/../../Controller/UserStory10/SearchUserProfileCon.php';

$controller = new SearchUserProfileCon();
$results = [];
$message = "";

// store search input so it stays after searching
$role = $_GET['role'] ?? '';
$permissions = $_GET['permissions'] ?? '';
$description = $_GET['description'] ?? '';
$status = $_GET['status'] ?? '';

// calls controller for search
if ($_SERVER['REQUEST_METHOD'] === 'GET' && (isset($_GET['search']) || !empty($role) || !empty($permissions) || !empty($description) || !empty($status))) {
    $results = $controller->searchProfiles($role, $permissions, $description, $status);

    if (empty($results)) {
        $message = "No profiles found matching your search criteria.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search User Profiles</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f6f7fb; margin: 30px; }
        h2 { text-align: center; }
        form { width: 80%; margin: 20px auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        label {font-weight: bold; display: block; margin-top: 10px; margin-bottom: 5px;}
        input[type=text], select { width: 40%; padding: 5px; margin-bottom: 10px; }
        textarea {width: 60%; padding: 5px; margin-bottom: 10px; font-family: Arial, sans-serif; resize: vertical;}
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
    <h2>Search User Profiles</h2>

    <form method="GET">
        <label>Role:</label>
        <input type="text" name="role" value="<?= htmlspecialchars($role) ?>"><br>

        <label>Permissions:</label>
        <input type="text" name="permissions" value="<?= htmlspecialchars($permissions) ?>"><br>

        <label>Description:</label>
        <textarea name="description" rows="4" style="width: 60%; padding: 5px;"><?= htmlspecialchars($description) ?></textarea><br>

        <label>Status:</label>
        <select name="status">
            <option value="" <?= $status === '' ? 'selected' : '' ?>>All</option>
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
                <th>Role</th>
                <th>Permissions</th>
                <th>Description</th>
                <th>Status</th>
            </tr>
            <?php foreach ($results as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['role']) ?></td>
                    <td><?= htmlspecialchars($r['permissions']) ?></td>
                    <td><?= htmlspecialchars($r['description']) ?></td>
                    <td><?= htmlspecialchars($r['status']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>
