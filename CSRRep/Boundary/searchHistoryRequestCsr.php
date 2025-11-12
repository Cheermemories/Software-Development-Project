<?php
session_start();

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'CSR Rep') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../../CSRRep/Controller/SearchHistoryRequestCsrCon.php';

$controller = new SearchHistoryRequestCsrCon($conn);

// get all completed requests
$completedRequests = $controller->getAllCompletedRequests();

// category list for dropdown
$categories = [];
foreach ($completedRequests as $r) {
    if (!empty($r['categoryID']) && !isset($categories[$r['categoryID']])) {
        $categories[$r['categoryID']] = $r['categoryName'] ?? 'Unknown';
    }
}

// store search inputs
$title = $_GET['title'] ?? '';
$description = $_GET['description'] ?? '';
$category = $_GET['category'] ?? '';
$startDate = $_GET['startDate'] ?? '';
$endDate = $_GET['endDate'] ?? '';

$results = [];
$message = "";

// search and filters
if ($_SERVER['REQUEST_METHOD'] === 'GET' && (isset($_GET['search']) || $title || $description || $category || $startDate || $endDate)) {
    $results = $completedRequests;

    if (!empty($title) || !empty($description)) {
        $results = array_filter($results, function ($r) use ($title, $description) {
            $titleMatch = empty($title) ? true : (stripos($r['title'], $title) !== false);
            $descMatch  = empty($description) ? true : (stripos($r['description'], $description) !== false);
            return $titleMatch && $descMatch;
        });
    }

    if (!empty($category)) {
        $results = array_filter($results, fn($r) => $r['categoryID'] == $category);
    }

    if (!empty($startDate)) {
        $results = array_filter($results, fn($r) => $r['dateCompleted'] >= $startDate);
    }

    if (!empty($endDate)) {
        $results = array_filter($results, fn($r) => $r['dateCompleted'] <= $endDate);
    }

    if (empty($results)) {
        $message = "No matching completed requests found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Completed Matches</title>
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

        .date-fields {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .date-fields .field {
            flex: 1;
        }

        .button-row {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            gap: 8px;
            margin-top: 8px;
        }

        .no-data {
            text-align: center;
            font-style: italic;
        }
    </style>
</head>
<body>
  <div class="page-wrap">
    <header class="header-inline" role="banner" aria-label="Page header">
      <button type="button" class="back-btn" onclick="location.href='../../Misc/Dashboards/csrrepDash.php';" aria-label="Go back">← Back</button>
      <div class="page-title" role="heading" aria-level="1">Search Completed Matches</div>
    </header>

    <main class="card" role="main" aria-labelledby="search-history-heading">
      <h2 id="search-history-heading" class="page-heading">Search Completed Requests</h2>

      <form method="GET" class="form single-column" role="search">
        <div class="form-row">
          <div class="field">
            <label class="label" for="title">Title</label>
            <input type="text" name="title" id="title" value="<?= htmlspecialchars($title) ?>" class="input">
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
            <label class="label" for="category">Service Type</label>
            <select name="category" id="category" class="select">
              <option value="">All Categories</option>
              <?php foreach ($categories as $id => $name): ?>
                <option value="<?= htmlspecialchars($id) ?>" <?= ($category == $id) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($name) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-row date-fields">
          <div class="field">
            <label class="label" for="startDate">From</label>
            <input type="date" name="startDate" id="startDate" value="<?= htmlspecialchars($startDate) ?>" class="input">
          </div>
          <div class="field">
            <label class="label" for="endDate">To</label>
            <input type="date" name="endDate" id="endDate" value="<?= htmlspecialchars($endDate) ?>" class="input">
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
          <table class="data-table" role="table" aria-label="Completed requests results">
            <thead>
              <tr>
                <th scope="col">Title</th>
                <th scope="col">Category</th>
                <th scope="col">Description</th>
                <th scope="col">PIN Name</th>
                <th scope="col">Date Completed</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($results as $r): ?>
                <tr>
                  <td data-label="Title"><?= htmlspecialchars($r['title']) ?></td>
                  <td data-label="Category"><?= htmlspecialchars($r['categoryName'] ?? 'N/A') ?></td>
                  <td data-label="Description"><?= nl2br(htmlspecialchars($r['description'])) ?></td>
                  <td data-label="PIN Name"><?= htmlspecialchars($r['pinName'] ?? 'Unknown') ?></td>
                  <td data-label="Date Completed"><?= htmlspecialchars($r['dateCompleted'] ?? '-') ?></td>
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
