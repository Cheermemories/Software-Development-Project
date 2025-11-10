<?php
session_start();
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'PIN') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../Controller/ViewRequestCon.php';

$pinID = $_SESSION['userID'];
$controller = new ViewRequestCon($conn);

// calls getAllRequests function in the controller
$requests = $controller->getAllRequests($pinID);

// pulls from the fetched array to select a specific result
$selectedRequest = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    foreach ($requests as $r) {
        if ($r['requestID'] == (int)$_GET['id']) {
            $selectedRequest = $r;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Requests</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <link rel="stylesheet" href="../../assets/css/table.css">
    <style>
        main.card {
            padding: 24px 32px;
        }
        .message {
            text-align: center;
            margin-top: 16px;
            font-weight: 600;
            color: var(--muted);
        }
    </style>
</head>
<body>
  <div class="page-wrap">
    <?php if ($selectedRequest): ?>
      <header class="header-inline" role="banner" aria-label="Page header">
        <button type="button" class="back-btn" onclick="location.href='viewRequest.php';" aria-label="Go back">← Back</button>
        <div class="page-title" role="heading" aria-level="1">Request Details</div>
      </header>

      <main class="card" role="main" aria-labelledby="request-details-heading">
        <h2 id="request-details-heading" class="page-heading">Request Information</h2>

        <section class="detail-grid" aria-label="Request details">
          <div>
            <div class="muted">Title</div>
            <div class="detail-value"><?= htmlspecialchars($selectedRequest['title']) ?></div>
          </div>
          <div>
            <div class="muted">Request Date</div>
            <div class="detail-value"><?= htmlspecialchars(date('Y-m-d', strtotime($selectedRequest['dateCreated']))) ?></div>
          </div>
          <div>
            <div class="muted">Category</div>
            <div class="detail-value"><?= htmlspecialchars($selectedRequest['categoryName'] ?? 'N/A') ?></div>
          </div>
          <div>
            <div class="muted">Status</div>
            <div class="detail-value"><?= htmlspecialchars($selectedRequest['status']) ?></div>
          </div>
          <div>
            <div class="muted">Views</div>
            <div class="detail-value"><?= htmlspecialchars($selectedRequest['views']) ?></div>
          </div>
          <div>
            <div class="muted">Shortlisted Count</div>
            <div class="detail-value"><?= htmlspecialchars($selectedRequest['shortlistedCount']) ?></div>
          </div>
        </section>
      </main>

    <?php else: ?>
      <header class="header-inline" role="banner" aria-label="Page header">
        <button type="button" class="back-btn" onclick="location.href='../../Misc/Dashboards/pinDash.php';" aria-label="Go back">← Back</button>
        <div class="page-title" role="heading" aria-level="1">My Requests</div>
        <div class="right-spacer"></div>
      </header>

      <main class="card" role="main" aria-labelledby="my-requests-heading">
        <div class="card-heading">
          <div class="card-title" id="my-requests-heading">My Requests</div>
          <div class="muted card-subtitle">Showing <?= count($requests) ?> requests</div>
        </div>

        <div class="table-wrapper" role="region" aria-label="My requests table">
          <table class="data-table" role="table" aria-label="Requests list table">
            <thead>
              <tr>
                <th scope="col">Title</th>
                <th scope="col">Status</th>
                <th scope="col" style="width:140px">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($requests)): ?>
                <tr><td colspan="3" class="no-data">No requests found.</td></tr>
              <?php else: ?>
                <?php foreach ($requests as $r): ?>
                  <tr>
                    <td data-label="Title"><?= htmlspecialchars($r['title']) ?></td>
                    <td data-label="Status"><?= htmlspecialchars($r['status']) ?></td>
                    <td data-label="Action">
                      <a class="btn ghost table-btn" href="viewRequest.php?id=<?= urlencode($r['requestID']) ?>">View</a>
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
