<?php
session_start();

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'User Admin') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../../Controller/UserAccount/CreateUserAccountCon.php';

$controller = new CreateUserAccountCon($conn);
$message = "";

// fetch role list from profiles table
$profiles = $controller->getAllProfiles();

// sends form data to controller
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        $message = "All fields are required.";
    } 
    else {
        $message = $controller->createUser($name, $email, $password, $role);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create User Account</title>
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

        /* fix padding and spacing for alignment consistency */
        main.card {
            padding: 24px 32px;
        }

        /* single-column form alignment */
        .form.single-column .form-row {
            display: block;
        }
        .form.single-column .field {
            width: 100%;
            margin-bottom: 18px;
        }

        /* left-aligned action button */
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
      <div class="page-title" role="heading" aria-level="1">Create User Account</div>
    </header>

    <main class="card" role="main" aria-labelledby="create-user-heading">
      <h2 id="create-user-heading" class="page-heading">New User Details</h2>

      <form method="POST" action="" class="form single-column">
        <div class="form-row">
          <div class="field">
            <label class="label" for="name">Name</label>
            <input type="text" name="name" id="name" class="input">
          </div>
        </div>

        <div class="form-row">
          <div class="field">
            <label class="label" for="email">Email</label>
            <input type="email" name="email" id="email" class="input">
          </div>
        </div>

        <div class="form-row">
          <div class="field">
            <label class="label" for="password">Password</label>
            <input type="password" name="password" id="password" class="input">
          </div>
        </div>

        <div class="form-row">
          <div class="field">
            <label class="label" for="role">Role</label>
            <select name="role" id="role" class="select">
              <option value="">Select Role</option>
              <?php foreach ($profiles as $p): ?>
                  <option value="<?= htmlspecialchars($p['role']) ?>"><?= htmlspecialchars($p['role']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="button-row">
          <button type="submit" class="btn">Create Account</button>
        </div>
      </form>

      <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
      <?php endif; ?>
    </main>
  </div>
</body>
</html>
