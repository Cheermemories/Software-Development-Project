<?php
session_start();

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'Platform Manager') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../../../PlatformManager/Controller/Category/SuspendCategoryCon.php';

$controller = new SuspendCategoryCon($conn);
$message = "";

// get all categories
$categories = $controller->getAllCategories();

// call to suspend or active the category
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['categoryID'] ?? 0);

    if (isset($_POST['toggle'])) {
        $message = $controller->toggleStatus($id);

        // update the value without needing to refetch data
        foreach ($categories as &$c) {
            if ($c['categoryID'] == $id) {
                $c['status'] = ($c['status'] === 'Active') ? 'Inactive' : 'Active';
                break;
            }
        }
        unset($c);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Categories</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <link rel="stylesheet" href="../../../assets/css/table.css">
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
        .btn.suspend {
            background-color: #f39c12;
            color: #fff;
            border: 1px solid #d48806;
        }
        .btn.suspend:hover {
            background-color: #d48806;
        }
        .btn.activate {
            background-color: #28a745;
            color: #fff;
            border: 1px solid #218838;
        }
        .btn.activate:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
  <div class="page-wrap">
    <header class="header-inline" role="banner" aria-label="Page header">
      <button type="button" class="back-btn" onclick="location.href='../../../Misc/Dashboards/pmDash.php';" aria-label="Go back">← Back</button>
      <div class="page-title" role="heading" aria-level="1">Manage Volunteer Service Categories</div>
      <div class="right-spacer"></div>
    </header>

    <?php if ($message): ?>
        <div class="card">
            <p class="message"><?= htmlspecialchars($message) ?></p>
        </div>
    <?php endif; ?>

    <main class="card" role="main" aria-labelledby="category-list-heading">
      <div class="card-heading">
        <div class="card-title" id="category-list-heading">All Categories</div>
        <div class="muted card-subtitle">Showing <?= count($categories) ?> categories</div>
      </div>

      <div class="table-wrapper" role="region" aria-label="Category list">
        <table class="data-table" role="table" aria-label="Category table">
          <thead>
            <tr>
              <th scope="col">Category Name</th>
              <th scope="col">Description</th>
              <th scope="col">Status</th>
              <th scope="col" style="width:140px">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($categories)): ?>
              <tr><td colspan="4" class="no-data">No categories found.</td></tr>
            <?php else: ?>
              <?php foreach ($categories as $c): ?>
                <tr>
                  <td data-label="Category Name"><?= htmlspecialchars($c['name']) ?></td>
                  <td data-label="Description"><?= htmlspecialchars($c['description']) ?></td>
                  <td data-label="Status"><?= htmlspecialchars($c['status']) ?></td>
                  <td data-label="Actions">
                    <form method="POST">
                      <input type="hidden" name="categoryID" value="<?= htmlspecialchars($c['categoryID']) ?>">
                      <?php if ($c['status'] === 'Active'): ?>
                        <button type="submit" name="toggle" class="btn table-btn suspend">Suspend</button>
                      <?php else: ?>
                        <button type="submit" name="toggle" class="btn table-btn activate">Activate</button>
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
