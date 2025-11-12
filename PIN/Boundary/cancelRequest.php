<?php
session_start();
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'PIN') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../Controller/CancelRequestCon.php';

$pinID = $_SESSION['userID'];
$controller = new CancelRequestCon($conn);
$message = "";

// fetch active requests
$requests = $controller->getActiveRequests($pinID);

// calls controller to change status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel'])) {
    $id = (int)($_POST['requestID'] ?? 0);
    $message = $controller->cancelRequest($id);

    // update status on page
    foreach ($requests as &$r) {
        if ($r['requestID'] == $id) {
            $r['status'] = 'Cancelled';
        }
    }
    unset($r);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cancel Requests</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <link rel="stylesheet" href="../../assets/css/table.css">
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
        .btn.cancel {
            background-color: #e74c3c;
            color: #fff;
            border: 1px solid #c0392b;
        }
        .btn.cancel:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>
  <div class="page-wrap">
    <header class="header-inline" role="banner" aria-label="Page header">
      <button type="button" class="back-btn" onclick="location.href='../../Misc/Dashboards/pinDash.php';" aria-label="Go back">← Back</button>
      <div class="page-title" role="heading" aria-level="1">Manage Requests</div>
      <div class="right-spacer"></div>
    </header>

    <?php if ($message): ?>
        <div class="card">
            <p class="message"><?= htmlspecialchars($message) ?></p>
        </div>
    <?php endif; ?>

    <main class="card" role="main" aria-labelledby="request-list-heading">
      <div class="card-heading">
        <div class="card-title" id="request-list-heading">Active Requests</div>
        <div class="muted card-subtitle">Showing <?= count($requests) ?> request(s)</div>
      </div>

      <div class="table-wrapper" role="region" aria-label="Requests table">
        <table class="data-table" role="table" aria-label="Requests list">
          <thead>
            <tr>
              <th scope="col">Request ID</th>
              <th scope="col">Title</th>
              <th scope="col">Description</th>
              <th scope="col">Status</th>
              <th scope="col" style="width:140px">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($requests)): ?>
              <tr><td colspan="5" class="no-data">You have no active requests.</td></tr>
            <?php else: ?>
              <?php foreach ($requests as $r): ?>
                <tr>
                  <td data-label="Request ID"><?= htmlspecialchars($r['requestID']) ?></td>
                  <td data-label="Title"><?= htmlspecialchars($r['title']) ?></td>
                  <td data-label="Description"><?= nl2br(htmlspecialchars($r['description'])) ?></td>
                  <td data-label="Status"><?= htmlspecialchars($r['status']) ?></td>
                  <td data-label="Action">
                    <form method="POST">
                      <input type="hidden" name="requestID" value="<?= htmlspecialchars($r['requestID']) ?>">
                      <?php if ($r['status'] === 'Active'): ?>
                        <button type="submit" name="cancel" class="btn table-btn cancel"
                                onclick="return confirm('Are you sure you want to cancel this request?');">
                          Cancel
                        </button>
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
