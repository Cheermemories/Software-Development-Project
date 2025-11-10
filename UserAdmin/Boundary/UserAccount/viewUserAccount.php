<?php
session_start();

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'User Admin') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../../Controller/UserAccount/ViewUserAccountCon.php';

$controller = new ViewUserAccountCon($conn);

// calls getAllUsers function in the controller
$users = $controller->getAllUsers();

// pulls from the fetched array to select a specific user
$selectedUser = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    foreach ($users as $u) {
        if ($u['userID'] == (int)$_GET['id']) {
            $selectedUser = $u;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View User Accounts</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <link rel="stylesheet" href="../../../assets/css/table.css">
    <style>
        .message {
            text-align: center;
            margin-top: 16px;
            font-weight: 600;
            color: var(--muted);
        }
        main.card {
            padding: 24px 32px;
        }
    </style>
</head>
<body>
  <div class="page-wrap">
    <?php if ($selectedUser): ?>
      <header class="header-inline" role="banner" aria-label="Page header">
        <button type="button" class="back-btn" onclick="location.href='viewUserAccount.php';" aria-label="Go back">← Back</button>
        <div class="page-title" role="heading" aria-level="1">User Account Details</div>
      </header>

      <main class="card" role="main" aria-labelledby="user-details-heading">
        <h2 id="user-details-heading" class="page-heading">Account Information</h2>

        <section class="detail-grid" aria-label="User details">
          <div>
            <div class="muted">Name</div>
            <div class="detail-value"><?= htmlspecialchars($selectedUser['name']) ?></div>
          </div>
          <div>
            <div class="muted">Email</div>
            <div class="detail-value"><?= htmlspecialchars($selectedUser['email']) ?></div>
          </div>
          <div>
            <div class="muted">Role</div>
            <div class="detail-value"><?= htmlspecialchars($selectedUser['role']) ?></div>
          </div>
          <div>
            <div class="muted">Status</div>
            <div class="detail-value"><?= htmlspecialchars($selectedUser['status']) ?></div>
          </div>
        </section>
      </main>

    <?php else: ?>
      <header class="header-inline" role="banner" aria-label="Page header">
        <button type="button" class="back-btn" onclick="location.href='../../../Misc/Dashboards/adminDash.php';" aria-label="Go back">← Back</button>
        <div class="page-title" role="heading" aria-level="1">User Accounts</div>
        <div class="right-spacer"></div>
      </header>

      <main class="card" role="main" aria-labelledby="all-users-heading">
        <div class="card-heading">
          <div class="card-title" id="all-users-heading">All Users</div>
          <div class="muted card-subtitle">Showing <?= count($users) ?> users</div>
        </div>

        <div class="table-wrapper" role="region" aria-label="User accounts list">
          <table class="data-table" role="table" aria-label="User accounts table">
            <thead>
              <tr>
                <th scope="col">Name</th>
                <th scope="col">Status</th>
                <th scope="col" style="width:140px">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($users)): ?>
                <tr><td colspan="3" class="no-data">No users found.</td></tr>
              <?php else: ?>
                <?php foreach ($users as $u): ?>
                  <tr>
                    <td data-label="Name"><?= htmlspecialchars($u['name']) ?></td>
                    <td data-label="Status"><?= htmlspecialchars($u['status']) ?></td>
                    <td data-label="Action">
                      <a class="btn ghost table-btn" href="viewUserAccount.php?id=<?= urlencode($u['userID']) ?>">View</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </main>
    <?php endif; ?>
  </div>
</body>
</html>
