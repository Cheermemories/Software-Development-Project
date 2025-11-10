<?php
session_start();
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'PIN') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../Controller/UpdateRequestCon.php';

$controller = new UpdateRequestCon($conn);
$message = "";
$selectedRequest = null;

$pinID = $_SESSION['userID'];

// get all requests from this PIN
$requests = $controller->getAllRequests($pinID);

// get all categories for dropdown
$categories = $controller->getAllCategories();

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    foreach ($requests as $r) {
        if ($r['requestID'] == (int)$_GET['id']) {
            $selectedRequest = $r;
            break;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['requestID'])) {
    $id = (int)$_POST['requestID'];
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $categoryID = (int)($_POST['categoryID'] ?? 0);
    
    $message = $controller->updateRequest($id, $title, $description, $categoryID);

    // updates the values so you don't need to refresh to see changes after update
    foreach ($requests as &$r) {
        if ($r['requestID'] == $id) {
            $r['title'] = $title;
            $r['description'] = $description;
            $r['categoryID'] = $categoryID;
            $selectedRequest = $r;
            break;
        }
    }
    unset($r);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Requests</title>
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
        main.card {
            padding: 24px 32px;
        }
        .form.single-column .form-row {
            display: block;
        }
        .form.single-column .field {
            width: 100%;
            margin-bottom: 18px;
        }
        .button-row {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            gap: 8px;
            margin-top: 8px;
        }
    </style>
</head>
<body>
  <div class="page-wrap">
    <header class="header-inline" role="banner" aria-label="Page header">
      <?php if ($selectedRequest): ?>
        <button type="button" class="back-btn" onclick="location.href='updateRequest.php';" aria-label="Go back">← Back</button>
        <div class="page-title" role="heading" aria-level="1">Edit Request</div>
      <?php else: ?>
        <button type="button" class="back-btn" onclick="location.href='../../Misc/Dashboards/pinDash.php';" aria-label="Go back">← Back</button>
        <div class="page-title" role="heading" aria-level="1">Update Requests</div>
      <?php endif; ?>
    </header>

    <?php if ($message): ?>
      <div class="card">
        <p class="message"><?= htmlspecialchars($message) ?></p>
      </div>
    <?php endif; ?>

    <?php if ($selectedRequest): ?>
      <main class="card" role="main" aria-labelledby="edit-request-heading">
        <h2 id="edit-request-heading" class="page-heading">Edit Request</h2>

        <form method="POST" class="form single-column">
          <input type="hidden" name="requestID" value="<?= htmlspecialchars($selectedRequest['requestID']) ?>">

          <div class="form-row">
            <div class="field">
              <label class="label">Title</label>
              <input type="text" name="title" value="<?= htmlspecialchars($selectedRequest['title']) ?>" class="input" required>
            </div>
          </div>

          <div class="form-row">
            <div class="field">
              <label class="label">Description</label>
              <textarea name="description" class="textarea" required><?= htmlspecialchars($selectedRequest['description']) ?></textarea>
            </div>
          </div>

          <div class="form-row">
            <div class="field">
              <label class="label">Category</label>
              <select name="categoryID" class="select" required>
                <?php foreach ($categories as $c): ?>
                  <option value="<?= htmlspecialchars($c['categoryID']) ?>" <?= $c['categoryID'] == $selectedRequest['categoryID'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="button-row">
            <button type="submit" class="btn">Save Changes</button>
          </div>
        </form>
      </main>

    <?php else: ?>
      <main class="card" role="main" aria-labelledby="request-list-heading">
        <div class="card-heading">
          <div class="card-title" id="request-list-heading">My Requests</div>
          <div class="muted card-subtitle">Showing <?= count($requests) ?> requests</div>
        </div>

        <div class="table-wrapper" role="region" aria-label="Requests list">
          <table class="data-table" role="table" aria-label="Requests table">
            <thead>
              <tr>
                <th scope="col">Title</th>
                <th scope="col">Category</th>
                <th scope="col">Description</th>
                <th scope="col" style="width:140px">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($requests)): ?>
                <tr><td colspan="4" class="no-data">No requests found.</td></tr>
              <?php else: ?>
                <?php foreach ($requests as $r): ?>
                  <tr>
                    <td data-label="Title"><?= htmlspecialchars($r['title']) ?></td>
                    <td data-label="Category"><?= htmlspecialchars($r['categoryName'] ?? 'N/A') ?></td>
                    <td data-label="Description"><?= htmlspecialchars($r['description']) ?></td>
                    <td data-label="Action">
                      <a class="btn ghost table-btn" href="updateRequest.php?id=<?= urlencode($r['requestID']) ?>">Edit</a>
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
