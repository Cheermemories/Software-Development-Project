<?php
session_start();
ob_start();

// simulate login
$_SESSION['userID'] = 999;
$_SESSION['role'] = 'User Admin';

unset($_GET); 
$_SERVER['REQUEST_METHOD'] = 'GET';

$boundaryFile = __DIR__ . '/../UserAdmin/Boundary/UserAccount/viewUserAccount.php';
include $boundaryFile;

$output_all = ob_get_clean();

// tests for user list output
if (stripos($output_all, 'All Users') !== false) {
    echo "[PASS] Page with user list loaded correctly.\n";
} elseif (stripos($output_all, 'No users found') !== false) {
    echo "[PASS] Page handled empty user list correctly.\n";
} else {
    echo "[FAIL] Page is unable to load user list.\n";
    echo substr(strip_tags($output_all), 0, 300) . "...\n";
}

echo "<br>";

// tests for user detail
ob_start();
$_GET['id'] = 1;
include $boundaryFile;
$output_single = ob_get_clean();

if (stripos($output_single, 'User Account Details') !== false ||
    stripos($output_single, 'Account Information') !== false) {
    echo "[PASS] User Account details loaded correctly.\n";
} elseif (stripos($output_single, 'No users found') !== false) {
    echo "[INFO] No users were returned from getAllUsers().\n";
} else {
    echo "[FAIL] Unable to load User Account details.\n";
    echo substr(strip_tags($output_single), 0, 300) . "...\n";
}

echo "<br>===Test Complete.===\n";
?>
