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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>User Admin Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
</head>
<body>
  <div class="app-shell">
    <header class="topbar">
      <div class="brand"><h2>Welcome, <?= htmlspecialchars($_SESSION['userName']) ?>!</h2></div>
    </header>

    <div class="container">
      <aside class="sidebar" aria-hidden="false">
        <nav class="nav" aria-label="Main navigation">
          <a href="#" class="active">Overview</a>
          <a href="../logout.php">Logout</a>
        </nav>
      </aside>

      <main class="main">
        <div class="card">
          <h2>User Account</h2>
          <div class="section" aria-label="User Account actions">
            <a class="btn" href="../../UserAdmin/Boundary/UserAccount/createUserAccount.php">Create User Accounts</a>
            <a class="btn" href="../../UserAdmin/Boundary/UserAccount/viewUserAccount.php">View User Accounts</a>
            <a class="btn" href="../../UserAdmin/Boundary/UserAccount/updateUserAccount.php">Update User Accounts</a>
            <a class="btn" href="../../UserAdmin/Boundary/UserAccount/suspendUserAccount.php">Manage User Accounts</a>
            <a class="btn" href="../../UserAdmin/Boundary/UserAccount/searchUserAccount.php">Search User Accounts</a>
          </div>
        </div>

        <div class="card">
          <h2>User Profile</h2>
          <div class="section" aria-label="User Profile actions">
            <a class="btn" href="../../UserAdmin/Boundary/UserProfile/createUserProfile.php">Create User Profiles</a>
            <a class="btn" href="../../UserAdmin/Boundary/UserProfile/viewUserProfile.php">View User Profiles</a>
            <a class="btn" href="../../UserAdmin/Boundary/UserProfile/updateUserProfile.php">Update User Profiles</a>
            <a class="btn" href="../../UserAdmin/Boundary/UserProfile/suspendUserProfile.php">Manage User Profiles</a>
            <a class="btn" href="../../UserAdmin/Boundary/UserProfile/searchUserProfile.php">Search User Profiles</a>
          </div>
        </div>
      </main>
    </div>
  </div>

  <script>
    (function(){
      const toggle = document.getElementById('menuToggle');
      const sidebar = document.querySelector('.sidebar');
      if(!toggle || !sidebar) return;
      toggle.addEventListener('click', ()=> {
        sidebar.classList.toggle('open');
        sidebar.setAttribute('aria-hidden', String(!sidebar.classList.contains('open')));
      });
      document.addEventListener('click', (e)=> {
        if (!sidebar.contains(e.target) && !toggle.contains(e.target) && sidebar.classList.contains('open')) {
          sidebar.classList.remove('open');
          sidebar.setAttribute('aria-hidden','true');
        }
      });
    })();
  </script>
</body>
</html>