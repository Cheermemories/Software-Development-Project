<?php
session_start();

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'User Admin') {
    header("Location: ../../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../../Controller/UserProfile/UpdateUserProfileCon.php';

$controller = new UpdateUserProfileCon($conn);
$message = "";
$selectedProfile = null;

// fetch all profiles for display
$profiles = $controller->getAllProfiles();

// fetch a single profile from the fetched array
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    foreach ($profiles as $p) {
        if ($p['profileID'] == (int)$_GET['id']) {
            $selectedProfile = $p;
            break;
        }
    }
}

// sends form info to contoller
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['profileID'])) {
    $id = (int)$_POST['profileID'];
    $role = $_POST['role'] ?? '';
    $permissions = $_POST['permissions'] ?? '';
    $description = $_POST['description'] ?? '';
    $message = $controller->updateProfile($id, $role, $permissions, $description);

    // overwrite the text data to what was updated within the boundry to avoid refetching data
    foreach ($profiles as &$p) {
        if ($p['profileID'] == $id) {
            $p['role'] = $role;
            $p['permissions'] = $permissions;
            $p['description'] = $description;
            $selectedProfile = $p;
            break;
        }
    }
    unset($p);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update User Profiles</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <link rel="stylesheet" href="../../../assets/css/table.css">
    <style>
        .message {
            margin-top: 16px;
            text-align: center;
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

        .textarea {
            min-height: 100px;
            resize: vertical;
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
    <?php if ($selectedProfile): ?>
      <header class="header-inline" role="banner" aria-label="Page header">
        <button type="button" class="back-btn" onclick="location.href='updateUserProfile.php';" aria-label="Go back">← Back</button>
        <div class="page-title" role="heading" aria-level="1">Update User Profile</div>
      </header>

      <main class="card" role="main" aria-labelledby="update-profile-heading">
        <h2 id="update-profile-heading" class="page-heading">Edit Profile</h2>

        <form method="POST" class="form single-column">
          <input type="hidden" name="profileID" value="<?= htmlspecialchars($selectedProfile['profileID']) ?>">

          <div class="form-row">
            <div class="field">
              <label class="label" for="role">Role</label>
              <input type="text" name="role" id="role" value="<?= htmlspecialchars($selectedProfile['role']) ?>" class="input" required>
            </div>
          </div>

          <div class="form-row">
            <div class="field">
              <label class="label" for="permissions">Permissions</label>
              <textarea name="permissions" id="permissions" class="textarea"><?= htmlspecialchars($selectedProfile['permissions']) ?></textarea>
            </div>
          </div>

          <div class="form-row">
            <div class="field">
              <label class="label" for="description">Description</label>
              <textarea name="description" id="description" class="textarea" style="min-height:150px;"><?= htmlspecialchars($selectedProfile['description']) ?></textarea>
            </div>
          </div>

          <div class="button-row">
            <button type="submit" class="btn">Save Changes</button>
          </div>
        </form>

        <?php if ($message): ?>
          <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
      </main>

    <?php else: ?>
      <header class="header-inline" role="banner" aria-label="Page header">
        <button type="button" class="back-btn" onclick="location.href='../../../Misc/Dashboards/adminDash.php';" aria-label="Go back">← Back</button>
        <div class="page-title" role="heading" aria-level="1">User Profiles</div>
        <div class="right-spacer"></div>
      </header>

      <main class="card" role="main" aria-labelledby="profile-list-heading">
        <div class="card-heading">
          <div class="card-title" id="profile-list-heading">All Profiles</div>
          <div class="muted card-subtitle">Showing <?= count($profiles) ?> profiles</div>
        </div>

        <div class="table-wrapper" role="region" aria-label="Profiles list">
          <table class="data-table" role="table" aria-label="Profiles table">
            <thead>
              <tr>
                <th scope="col">Role</th>
                <th scope="col">Status</th>
                <th scope="col" style="width:140px">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($profiles)): ?>
                <tr><td colspan="3" class="no-data">No profiles found.</td></tr>
              <?php else: ?>
                <?php foreach ($profiles as $p): ?>
                  <tr>
                    <td data-label="Role"><?= htmlspecialchars($p['role']) ?></td>
                    <td data-label="Status"><?= htmlspecialchars($p['status']) ?></td>
                    <td data-label="Action">
                      <a class="btn ghost table-btn" href="updateUserProfile.php?id=<?= urlencode($p['profileID']) ?>">Edit</a>
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
