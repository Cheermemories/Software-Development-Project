<?php
session_start();
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'CSR Rep') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../../CSRRep/Controller/SearchShortlistRequestCsrCon.php';

$csrID = $_SESSION['userID'];
$controller = new SearchShortlistRequestCsrCon($conn);

$results = [];
$message = "";

$title = $_GET['title'] ?? '';
$description = $_GET['description'] ?? '';
$category = $_GET['category'] ?? '';
$pinName = $_GET['pinName'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && (isset($_GET['search']) || $title || $description || $category || $pinName)) {
    $results = $controller->searchShortlistedRequests($csrID, $title, $description, $category, $pinName);
    if (empty($results)) {
        $message = "No matching shortlisted requests found.";
    }
}

$selectedRequest = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    foreach ($results as $r) {
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
    <title>Search My Shortlist</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <link rel="stylesheet" href="../../assets/css/table.css">
    <style>
        .message {
            margin-top: 16px;
            text-align: center;
            font-weight: 600;
            color: var(--danger);
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
      <button type="button" class="back-btn" onclick="location.href='../../Misc/Dashboards/csrrepDash.php';" aria-label="Go back">← Back</button>
      <div class="page-title" role="heading" aria-level="1">Search My Shortlist</div>
    </header>

    <main class="card" role="main" aria-labelledby="search-shortlist-heading">
      <h2 id="search-shortlist-heading" class="page-heading">Search Criteria</h2>

      <form method="GET" class="form single-column">
        <div class="form-row">
          <div class="field">
            <label class="label" for="title">Title</label>
            <input type="text" name="title" id="title" value="<?= htmlspecialchars($title) ?>" class="input">
          </div>
        </div>

        <div class="form-row">
          <div class="field">
            <label class="label" for="pinName">Name of PIN</label>
            <input type="text" name="pinName" id="pinName" value="<?= htmlspecialchars($pinName) ?>" class="input">
          </div>
        </div>

        <div class="form-row">
          <div class="field">
            <label class="label" for="description">Description</label>
            <textarea name="description" id="description" rows="3" class="textarea"><?= htmlspecialchars($description) ?></textarea>
          </div>
        </div>

        <div class="form-row">
          <div class="field">
            <label class="label" for="category">Category</label>
            <input type="text" name="category" id="category" value="<?= htmlspecialchars($category) ?>" class="input">
          </div>
        </div>

        <div class="button-row">
          <button type="submit" name="search" class="btn">Search</button>
        </div>
      </form>

      <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
      <?php endif; ?>
    </main>

    <?php if (!empty($results)): ?>
      <main class="card" role="region" aria-label="Search results">
        <div class="card-heading">
          <div class="card-title">Search Results</div>
          <div class="muted card-subtitle">Showing <?= count($results) ?> result(s)</div>
        </div>

        <div class="table-wrapper" role="region">
          <table class="data-table" role="table" aria-label="Search shortlisted requests table">
            <thead>
              <tr>
                <th scope="col">Title</th>
                <th scope="col">PIN Name</th>
                <th scope="col">Category</th>
                <th scope="col">Date Created</th>
                <th scope="col">Description</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($results as $r): ?>
                <tr>
                  <td data-label="Title"><?= htmlspecialchars($r['title']) ?></td>
                  <td data-label="PIN Name"><?= htmlspecialchars($r['pinName'] ?? 'Unknown') ?></td>
                  <td data-label="Category"><?= htmlspecialchars($r['categoryName'] ?? 'N/A') ?></td>
                  <td data-label="Date Created"><?= htmlspecialchars($r['dateCreated']) ?></td>
                  <td data-label="Description"><?= nl2br(htmlspecialchars($r['description'])) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </main>
    <?php endif; ?>
  </div>
</body>
</html>
