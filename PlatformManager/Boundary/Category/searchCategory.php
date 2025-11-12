<?php
session_start();

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'Platform Manager') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../../../PlatformManager/Controller/Category/SearchCategoryCon.php';

$controller = new SearchCategoryCon($conn);
$results = [];
$message = "";

// store search input
$name = $_GET['name'] ?? '';
$description = $_GET['description'] ?? '';
$status = $_GET['status'] ?? '';

// call for search
if ($_SERVER['REQUEST_METHOD'] === 'GET' && (isset($_GET['search']) || !empty($name) || !empty($description) || !empty($status))) {
    $results = $controller->searchCategories($name, $description, $status);

    if (empty($results)) {
        $message = "No categories found matching your search criteria.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Categories</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <link rel="stylesheet" href="../../../assets/css/table.css">
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
      <button type="button" class="back-btn" onclick="location.href='../../../Misc/Dashboards/pmDash.php';" aria-label="Go back">← Back</button>
      <div class="page-title" role="heading" aria-level="1">Search Volunteer Service Categories</div>
    </header>

    <main class="card" role="main" aria-labelledby="search-category-heading">
      <h2 id="search-category-heading" class="page-heading">Search Criteria</h2>

      <form method="GET" class="form single-column">
        <div class="form-row">
          <div class="field">
            <label class="label" for="name">Category Name</label>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($name) ?>" class="input">
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
            <label class="label" for="status">Status</label>
            <select name="status" id="status" class="select">
              <option value="">All</option>
              <option value="Active" <?= $status === 'Active' ? 'selected' : '' ?>>Active</option>
              <option value="Inactive" <?= $status === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
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
                <th scope="col">Category Name</th>
                <th scope="col">Description</th>
                <th scope="col">Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($results as $r): ?>
                <tr>
                  <td data-label="Category Name"><?= htmlspecialchars($r['name']) ?></td>
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
