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
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Platform Manager Dashboard</title>
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
          <h2>Volunteer Service</h2>
          <div class="section" aria-label="Volunteer Service actions">
            <a class="btn" href="../../PlatformManager/Boundary/Category/createCategory.php">Create Volunteer Service Categories</a>
            <a class="btn" href="../../PlatformManager/Boundary/Category/viewCategory.php">View Volunteer Service Categories</a>
            <a class="btn" href="../../PlatformManager/Boundary/Category/updateCategory.php">Update Volunteer Service Categories</a>
            <a class="btn" href="../../PlatformManager/Boundary/Category/suspendCategory.php">Manage Volunteer Service Categories</a>
            <a class="btn" href="../../PlatformManager/Boundary/Category/searchCategory.php">Search Volunteer Service Categories</a>
          </div>
        </div>

        <div class="card">
          <h2>Reports</h2>
          <div class="section" aria-label="Reports actions">
            <a class="btn" href="../../PlatformManager/Boundary/GenerateReport/generateReport.php">Generate Reports</a>
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
