<?php
require_once __DIR__ . '/../../Controller/UserStory6/CreateUserProfileCon.php';

$message = "";

// sends form data to controller
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = trim($_POST['role']);
    $permissions = trim($_POST['permissions']);
    $description = trim($_POST['description']);

    if (empty($role) || empty($permissions) || empty($description)) {
        $message = "All fields are required.";
    }
    else {
        $controller = new CreateUserProfileCon($conn);
        $message = $controller->createProfile($role, $permissions, $description);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create User Profile</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f9fc; padding: 30px; }
        .container { width: 60%; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        h2 { text-align: center; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input[type=text], textarea { width: 90%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        textarea { resize: vertical; }
        textarea#description { height: 150px; }
        textarea#permissions { height: 80px; }
        button { margin-top: 20px; padding: 10px 20px; background-color: #007BFF; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        .message { margin-top: 15px; text-align: center; font-weight: bold; color: #333; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Create User Profile</h2>
        <form method="POST" action="">
            <label for="role">Role:</label>
            <input type="text" name="role" id="role" placeholder="Enter role name">

            <label for="permissions">Permissions:</label>
            <textarea id="permissions" name="permissions"></textarea>

            <label for="description">Description:</label>
            <textarea id="description" name="description"></textarea>
            <br>
            <button type="submit">Create Profile</button>
        </form>

        <?php if ($message): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
