<?php
session_start();

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'Platform Manager') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../../../PlatformManager/Controller/Category/ViewCategoryCon.php';

$controller = new ViewCategoryCon($conn);
$categories = $controller->getAllCategories();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Categories</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <link rel="stylesheet" href="../../../assets/css/table.css">
    <style>
        main.card {
            padding: 24px 32px;
        }

        .back-btn {
            margin-right: 10px;
        }

        .no-data {
            text-align: center;
            color: var(--muted);
        }

        .card-heading .card-subtitle {
            font-size: 0.9rem;
            color: var(--muted);
        }
    </style>
</head>
<body>
  <div class="page-wrap">
    <header class="header-inline" role="banner" aria-label="Page header">
      <button type="button" class="back-btn" onclick="location.href='../../../Misc/Dashboards/pmDash.php';" aria-label="Go back">← Back</button>
      <div class="page-title" role="heading" aria-level="1">Volunteer Service Categories</div>
    </header>

    <main class="card" role="main" aria-labelledby="category-list-heading">
      <div class="card-heading">
        <div class="card-title" id="category-list-heading">All Categories</div>
        <div class="muted card-subtitle">Showing <?= count($categories) ?> categories</div>
      </div>

      <div class="table-wrapper" role="region" aria-label="Category list">
        <table class="data-table" role="table" aria-label="Categories table">
          <thead>
            <tr>
              <th scope="col">Category ID</th>
              <th scope="col">Name</th>
              <th scope="col">Description</th>
              <th scope="col">Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($categories)): ?>
              <tr><td colspan="4" class="no-data">No categories found.</td></tr>
            <?php else: ?>
              <?php foreach ($categories as $c): ?>
                <tr>
                  <td data-label="Category ID"><?= htmlspecialchars($c['categoryID']) ?></td>
                  <td data-label="Name"><?= htmlspecialchars($c['name']) ?></td>
                  <td data-label="Description"><?= htmlspecialchars($c['description']) ?></td>
                  <td data-label="Status"><?= htmlspecialchars($c['status'] ?? 'Active') ?></td>
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
