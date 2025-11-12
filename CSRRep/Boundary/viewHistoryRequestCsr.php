<?php
session_start();

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'CSR Rep') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../../CSRRep/Controller/ViewHistoryRequestCsrCon.php';

$controller = new ViewHistoryRequestCsrCon($conn);

// get all completed requests
$completedRequests = $controller->getAllCompletedRequests();

// collect unique categories for filter dropdown
$categories = [];
foreach ($completedRequests as $r) {
    if (!empty($r['categoryID']) && !isset($categories[$r['categoryID']])) {
        $categories[$r['categoryID']] = $r['categoryName'] ?? 'Unknown';
    }
}

// get filter inputs
$selectedCategory = $_GET['category'] ?? '';
$startDate = $_GET['startDate'] ?? '';
$endDate   = $_GET['endDate'] ?? '';

// apply filters
if (!empty($selectedCategory)) {
    $completedRequests = array_filter($completedRequests, fn($r) => $r['categoryID'] == $selectedCategory);
}
if (!empty($startDate)) {
    $completedRequests = array_filter($completedRequests, fn($r) => $r['dateCompleted'] >= $startDate);
}
if (!empty($endDate)) {
    $completedRequests = array_filter($completedRequests, fn($r) => $r['dateCompleted'] <= $endDate);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Completed Matches History</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <link rel="stylesheet" href="../../assets/css/table.css">
    <style>
        main.card {
            padding: 24px 32px;
        }
        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: flex-end;
            margin-bottom: 16px;
        }
        .filter-form .field {
            display: flex;
            flex-direction: column;
        }
        .filter-form label {
            font-weight: 600;
            margin-bottom: 4px;
        }
        .filter-form select,
        .filter-form input {
            padding: 6px;
            min-width: 160px;
        }
        .filter-form button {
            height: 36px;
            padding: 0 14px;
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
      <div class="page-title" role="heading" aria-level="1">Completed Matches History</div>
      <div class="right-spacer"></div>
    </header>

    <main class="card" role="main" aria-labelledby="history-heading">
      <h2 id="history-heading" class="page-heading">Completed Requests</h2>

      <form method="GET" class="filter-form" role="search" aria-label="Filter requests">
        <div class="field">
          <label for="category">Service Type</label>
          <select name="category" id="category" class="select">
            <option value="">All Categories</option>
            <?php foreach ($categories as $id => $name): ?>
              <option value="<?= htmlspecialchars($id) ?>" <?= $selectedCategory == $id ? 'selected' : '' ?>>
                <?= htmlspecialchars($name) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="field">
          <label for="startDate">From</label>
          <input type="date" name="startDate" id="startDate" value="<?= htmlspecialchars($startDate) ?>">
        </div>

        <div class="field">
          <label for="endDate">To</label>
          <input type="date" name="endDate" id="endDate" value="<?= htmlspecialchars($endDate) ?>">
        </div>

        <button type="submit" class="btn">Apply Filters</button>
      </form>

      <div class="table-wrapper" role="region" aria-label="Completed requests table">
        <table class="data-table" role="table">
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
            <?php if (empty($completedRequests)): ?>
              <tr><td colspan="5" class="no-data">No completed requests found for the selected filters.</td></tr>
            <?php else: ?>
              <?php foreach ($completedRequests as $r): ?>
                <tr>
                  <td data-label="Title"><?= htmlspecialchars($r['title']) ?></td>
                  <td data-label="Category"><?= htmlspecialchars($r['categoryName'] ?? 'N/A') ?></td>
                  <td data-label="Description"><?= nl2br(htmlspecialchars($r['description'])) ?></td>
                  <td data-label="PIN Name"><?= htmlspecialchars($r['pinName'] ?? 'Unknown') ?></td>
                  <td data-label="Date Completed"><?= htmlspecialchars($r['dateCompleted'] ?? '-') ?></td>
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
