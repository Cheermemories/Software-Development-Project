<?php
session_start();
ob_start();

// simulate login
$_SESSION['userID'] = 999;
$_SESSION['role'] = 'User Admin';

$boundaryFile = __DIR__ . '/../UserAdmin/Boundary/UserAccount/searchUserAccount.php';

// test for normal page load (no search yet)
unset($_GET);
$_SERVER['REQUEST_METHOD'] = 'GET';
include $boundaryFile;
$output_page = ob_get_clean();

if (stripos($output_page, 'Search Criteria') !== false) {
    echo "[PASS] Search page loaded correctly.\n";
} else {
    echo "[FAIL] Unable to load search page.\n";
    echo substr(strip_tags($output_page), 0, 300) . "...\n";
}

echo "<br>";

// test for search using data from userID 1 
ob_start();
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET = [
    'name' => 'UserAdmin',
    'email' => 'ua@example.com',
    'role' => 'User Admin',
    'status' => 'Active'
];
include $boundaryFile;
$output_search = ob_get_clean();

if (stripos($output_search, 'Search Results') !== false &&
    stripos($output_search, 'UserAdmin') !== false &&
    stripos($output_search, 'ua@example.com') !== false) {
    echo "[PASS] Search returned correct user results.\n";
} elseif (stripos($output_search, 'No users found') !== false) {
    echo "[FAIL] Search did not find expected user.\n";
} else {
    echo "[FAIL] Unable to confirm search behavior.\n";
    echo substr(strip_tags($output_search), 0, 300) . "...\n";
}

echo "<br>===Test Complete.===\n";
?>
