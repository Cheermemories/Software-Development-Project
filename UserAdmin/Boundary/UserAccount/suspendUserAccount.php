<?php
session_start();

// only allow logged-in User Admins
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'User Admin') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../../Controller/UserAccount/SuspendUserAccountCon.php';

$controller = new SuspendUserAccountCon($conn);
$message = "";

// fetch all users
$users = $controller->getAllUsers();

// calls controller to change status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['userID'] ?? 0);

    if (isset($_POST['toggle'])) {
        $message = $controller->toggleStatus($id);
    }

    // overwrite the status to show what was updated within the boundry to avoid refetching data
    foreach ($users as &$u) {
        if ($u['userID'] == $id) {
            $u['status'] = ($u['status'] === 'Active') ? 'Inactive' : 'Active';
            break;
        }
    }
    unset($u);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Suspend User Accounts</title>
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
        .message.success {
            color: var(--primary);
        }
        .message.error {
            color: var(--danger);
        }
        .btn.suspend {
            background-color: #f39c12;
            color: #fff;
            border: 1px solid #d48806;
        }
        .btn.suspend:hover {
            background-color: #d48806;
        }
        .btn.activate {
            background-color: #28a745;
            color: #fff;
            border: 1px solid #218838;
        }
        .btn.activate:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
  <div class="page-wrap">
    <header class="header-inline" role="banner" aria-label="Page header">
      <button type="button" class="back-btn" onclick="location.href='../../../Misc/Dashboards/adminDash.php';" aria-label="Go back">← Back</button>
      <div class="page-title" role="heading" aria-level="1">Manage User Accounts</div>
      <div class="right-spacer"></div>
    </header>

    <?php if ($message): ?>
        <div class="card">
            <p class="message"><?= htmlspecialchars($message) ?></p>
        </div>
    <?php endif; ?>

    <main class="card" role="main" aria-labelledby="user-list-heading">
      <div class="card-heading">
        <div class="card-title" id="user-list-heading">All Users</div>
        <div class="muted card-subtitle">Showing <?= count($users) ?> users</div>
      </div>

      <div class="table-wrapper" role="region" aria-label="User accounts list">
        <table class="data-table" role="table" aria-label="User accounts table">
          <thead>
            <tr>
              <th scope="col">User ID</th>
              <th scope="col">Name</th>
              <th scope="col">Email</th>
              <th scope="col">Role</th>
              <th scope="col">Status</th>
              <th scope="col" style="width:140px">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($users)): ?>
              <tr><td colspan="6" class="no-data">No users found.</td></tr>
            <?php else: ?>
              <?php foreach ($users as $u): ?>
                <tr>
                  <td data-label="User ID"><?= htmlspecialchars($u['userID']) ?></td>
                  <td data-label="Name"><?= htmlspecialchars($u['name']) ?></td>
                  <td data-label="Email"><?= htmlspecialchars($u['email']) ?></td>
                  <td data-label="Role"><?= htmlspecialchars($u['role']) ?></td>
                  <td data-label="Status"><?= htmlspecialchars($u['status']) ?></td>
                  <td data-label="Actions">
                    <form method="POST">
                      <input type="hidden" name="userID" value="<?= htmlspecialchars($u['userID']) ?>">
                      <?php if ($u['status'] === 'Active'): ?>
                        <button type="submit" name="toggle" class="btn table-btn suspend">Suspend</button>
                      <?php else: ?>
                        <button type="submit" name="toggle" class="btn table-btn activate">Activate</button>
                      <?php endif; ?>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</body>
</html>
