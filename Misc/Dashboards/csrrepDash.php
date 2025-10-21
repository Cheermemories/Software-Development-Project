<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'CSR Rep') {
    header("Location: ../Login/Boundary/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>CSR Representative Dashboard</title>
    <style>
        body { font-family: Arial; background: #f6f7fb; text-align: center; margin: 50px; }
        h1 { color: #27ae60; }
        .btn { display: inline-block; margin: 10px; padding: 12px 20px; background: #2ecc71; color: white; text-decoration: none; border-radius: 6px; }
        .btn:hover { background: #27ae60; }
    </style>
</head>
<body>
    <h1>Welcome, <?= htmlspecialchars($_SESSION['userName']) ?> (CSR Rep)</h1>
    <a class="btn" href="../CSR/Boundary/searchRequests.php">Search Requests</a>
    <a class="btn" href="../CSR/Boundary/viewShortlist.php">View Shortlist</a>
    <a class="btn" href="../logout.php">Logout</a>
</body>
</html>
