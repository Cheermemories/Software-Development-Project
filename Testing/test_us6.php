<?php
session_start();
ob_start();

// simulate login
$_SESSION['userID'] = 999;
$_SESSION['role'] = 'User Admin';

$boundaryFile = __DIR__ . '/../UserAdmin/Boundary/UserProfile/createUserProfile.php';

// test for page load
unset($_GET);
$_SERVER['REQUEST_METHOD'] = 'GET';
include $boundaryFile;
$output_page = ob_get_clean();

if (stripos($output_page, 'Create User Profile') !== false) {
    echo "[PASS] Create User Profile page loaded correctly.\n";
} else {
    echo "[FAIL] Unable to load Create User Profile page.\n";
    echo substr(strip_tags($output_page), 0, 300) . "...\n";
}

echo "<br>";

// test for valid form submission
ob_start();
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'role' => 'Test Role ' . rand(100, 999),
    'permissions' => 'Test, Test',
    'description' => 'A Test Profile.'
];
include $boundaryFile;
$output_post = ob_get_clean();

if (stripos($output_post, 'success') !== false ||
    stripos($output_post, 'created') !== false ||
    stripos($output_post, 'Profile') !== false) {
    echo "[PASS] Profile creation successful.\n";
} elseif (stripos($output_post, 'All fields are required') !== false) {
    echo "[FAIL] Validation failed unexpectedly.\n";
} else {
    echo "[FAIL] Unable to detect profile creation result.\n";
    echo substr(strip_tags($output_post), 0, 300) . "...\n";
}

echo "<br>===Test Complete.===\n";
?>
