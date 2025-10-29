<?php
session_start();

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'User Admin') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../../Controller/UserAccount/CreateUserAccountCon.php';

$controller = new CreateUserAccountCon($conn);
$message = "";

// fetch role list from profiles table
$profiles = $controller->getAllProfiles();

// sends form data to controller
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        $message = "All fields are required.";
    } 
    else {
        $message = $controller->createUser($name, $email, $password, $role);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create User Account</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f9fc; padding: 30px; }
        .container { width: 60%; margin: 0 auto; background: white; padding: 20px; border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        h2 { text-align: center; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input[type=text], input[type=email], input[type=password], select {
            width: 90%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;
        }
        button { margin-top: 20px; padding: 10px 20px; background-color: #007BFF;
            color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        .message { margin-top: 15px; text-align: center; font-weight: bold; color: #333; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Create User Account</h2>
        <form method="POST" action="">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name">

            <label for="email">Email:</label>
            <input type="email" name="email" id="email">

            <label for="password">Password:</label>
            <input type="password" name="password" id="password">

            <label for="role">Role:</label>
            <select name="role" id="role">
                <option value="">Select Role</option>
                <?php foreach ($profiles as $p): ?>
                    <option value="<?= htmlspecialchars($p['role']) ?>"><?= htmlspecialchars($p['role']) ?></option>
                <?php endforeach; ?>
            </select>

            <br>
            <button type="submit">Create Account</button>
        </form>

        <?php if ($message): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
