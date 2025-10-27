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
    <h1>Welcome, <?= htmlspecialchars($_SESSION['userName']) ?>!</h1>
    <h2>User Account</h2>
    <a class="btn" href="../../UserAdmin/Boundary/UserAccount/createUserAccount.php">Create User Accounts</a>
    <a class="btn" href="../../UserAdmin/Boundary/UserAccount/viewUserAccount.php">View User Accounts</a>
    <a class="btn" href="../../UserAdmin/Boundary/UserAccount/updateUserAccount.php">Update User Accounts</a>
    <a class="btn" href="../../UserAdmin/Boundary/UserAccount/suspendUserAccount.php">Manage User Accounts</a>
    <a class="btn" href="../../UserAdmin/Boundary/UserAccount/searchUserAccount.php">Search User Accounts</a>
    <h2>User Profile</h2>
    <a class="btn" href="../../UserAdmin/Boundary/UserProfile/createUserProfile.php">Create User Profiles</a>
    <a class="btn" href="../../UserAdmin/Boundary/UserProfile/viewUserProfile.php">View User Profiles</a>
    <a class="btn" href="../../UserAdmin/Boundary/UserProfile/updateUserProfile.php">Update User Profiles</a>
    <a class="btn" href="../../UserAdmin/Boundary/UserProfile/suspendUserProfile.php">Manage User Profiles</a>
    <a class="btn" href="../../UserAdmin/Boundary/UserProfile/searchUserProfile.php">Search User Profiles</a>
    <br><br>
    <a class="btn" href="../logout.php">Logout</a>
</body>
</html>