<?php
session_start();
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'CSR Rep') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../../CSRRep/Controller/ViewShortlistRequestCsrCon.php';

$csrID = $_SESSION['userID'];
$controller = new ViewShortlistRequestCsrCon($conn);

// get shortlisted requests from this csr rep
$shortlistedRequests = $controller->getShortlistedRequests($csrID);

$selectedRequest = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    foreach ($shortlistedRequests as $r) {
        if ($r['requestID'] == (int)$_GET['id']) {
            $selectedRequest = $r;
            break;
        }
    }

    // to call for view value increase
    if ($selectedRequest) {
        $controller->incrementViewCount($selectedRequest['requestID']);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Shortlist</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <link rel="stylesheet" href="../../assets/css/table.css">
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
        }
    </style>
</head>
<body>
  <div class="page-wrap">
    <?php if ($selectedRequest): ?>
      <header class="header-inline" role="banner" aria-label="Page header">
        <button type="button" class="back-btn" onclick="location.href='viewShortlistRequestCsr.php';" aria-label="Go back">← Back</button>
        <div class="page-title" role="heading" aria-level="1">Request Details</div>
      </header>

      <main class="card" role="main" aria-labelledby="request-details-heading">
        <h2 id="request-details-heading" class="page-heading">Shortlisted Request Information</h2>

        <section class="detail-grid" aria-label="Request details">
          <div>
            <div class="muted">Title</div>
            <div class="detail-value"><?= htmlspecialchars($selectedRequest['title']) ?></div>
          </div>
          <div>
            <div class="muted">Name of PIN</div>
            <div class="detail-value"><?= htmlspecialchars($selectedRequest['pinName'] ?? 'Unknown') ?></div>
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
            <div class="muted">Date Created</div>
            <div class="detail-value"><?= htmlspecialchars($selectedRequest['dateCreated'] ?? '-') ?></div>
          </div>
          <div style="grid-column: 1 / -1;">
            <div class="muted">Description</div>
              <div class="detail-value"><?= htmlspecialchars($selectedRequest['description']) ?></div>
            </div>
          </div>
        </section>
      </main>

    <?php else: ?>
      <header class="header-inline" role="banner" aria-label="Page header">
        <button type="button" class="back-btn" onclick="location.href='../../Misc/Dashboards/csrrepDash.php';" aria-label="Go back">← Back</button>
        <div class="page-title" role="heading" aria-level="1">My Shortlist</div>
        <div class="right-spacer"></div>
      </header>

      <main class="card" role="main" aria-labelledby="shortlist-heading">
        <div class="card-heading">
          <div class="card-title" id="shortlist-heading">Shortlisted Requests</div>
          <div class="muted card-subtitle">Showing <?= count($shortlistedRequests) ?> request(s)</div>
        </div>

        <div class="table-wrapper" role="region" aria-label="Shortlisted requests list">
          <table class="data-table" role="table" aria-label="Shortlisted requests table">
            <thead>
              <tr>
                <th scope="col">Title</th>
                <th scope="col">PIN Name</th>
                <th scope="col">Status</th>
                <th scope="col" style="width:140px">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($shortlistedRequests)): ?>
                <tr><td colspan="4" class="no-data">No shortlisted requests found.</td></tr>
              <?php else: ?>
                <?php foreach ($shortlistedRequests as $r): ?>
                  <tr>
                    <td data-label="Title"><?= htmlspecialchars($r['title']) ?></td>
                    <td data-label="PIN Name"><?= htmlspecialchars($r['pinName'] ?? 'Unknown') ?></td>
                    <td data-label="Status"><?= htmlspecialchars($r['status']) ?></td>
                    <td data-label="Action">
                      <a class="btn ghost table-btn" href="viewShortlistRequestCsr.php?id=<?= urlencode($r['requestID']) ?>">View</a>
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
