<?php
session_start();

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'Platform Manager') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../../../PlatformManager/Controller/Category/UpdateCategoryCon.php';

$controller = new UpdateCategoryCon($conn);
$message = "";
$selectedCategory = null;

// get all categories for display
$categories = $controller->getAllCategories();

// use fetched data to find the category we want to update
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    foreach ($categories as $c) {
        if ($c['categoryID'] == (int)$_GET['id']) {
            $selectedCategory = $c;
            break;
        }
    }
}

// calls to update the form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['categoryID'])) {
    $id = (int)$_POST['categoryID'];
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($name) || empty($description)) {
        $message = "All fields are required.";
    } else {
        $message = $controller->updateCategory($id, $name, $description);

        // updates the values to it displays changes without refetching
        foreach ($categories as &$c) {
            if ($c['categoryID'] == $id) {
                $c['name'] = $name;
                $c['description'] = $description;
                $selectedCategory = $c;
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
    <title>Update Categories</title>
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
      <?php if ($selectedCategory): ?>
        <!-- When editing a category -->
        <button type="button" class="back-btn" onclick="location.href='updateCategory.php';" aria-label="Go back">← Back</button>
        <div class="page-title" role="heading" aria-level="1">Edit Category</div>
      <?php else: ?>
        <!-- When viewing the list -->
        <button type="button" class="back-btn" onclick="location.href='../../../Misc/Dashboards/pmDash.php';" aria-label="Go back">← Back</button>
        <div class="page-title" role="heading" aria-level="1">Update Volunteer Service Categories</div>
      <?php endif; ?>
    </header>


    <?php if ($message): ?>
      <div class="card">
        <p class="message"><?= htmlspecialchars($message) ?></p>
      </div>
    <?php endif; ?>

    <?php if ($selectedCategory): ?>
      <main class="card" role="main" aria-labelledby="edit-category-heading">
        <h2 id="edit-category-heading" class="page-heading">Edit Category</h2>

        <form method="POST" class="form single-column">
          <input type="hidden" name="categoryID" value="<?= htmlspecialchars($selectedCategory['categoryID']) ?>">

          <div class="form-row">
            <div class="field">
              <label class="label">Category Name</label>
              <input type="text" name="name" value="<?= htmlspecialchars($selectedCategory['name']) ?>" class="input" required>
            </div>
          </div>

          <div class="form-row">
            <div class="field">
              <label class="label">Description</label>
              <textarea name="description" class="textarea" required><?= htmlspecialchars($selectedCategory['description']) ?></textarea>
            </div>
          </div>

          <div class="button-row">
            <button type="submit" class="btn">Save Changes</button>
          </div>
        </form>
      </main>

    <?php else: ?>
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
                <th scope="col" style="width:140px">Action</th>
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
                    <td data-label="Action">
                      <a class="btn ghost table-btn" href="updateCategory.php?id=<?= urlencode($c['categoryID']) ?>">Edit</a>
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
