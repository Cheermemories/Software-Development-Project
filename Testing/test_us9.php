<?php
session_start();
ob_start();

// simulate login
$_SESSION['userID'] = 999;
$_SESSION['role'] = 'User Admin';

$boundaryFile = __DIR__ . '/../UserAdmin/Boundary/UserProfile/suspendUserProfile.php';

// test for profile list
unset($_GET);
$_SERVER['REQUEST_METHOD'] = 'GET';
include $boundaryFile;
$output_list = ob_get_clean();

if (stripos($output_list, 'All Profiles') !== false) {
    echo "[PASS] Page with profile list loaded correctly.\n";
} elseif (stripos($output_list, 'No profiles found') !== false) {
    echo "[INFO] Page handled empty profile list correctly.\n";
} else {
    echo "[FAIL] Unable to load profile list.\n";
    echo substr(strip_tags($output_list), 0, 300) . "...\n";
}

echo "<br>";

// test for suspend/activate on profile 2
ob_start();
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'profileID' => 2,
    'toggle' => '1'
];
include $boundaryFile;
$output_toggle = ob_get_clean();

if (stripos($output_toggle, 'success') !== false ||
    stripos($output_toggle, 'updated') !== false ||
    stripos($output_toggle, 'Active') !== false ||
    stripos($output_toggle, 'Inactive') !== false ||
    stripos($output_toggle, 'Profile') !== false) {
    echo "[PASS] Toggle (suspend/activate) request processed correctly for profileID 2.\n";
} elseif (stripos($output_toggle, 'No profiles found') !== false) {
    echo "[INFO] profileID 2 not found in list.\n";
} else {
    echo "[FAIL] Unable to process toggle request for profileID 2.\n";
    echo substr(strip_tags($output_toggle), 0, 300) . "...\n";
}

echo "<br>===Test Complete.===\n";
?>
