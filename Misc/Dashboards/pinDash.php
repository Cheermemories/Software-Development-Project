<?php
session_start();
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'PIN') {
    header("Location: ../Login/Boundary/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>PIN Dashboard</title>
    <style>
        body { 
            font-family: Arial; 
            background: #f7f8fc; 
            text-align: center; 
            padding: 50px; 
        }
        h2 { color: #333; }
        a { 
            display: block; 
            margin: 10px auto; 
            width: 240px; 
            padding: 12px; 
            background: #3498db; 
            color: white; 
            text-decoration: none; 
            border-radius: 6px; 
            font-size: 15px;
        }
        a:hover { background: #2980b9; }
        .logout { background: #e74c3c; }
        .logout:hover { background: #c0392b; }
    </style>
</head>
<body>
    <h2>Welcome, <?= htmlspecialchars($_SESSION['userName']) ?>!</h2>
    <p>Select a function:</p>

    <a href="../../PIN/Boundary/searchRequest.php">Search My Requests</a>
    <a href="../../PIN/Boundary/viewRequest.php">View Requests</a>
    <a href="../../PIN/Boundary/createRequest.php">Create a Request</a>
    <a href="../../PIN/Boundary/updateRequest.php">Update Request</a>
    <a href="../../PIN/Boundary/cancelRequest.php">Cancel Request</a>
    <a href="../../PIN/Boundary/viewHistoryRequest.php">View Completed Request History</a>
    <a href="../../PIN/Boundary/searchHistoryRequest.php">Search Completed Request History</a>

    <a href="../logout.php" class="logout">Logout</a>
</body>
</html>