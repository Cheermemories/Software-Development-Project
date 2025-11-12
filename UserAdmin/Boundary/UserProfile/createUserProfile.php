<?php
session_start();

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'User Admin') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../../Controller/UserProfile/CreateUserProfileCon.php';

$controller = new CreateUserProfileCon($conn);
$message = "";

// sends form data to controller
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = trim($_POST['role']);
    $permissions = trim($_POST['permissions']);
    $description = trim($_POST['description']);

    if (empty($role) || empty($permissions) || empty($description)) {
        $message = "All fields are required.";
    }
    else {
        $message = $controller->createProfile($role, $permissions, $description);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create User Profile</title>
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
    <header class="header-inline" role="banner" aria-label="Page header">
      <button type="button" class="back-btn" onclick="location.href='../../../Misc/Dashboards/adminDash.php';" aria-label="Go back">← Back</button>
      <div class="page-title" role="heading" aria-level="1">Create User Profile</div>
    </header>

    <main class="card" role="main" aria-labelledby="create-profile-heading">
      <h2 id="create-profile-heading" class="page-heading">New Profile Details</h2>

      <form method="POST" action="" class="form single-column">
        <div class="form-row">
          <div class="field">
            <label class="label" for="role">Role</label>
            <input type="text" name="role" id="role" class="input">
          </div>
        </div>

        <div class="form-row">
          <div class="field">
            <label class="label" for="permissions">Permissions</label>
            <textarea id="permissions" name="permissions" class="textarea"></textarea>
          </div>
        </div>

        <div class="form-row">
          <div class="field">
            <label class="label" for="description">Description</label>
            <textarea id="description" name="description" class="textarea" style="min-height:150px;"></textarea>
          </div>
        </div>

        <div class="button-row">
          <button type="submit" class="btn">Create Profile</button>
        </div>
      </form>

      <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
      <?php endif; ?>
    </main>
  </div>
</body>
</html>
