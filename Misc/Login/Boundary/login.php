<?php
require_once __DIR__ . '/../Controller/LoginCon.php';
session_start();

$controller = new LoginCon();
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $user = $controller->authenticateUser($email, $password);

    if (is_array($user)) {
        $_SESSION['userID'] = $user['userID'];
        $_SESSION['userName'] = $user['name'];
        $_SESSION['userEmail'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        switch ($user['role']) {
            case 'PIN':
                header("Location: ../../Dashboards/pinDash.php");
                break;
            case 'User Admin':
                header("Location: ../../Dashboards/adminDash.php");
                break;
            case 'CSR Rep':
                header("Location: ../../Dashboards/csrrepDash.php");
                break;
            case 'Platform Manager':
                header("Location: ../../Dashboards/pmDash.php");
                break;
        }
        exit();
    } else {
        $message = "Invalid login credentials or inactive account.";
    }
}

if (isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'PIN': header("Location: ../../Dashboards/pinDash.php"); break;
        case 'User Admin': header("Location: ../../Dashboards/adminDash.php"); break;
        case 'CSR Rep': header("Location: ../../Dashboards/csrDash.php"); break;
        case 'Platform Manager': header("Location: ../../Dashboards/managerDash.php"); break;
    }
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login</title>
  <link rel="stylesheet" href="../../../assets/css/auth.css">
</head>
<body>
  <form method="POST" class="auth-card" novalidate>
    <h2>Login</h2>
    <?php if ($message): ?><p class="message"><?= htmlspecialchars($message) ?></p><?php endif; ?>
    <div class="form-row">
      <input class="input" type="email" name="email" placeholder="Email" required autocomplete="email" />
    </div>
    <div class="form-row">
      <input class="input" type="password" name="password" placeholder="Password" required autocomplete="current-password" />
    </div>
    <div class="form-row" style="margin-top:6px">
      <button class="btn" type="submit">Login</button>
    </div>
  </form>
</body>
</html>