<?php
session_start();

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'User Admin') {
    header("Location: ../../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../../Controller/UserAccount/UpdateUserAccountCon.php';

$controller = new UpdateUserAccountCon($conn);
$message = "";
$selectedUser = null;

// fetch all users for display
$users = $controller->getAllUsers();

// fetch all profiles for dropdown options
$profiles = $controller->getAllProfiles();

// fetch a single profile from the fetched array
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    foreach ($users as $u) {
        if ($u['userID'] == (int)$_GET['id']) {
            $selectedUser = $u;
            break;
        }
    }
}

// sends form info to contoller
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['userID'])) {
    $id = (int)$_POST['userID'];
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    $message = $controller->updateUser($id, $name, $email, $password, $role);

    // overwrite the text data to what was updated within the boundry to avoid refetching data
    foreach ($users as &$u) {
        if ($u['userID'] == $id) {
            $u['name'] = $name;
            $u['email'] = $email;
            $u['password'] = $password;
            $u['role'] = $role;
            $selectedUser = $u;
            break;
        }
    }
    unset($u);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update User Accounts</title>
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
      <?php if ($selectedUser): ?>
        <button type="button" class="back-btn" onclick="location.href='updateUserAccount.php';" aria-label="Go back">← Back</button>
        <div class="page-title" role="heading" aria-level="1">Edit User Account</div>
      <?php else: ?>
        <button type="button" class="back-btn" onclick="location.href='../../../Misc/Dashboards/adminDash.php';" aria-label="Go back">← Back</button>
        <div class="page-title" role="heading" aria-level="1">Update User Accounts</div>
      <?php endif; ?>
    </header>

    <?php if ($message): ?>
      <div class="card">
        <p class="message"><?= htmlspecialchars($message) ?></p>
      </div>
    <?php endif; ?>

    <?php if ($selectedUser): ?>
      <main class="card" role="main" aria-labelledby="edit-user-heading">
        <h2 id="edit-user-heading" class="page-heading">Edit User</h2>

        <form method="POST" class="form single-column">
          <input type="hidden" name="userID" value="<?= htmlspecialchars($selectedUser['userID']) ?>">

          <div class="form-row">
            <div class="field">
              <label class="label">Name</label>
              <input type="text" name="name" value="<?= htmlspecialchars($selectedUser['name']) ?>" class="input" required>
            </div>
          </div>

          <div class="form-row">
            <div class="field">
              <label class="label">Email</label>
              <input type="email" name="email" value="<?= htmlspecialchars($selectedUser['email']) ?>" class="input" required>
            </div>
          </div>

          <div class="form-row">
            <div class="field">
              <label class="label">Password</label>
              <input type="text" name="password" value="<?= htmlspecialchars($selectedUser['password']) ?>" class="input" required>
            </div>
          </div>

          <div class="form-row">
            <div class="field">
              <label class="label">Role</label>
              <select name="role" class="select" required>
                <?php foreach ($profiles as $p): ?>
                  <option value="<?= htmlspecialchars($p['role']) ?>" <?= $p['role'] === $selectedUser['role'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p['role']) ?>
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
      <main class="card" role="main" aria-labelledby="user-list-heading">
        <div class="card-heading">
          <div class="card-title" id="user-list-heading">All Users</div>
          <div class="muted card-subtitle">Showing <?= count($users) ?> users</div>
        </div>

        <div class="table-wrapper" role="region" aria-label="User accounts list">
          <table class="data-table" role="table" aria-label="User accounts table">
            <thead>
              <tr>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Role</th>
                <th scope="col">Status</th>
                <th scope="col" style="width:140px">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($users)): ?>
                <tr><td colspan="5" class="no-data">No users found.</td></tr>
              <?php else: ?>
                <?php foreach ($users as $u): ?>
                  <tr>
                    <td data-label="Name"><?= htmlspecialchars($u['name']) ?></td>
                    <td data-label="Email"><?= htmlspecialchars($u['email']) ?></td>
                    <td data-label="Role"><?= htmlspecialchars($u['role']) ?></td>
                    <td data-label="Status"><?= htmlspecialchars($u['status']) ?></td>
                    <td data-label="Action">
                      <a class="btn ghost table-btn" href="updateUserAccount.php?id=<?= urlencode($u['userID']) ?>">Edit</a>
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
