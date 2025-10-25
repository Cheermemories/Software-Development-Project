<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Platform Manager') {
    header("Location: Login/Boundary/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Platform Manager Dashboard</title>
    <style>
        body { font-family: Arial; background: #f6f7fb; text-align: center; margin: 50px; }
        h1 { color: #8e44ad; }
        .btn { display: inline-block; margin: 10px; padding: 12px 20px; background: #9b59b6; color: white; text-decoration: none; border-radius: 6px; }
        .btn:hover { background: #8e44ad; }
    </style>
</head>
<body>
    <h1>Welcome, <?= htmlspecialchars($_SESSION['userName']) ?> (Platform Manager)</h1>
    <a class="btn" href="../Manager/Boundary/manageCategories.php">Manage Service Categories</a>
    <a class="btn" href="../Manager/Boundary/generateReports.php">Generate Reports</a>
    <a class="btn" href="../logout.php">Logout</a>
</body>
</html>
