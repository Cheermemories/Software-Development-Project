<?php
session_start();

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'User Admin') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../../Controller/UserProfile/SuspendUserProfileCon.php';

$controller = new SuspendUserProfileCon($conn);
$message = "";

// fetch all profiles
$profiles = $controller->getAllProfiles();

// calls controller to change status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['profileID'] ?? 0);

    if (isset($_POST['toggle'])) {
        $message = $controller->toggleStatus($id);
    }

    // overwrite the status to show what was updated within the boundry to avoid refetching data
    if (isset($_POST['toggle'])) {
        foreach ($profiles as &$p) {
            if ($p['profileID'] == $id) {
                $p['status'] = ($p['status'] === 'Active') ? 'Inactive' : 'Active';
                break;
            }
        }
        unset($p);
    } elseif (isset($_POST['delete'])) {
        $profiles = array_filter($profiles, fn($p) => $p['profileID'] != $id);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage User Profiles</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <link rel="stylesheet" href="../../../assets/css/table.css">
    <style>
        .message {
            margin: 16px;
            text-align: center;
            font-weight: 600;
            color: var(--muted);
        }

        main.card {
            padding: 24px 32px;
        }

        form.inline-action {
            display: inline-block;
        }

        .btn.suspend {
            background-color: #f39c12;
            border: 1px solid #d6860f;
        }

        .btn.suspend:hover {
            background-color: #e58f0d;
        }

        .btn.activate {
            background-color: #28a745;
            border: 1px solid #1e7e34;
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
      <div class="page-title" role="heading" aria-level="1">Manage User Profiles</div>
    </header>

    <?php if ($message): ?>
      <div class="card">
        <div class="message"><?= htmlspecialchars($message) ?></div>
      </div>
    <?php endif; ?>

    <main class="card" role="main" aria-labelledby="profiles-list-heading">
      <div class="card-heading">
        <div class="card-title" id="profiles-list-heading">All Profiles</div>
        <div class="muted card-subtitle">Showing <?= count($profiles) ?> profiles</div>
      </div>

      <div class="table-wrapper" role="region" aria-label="Profiles list">
        <table class="data-table" role="table" aria-label="Profiles table">
          <thead>
            <tr>
              <th scope="col">Role</th>
              <th scope="col">Status</th>
              <th scope="col" style="width:180px">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($profiles)): ?>
              <tr><td colspan="3" class="no-data">No profiles found.</td></tr>
            <?php else: ?>
              <?php foreach ($profiles as $p): ?>
                <tr>
                  <td data-label="Role"><?= htmlspecialchars($p['role']) ?></td>
                  <td data-label="Status"><?= htmlspecialchars($p['status']) ?></td>
                  <td data-label="Actions">
                    <form method="POST" class="inline-action">
                      <input type="hidden" name="profileID" value="<?= htmlspecialchars($p['profileID']) ?>">
                      <?php if ($p['status'] === 'Active'): ?>
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
