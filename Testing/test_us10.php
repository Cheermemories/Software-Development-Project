<?php
session_start();
ob_start();

// simulate login
$_SESSION['userID'] = 999;
$_SESSION['role'] = 'User Admin';

$boundaryFile = __DIR__ . '/../UserAdmin/Boundary/UserProfile/searchUserProfile.php';

// test for page load
unset($_GET);
$_SERVER['REQUEST_METHOD'] = 'GET';
include $boundaryFile;
$output_page = ob_get_clean();

if (stripos($output_page, 'Search Criteria') !== false) {
    echo "[PASS] Search User Profile page loaded correctly.\n";
} else {
    echo "[FAIL] Unable to load Search User Profile page.\n";
    echo substr(strip_tags($output_page), 0, 300) . "...\n";
}

echo "<br>";

// test for valid search criteria using known profile data
ob_start();
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET = [
    'role' => 'User Admin',
    'permissions' => 'Full',
    'description' => 'Responsible',
    'status' => 'Active'
];
include $boundaryFile;
$output_search = ob_get_clean();

if (stripos($output_search, 'Search Results') !== false &&
    stripos($output_search, 'User Admin') !== false &&
    stripos($output_search, 'Full') !== false &&
    stripos($output_search, 'Responsible') !== false) {
    echo "[PASS] Search returned expected profile results.\n";
} elseif (stripos($output_search, 'No profiles found') !== false) {
    echo "[FAIL] Search did not find expected profiles.\n";
} else {
    echo "[FAIL] Unable to confirm search behavior.\n";
    echo substr(strip_tags($output_search), 0, 300) . "...\n";
}

echo "<br>===Test Complete.===\n";
?>
