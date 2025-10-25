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
    <title>Login</title>
    <style>
        body { font-family: Arial; background: #f7f8fc; display: flex;  align-items: center;  }
        form { background: white; padding: 30px; padding-right: 50px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); width: 320px; }
        h2 { text-align: center; margin-bottom: 20px; }
        input { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; padding: 10px; background: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #2980b9; }
        .message { text-align: center; color: red; }
    </style>
</head>
<body>
<form method="POST">
    <h2>Login</h2>
    <?php if ($message): ?><p class="message"><?= htmlspecialchars($message) ?></p><?php endif; ?>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
</form>
</body>
</html>
