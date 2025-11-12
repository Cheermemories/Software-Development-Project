<?php
session_start();
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'CSR Rep') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../../CSRRep/Controller/ShortlistRequestCsrCon.php';

$csrID = $_SESSION['userID'];
$controller = new ShortlistRequestCsrCon($conn);
$message = "";

// get all active requests
$requests = $controller->getActiveRequests($csrID);

// add or remove from shortlist
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['requestID'])) {
    $requestID = (int)$_POST['requestID'];
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $message = $controller->addToShortlist($csrID, $requestID);
    } elseif ($action === 'remove') {
        $message = $controller->removeFromShortlist($csrID, $requestID);
    }

    // get all active requests again to reflect changes
    $requests = $controller->getActiveRequests($csrID);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Shortlist Requests</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <link rel="stylesheet" href="../../assets/css/table.css">
    <style>
        .message {
            margin-top: 16px;
            text-align: center;
            font-weight: 600;
            color: green;
        }

        main.card {
            padding: 24px 32px;
        }

        table form {
            display: inline;
        }

        .btn.add {
            background-color: #3498db;
            color: white;
        }

        .btn.remove {
            background-color: #e74c3c;
            color: white;
        }

        .btn.add:hover {
            background-color: #2980b9;
        }

        .btn.remove:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>
  <div class="page-wrap">
    <header class="header-inline" role="banner" aria-label="Page header">
      <button type="button" class="back-btn" onclick="location.href='../../Misc/Dashboards/csrrepDash.php';" aria-label="Go back">← Back</button>
      <div class="page-title" role="heading" aria-level="1">Shortlist Requests</div>
    </header>

    <main class="card" role="main" aria-labelledby="shortlist-heading">
      <h2 id="shortlist-heading" class="page-heading">Active Requests</h2>

      <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
      <?php endif; ?>

      <div class="table-wrapper" role="region">
        <table class="data-table" role="table" aria-label="Active requests table">
          <thead>
            <tr>
              <th scope="col">Title</th>
              <th scope="col">Description</th>
              <th scope="col">Category</th>
              <th scope="col">Date Created</th>
              <th scope="col">Shortlist</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($requests)): ?>
              <tr><td colspan="5" class="no-data">No active requests found.</td></tr>
            <?php else: ?>
              <?php foreach ($requests as $r): ?>
                <tr>
                  <td data-label="Title"><?= htmlspecialchars($r['title']) ?></td>
                  <td data-label="Description"><?= htmlspecialchars($r['description']) ?></td>
                  <td data-label="Category"><?= htmlspecialchars($r['categoryName'] ?? 'N/A') ?></td>
                  <td data-label="Date Created"><?= htmlspecialchars($r['dateCreated']) ?></td>
                  <td data-label="Shortlist">
                    <form method="POST">
                        <input type="hidden" name="requestID" value="<?= htmlspecialchars($r['requestID']) ?>">
                        <?php if ($r['isShortlisted']): ?>
                            <button type="submit" name="action" value="remove" class="btn remove">Remove</button>
                        <?php else: ?>
                            <button type="submit" name="action" value="add" class="btn add">Add</button>
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
