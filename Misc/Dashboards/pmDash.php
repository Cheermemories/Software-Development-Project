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
    <h1>Welcome, <?= htmlspecialchars($_SESSION['userName']) ?>!</h1>
    <h2>Volunteer Service</h2>
    <a class="btn" href="../../PlatformManager/Boundary/Category/createCategory.php">Create Volunteer Service Categories</a>
    <a class="btn" href="../../PlatformManager/Boundary/Category/viewCategory.php">View Volunteer Service Categories</a>
    <a class="btn" href="../../PlatformManager/Boundary/Category/updateCategory.php">Update Volunteer Service Categories</a>
    <a class="btn" href="../../PlatformManager/Boundary/Category/suspendCategory.php">Suspend Volunteer Service Categories</a>
    <a class="btn" href="../../PlatformManager/Boundary/Category/searchCategory.php">Search Volunteer Service Categories</a>
    <h2>Reports</h2>
    <a class="btn" href="../../PlatformManager/Boundary/GenerateReport/generateReport.php">Generate Reports</a>
    <br><br>
    <a class="btn" href="../logout.php">Logout</a>
</body>
</html>
