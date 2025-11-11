<?php
session_start();
ob_start();

// simulate login
$_SESSION['userID'] = 999;
$_SESSION['role'] = 'User Admin';

// test form submission data
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'name'     => 'Test User ' . rand(1000, 9999),
    'email'    => 'testuser' . rand(1000, 9999) . '@example.com',
    'password' => 'testpass',
    'role'     => 'CSR Rep',
];

$boundaryFile = __DIR__ . '/../UserAdmin/Boundary/UserAccount/createUserAccount.php';
include $boundaryFile;

$output = ob_get_clean();

// test for result message
if (stripos($output, 'success') !== false ||
    stripos($output, 'Account created') !== false ||
    stripos($output, 'User added') !== false) {
    echo "[PASS] Account creation successful.\n";
} elseif (stripos($output, 'All fields are required') !== false) {
    echo "[FAIL] Validation failed: missing required field.\n";
} elseif (stripos($output, 'Error') !== false) {
    echo "[FAIL] Controller or Entity reported an error.\n";
} else {
    echo "[FAIL] Unable to detect result message.\n";
}

echo "<br>===Test Complete.===\n";
?>
