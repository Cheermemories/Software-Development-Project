<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'User Admin') {
    header("Location: Login/Boundary/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Admin Dashboard</title>
    <style>
        body { font-family: Arial; background: #f6f7fb; text-align: center; margin: 50px; }
        h1 { color: #2c3e50; }
        .btn { display: inline-block; margin: 10px; padding: 12px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 6px; }
        .btn:hover { background: #2980b9; }
    </style>
</head>
<body>
    <h1>Welcome, <?= htmlspecialchars($_SESSION['userName']) ?> (User Admin)</h1>
    <a class="btn" href="../UserAdmin/Boundary/createUserProfile.php">Manage Profiles</a>
    <a class="btn" href="../UserAdmin/Boundary/viewUserAccounts.php">View User Accounts</a>
    <a class="btn" href="../logout.php">Logout</a>
</body>
</html>