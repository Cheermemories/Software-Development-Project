<?php
session_start();

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'User Admin') {
    header("Location: ../../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../../Controller/UserAccount/SearchUserAccountCon.php';

$controller = new SearchUserAccountCon($conn);
$results = [];
$message = "";

// store search input so it stays after searching
$name = $_GET['name'] ?? '';
$email = $_GET['email'] ?? '';
$role = $_GET['role'] ?? '';
$status = $_GET['status'] ?? '';

// pull roles from profile table
$roles = $controller->getAllRoles();

// calls controller for search
if ($_SERVER['REQUEST_METHOD'] === 'GET' && (isset($_GET['search']) || !empty($name) || !empty($email) || !empty($role) || !empty($status))) {
    $results = $controller->searchUsers($name, $email, $role, $status);

    if (empty($results)) {
        $message = "No users found matching your search criteria.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search User Accounts</title>
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
      <button type="button" class="back-btn" onclick="location.href='../../../Misc/Dashboards/adminDash.php';" aria-label="Go back">← Back</button>
      <div class="page-title" role="heading" aria-level="1">Search User Accounts</div>
    </header>

    <main class="card" role="main" aria-labelledby="search-user-heading">
      <h2 id="search-user-heading" class="page-heading">Search Criteria</h2>

      <form method="GET" class="form single-column">
        <div class="form-row">
          <div class="field">
            <label class="label" for="name">Name</label>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($name) ?>" class="input">
          </div>
        </div>

        <div class="form-row">
          <div class="field">
            <label class="label" for="email">Email</label>
            <input type="text" name="email" id="email" value="<?= htmlspecialchars($email) ?>" class="input">
          </div>
        </div>

        <div class="form-row">
          <div class="field">
            <label class="label" for="role">Role</label>
            <select name="role" id="role" class="select">
              <option value="">All Roles</option>
              <?php foreach ($roles as $r): ?>
                <option value="<?= htmlspecialchars($r['role']) ?>" <?= $role === $r['role'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($r['role']) ?>
                </option>
              <?php endforeach; ?>
            </select>
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
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Role</th>
                <th scope="col">Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($results as $r): ?>
                <tr>
                  <td data-label="Name"><?= htmlspecialchars($r['name']) ?></td>
                  <td data-label="Email"><?= htmlspecialchars($r['email']) ?></td>
                  <td data-label="Role"><?= htmlspecialchars($r['role']) ?></td>
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
