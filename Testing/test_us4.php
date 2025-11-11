<?php
session_start();
ob_start();

// simulate login
$_SESSION['userID'] = 999;
$_SESSION['role'] = 'User Admin';

$boundaryFile = __DIR__ . '/../UserAdmin/Boundary/UserAccount/suspendUserAccount.php';

// test for user list
unset($_GET);
$_SERVER['REQUEST_METHOD'] = 'GET';
include $boundaryFile;
$output_list = ob_get_clean();

if (stripos($output_list, 'All Users') !== false) {
    echo "[PASS] Page with user list loaded correctly.\n";
} elseif (stripos($output_list, 'No users found') !== false) {
    echo "[INFO] Page handled empty user list correctly.\n";
} else {
    echo "[FAIL] Unable to load user list.\n";
    echo substr(strip_tags($output_list), 0, 300) . "...\n";
}

echo "<br>";

// test for suspend/activate for user 5
ob_start();
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'userID' => 5,
    'toggle' => '1'
];
include $boundaryFile;
$output_toggle = ob_get_clean();

if (stripos($output_toggle, 'success') !== false ||
    stripos($output_toggle, 'updated') !== false ||
    stripos($output_toggle, 'Active') !== false ||
    stripos($output_toggle, 'Inactive') !== false) {
    echo "[PASS] Toggle (suspend/activate) request processed correctly for userID 5.\n";
} elseif (stripos($output_toggle, 'No users found') !== false) {
    echo "[INFO] UserID 5 not found in list.\n";
} else {
    echo "[FAIL] Unable to process toggle request for userID 5.\n";
    echo substr(strip_tags($output_toggle), 0, 300) . "...\n";
}

echo "<br>===Test Complete.===\n";
?>
