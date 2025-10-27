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
    <h1>Welcome, <?= htmlspecialchars($_SESSION['userName']) ?>!</h1>
    <a class="btn" href="../../CSRRep/Boundary/viewRequestCsr.php">View PIN Requests</a>
    <a class="btn" href="../../CSRRep/Boundary/searchRequestCsr.php">Search PIN Requests</a>
    <a class="btn" href="../../CSRRep/Boundary/shortlistRequestCsr.php">Shortlist PIN Requests</a>
    <a class="btn" href="../../CSRRep/Boundary/viewShortlistRequestCsr.php">View Shortlisted Requests</a>
    <a class="btn" href="../../CSRRep/Boundary/searchShortlistRequestCsr.php">Search Shortlisted Requests</a>
    <a class="btn" href="../../CSRRep/Boundary/viewHistoryRequestCsr.php">View History of Completed Requests</a>
    <a class="btn" href="../../CSRRep/Boundary/searchHistoryRequestCsr.php">Search History of Completed Requests</a>
    <br><br>
    <a class="btn" href="../logout.php">Logout</a>
</body>
</html>
