<?php
session_start();

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'User Admin') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../../Controller/UserProfile/ViewUserProfileCon.php';

$controller = new ViewUserProfileCon($conn);

// calls getAllProfiles function in the controller
$profiles = $controller->getAllProfiles();

// pulls from the fetched array to select a specific profile
$selectedProfile = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    foreach ($profiles as $p) {
        if ($p['profileID'] == (int)$_GET['id']) {
            $selectedProfile = $p;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View User Profiles</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <link rel="stylesheet" href="../../../assets/css/table.css">
    <style>
        main.card {
            padding: 24px 32px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
        }

        .detail-grid .muted {
            font-weight: 600;
            color: var(--muted);
        }

        .detail-value {
            margin-top: 4px;
            font-weight: 600;
            color: #0f172a;
        }

        .detail-section {
            margin-top: 20px;
        }

        .detail-section .muted {
            font-weight: 600;
            color: var(--muted);
        }

        .detail-section .detail-value {
            margin-top: 4px;
            white-space: pre-wrap;
            text-align: left;
            font-weight: 600;
            color: #0f172a;
        }

        .no-data {
            text-align: center;
            font-style: italic;
        }
    </style>
</head>
<body>
  <div class="page-wrap">
    <?php if ($selectedProfile): ?>
      <header class="header-inline" role="banner" aria-label="Page header">
        <button type="button" class="back-btn" onclick="location.href='viewUserProfile.php';" aria-label="Go back">← Back</button>
        <div class="page-title" role="heading" aria-level="1">Profile Details</div>
      </header>

      <main class="card" role="main" aria-labelledby="profile-details-heading">
        <h2 id="profile-details-heading" class="page-heading">Profile Information</h2>

        <section class="detail-grid" aria-label="Profile details">
          <div>
            <div class="muted">Role</div>
            <div class="detail-value"><?= htmlspecialchars($selectedProfile['role']) ?></div>
          </div>
          <div>
            <div class="muted">Status</div>
            <div class="detail-value"><?= htmlspecialchars($selectedProfile['status']) ?></div>
          </div>
        </section>

        <div class="detail-section">
          <div class="muted">Permissions</div>
          <div class="detail-value"><?= nl2br(htmlspecialchars($selectedProfile['permissions'])) ?></div>
        </div>

        <div class="detail-section">
          <div class="muted">Description</div>
          <div class="detail-value"><?= nl2br(htmlspecialchars($selectedProfile['description'])) ?></div>
        </div>
      </main>

    <?php else: ?>
      <header class="header-inline" role="banner" aria-label="Page header">
        <button type="button" class="back-btn" onclick="location.href='../../../Misc/Dashboards/adminDash.php';" aria-label="Go back">← Back</button>
        <div class="page-title" role="heading" aria-level="1">User Profiles</div>
        <div class="right-spacer"></div>
      </header>

      <main class="card" role="main" aria-labelledby="all-profiles-heading">
        <div class="card-heading">
          <div class="card-title" id="all-profiles-heading">All Profiles</div>
          <div class="muted card-subtitle">Showing <?= count($profiles) ?> profiles</div>
        </div>

        <div class="table-wrapper" role="region" aria-label="Profile list">
          <table class="data-table" role="table" aria-label="User profiles table">
            <thead>
              <tr>
                <th scope="col">Role</th>
                <th scope="col">Status</th>
                <th scope="col" style="width:140px">Action</th>
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
                    <td data-label="Action">
                      <a class="btn ghost table-btn" href="viewUserProfile.php?id=<?= urlencode($p['profileID']) ?>">View</a>
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
