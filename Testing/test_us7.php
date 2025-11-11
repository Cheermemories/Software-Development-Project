<?php
session_start();
ob_start();

// simulate login
$_SESSION['userID'] = 999;
$_SESSION['role'] = 'User Admin';

$boundaryFile = __DIR__ . '/../UserAdmin/Boundary/UserProfile/viewUserProfile.php';

// test for profile list page
unset($_GET);
$_SERVER['REQUEST_METHOD'] = 'GET';
include $boundaryFile;
$output_list = ob_get_clean();

if (stripos($output_list, 'All Profiles') !== false) {
    echo "[PASS] Page with profile list loaded correctly.\n";
} elseif (stripos($output_list, 'No profiles found') !== false) {
    echo "[PASS] Page handled empty profile list correctly.\n";
} else {
    echo "[FAIL] Page is unable to load profile list.\n";
    echo substr(strip_tags($output_list), 0, 300) . "...\n";
}

echo "<br>";

// test for single profile details
ob_start();
$_GET['id'] = 1;
$_SERVER['REQUEST_METHOD'] = 'GET';
include $boundaryFile;
$output_single = ob_get_clean();

if (stripos($output_single, 'Profile Details') !== false ||
    stripos($output_single, 'Profile Information') !== false) {
    echo "[PASS] Profile details loaded correctly.\n";
} elseif (stripos($output_single, 'No profiles found') !== false) {
    echo "[INFO] No profiles were returned from getAllProfiles().\n";
} else {
    echo "[FAIL] Unable to load profile details.\n";
    echo substr(strip_tags($output_single), 0, 300) . "...\n";
}

echo "<br>===Test Complete.===\n";
?>
