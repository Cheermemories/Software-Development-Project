<?php
require_once __DIR__ . '/../Misc/Login/Controller/LoginCon.php';

$controller = new LoginCon();

$email = "pin@example.com";
$password = "password";

// injecting into controller directly
$result = $controller->authenticateUser($email, $password);

// check results
if (is_array($result) && $result['role'] === 'PIN') {
    echo "[PASS] PIN login successful.\n<br>";
    echo "Email used: {$result['email']} | Password used: {$result['password']}\n";
} else {
    echo "[FAIL] PIN login failed or returned unexpected result.\n";
    var_dump($result);
}

echo "<br>===Test Complete==="
?>
