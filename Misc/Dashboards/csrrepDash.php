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
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>CSR Representative Dashboard</title>
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
          <h2>PIN Requests</h2>
          <div class="section" aria-label="PIN request actions">
            <a class="btn" href="../../CSRRep/Boundary/viewRequestCsr.php">View PIN Requests</a>
            <a class="btn" href="../../CSRRep/Boundary/searchRequestCsr.php">Search PIN Requests</a>
            <a class="btn" href="../../CSRRep/Boundary/shortlistRequestCsr.php">Shortlist PIN Requests</a>
          </div>
        </div>

        <div class="card">
          <h2>Shortlisted Requests</h2>
          <div class="section" aria-label="Shortlisted request actions">
            <a class="btn" href="../../CSRRep/Boundary/viewShortlistRequestCsr.php">View Shortlisted Requests</a>
            <a class="btn" href="../../CSRRep/Boundary/searchShortlistRequestCsr.php">Search Shortlisted Requests</a>
          </div>
        </div>

        <div class="card">
          <h2>Completed Requests</h2>
          <div class="section" aria-label="History request actions">
            <a class="btn" href="../../CSRRep/Boundary/viewHistoryRequestCsr.php">View History of Completed Requests</a>
            <a class="btn" href="../../CSRRep/Boundary/searchHistoryRequestCsr.php">Search History of Completed Requests</a>
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
