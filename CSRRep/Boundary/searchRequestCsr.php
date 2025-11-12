<?php
session_start();

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'CSR Rep') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../../CSRRep/Controller/SearchRequestCsrCon.php';

$controller = new SearchRequestCsrCon($conn);

$results = [];
$message = "";

$title = $_GET['title'] ?? '';
$description = $_GET['description'] ?? '';
$category = $_GET['category'] ?? '';
$status = $_GET['status'] ?? '';
$pinName = $_GET['pinName'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && (isset($_GET['search']) || $title || $description || $category || $status || $pinName)) {
    $results = $controller->searchRequests($title, $description, $category, $status, $pinName);

    if (empty($results)) {
        $message = "No matching requests found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Requests</title>
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
      <div class="page-title" role="heading" aria-level="1">Search Requests</div>
    </header>

    <main class="card" role="main" aria-labelledby="search-requests-heading">
      <h2 id="search-requests-heading" class="page-heading">Search Criteria</h2>

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
            <textarea name="description" id="description" class="textarea"><?= htmlspecialchars($description) ?></textarea>
          </div>
        </div>

        <div class="form-row">
          <div class="field">
            <label class="label" for="category">Category</label>
            <input type="text" name="category" id="category" value="<?= htmlspecialchars($category) ?>" class="input">
          </div>
        </div>

        <div class="form-row">
          <div class="field">
            <label class="label" for="status">Status</label>
            <select name="status" id="status" class="select">
              <option value="">All</option>
              <option value="Active" <?= $status === 'Active' ? 'selected' : '' ?>>Active</option>
              <option value="Matched" <?= $status === 'Matched' ? 'selected' : '' ?>>Matched</option>
              <option value="Completed" <?= $status === 'Completed' ? 'selected' : '' ?>>Completed</option>
              <option value="Cancelled" <?= $status === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>
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
          <table class="data-table" role="table" aria-label="Search results table">
            <thead>
              <tr>
                <th scope="col">Title</th>
                <th scope="col">PIN Name</th>
                <th scope="col">Category</th>
                <th scope="col">Request Date</th>
                <th scope="col">Description</th>
                <th scope="col">Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($results as $r): ?>
                <tr>
                  <td data-label="Title"><?= htmlspecialchars($r['title']) ?></td>
                  <td data-label="PIN Name"><?= htmlspecialchars($r['pinName'] ?? 'Unknown') ?></td>
                  <td data-label="Category"><?= htmlspecialchars($r['categoryName'] ?? 'N/A') ?></td>
                  <td data-label="Request Date"><?= htmlspecialchars(date('Y-m-d', strtotime($r['dateCreated']))) ?></td>
                  <td data-label="Description"><?= htmlspecialchars($r['description']) ?></td>
                  <td data-label="Status"><?= htmlspecialchars($r['status']) ?></td>
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
