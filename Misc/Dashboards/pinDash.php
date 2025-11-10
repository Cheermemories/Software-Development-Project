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
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>PIN Dashboard</title>
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
          <h2>Request Management</h2>
          <div class="section" aria-label="Request actions">
            <a class="btn" href="../../PIN/Boundary/createRequest.php">Create a Request</a>
            <a class="btn" href="../../PIN/Boundary/viewRequest.php">View My Requests</a>
            <a class="btn" href="../../PIN/Boundary/updateRequest.php">Update My Requests</a>
            <a class="btn" href="../../PIN/Boundary/cancelRequest.php">Cancel My Requests</a>
            <a class="btn" href="../../PIN/Boundary/searchRequest.php">Search My Requests</a>
          </div>
        </div>

        <div class="card">
          <h2>Completed Requests</h2>
          <div class="section" aria-label="History request actions">
            <a class="btn" href="../../PIN/Boundary/viewHistoryRequest.php">View Completed Request History</a>
            <a class="btn" href="../../PIN/Boundary/searchHistoryRequest.php">Search Completed Request History</a>
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
