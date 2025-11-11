<?php
session_start();
ob_start();

// simulate login
$_SESSION['userID'] = 999;
$_SESSION['role'] = 'User Admin';

$boundaryFile = __DIR__ . '/../UserAdmin/Boundary/UserProfile/updateUserProfile.php';

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

// test for edit form
ob_start();
$_GET['id'] = 1;
$_SERVER['REQUEST_METHOD'] = 'GET';
include $boundaryFile;
$output_edit = ob_get_clean();

if (stripos($output_edit, 'Update User Profile') !== false ||
    stripos($output_edit, 'Edit Profile') !== false) {
    echo "[PASS] Edit form loaded correctly.\n";
} else {
    echo "[FAIL] Unable to load edit form.\n";
    echo substr(strip_tags($output_edit), 0, 300) . "...\n";
}

echo "<br>";

// test for update (no change in these data)
preg_match('/name="role"[^>]*value="([^"]+)"/', $output_edit, $roleMatch);
preg_match('/<textarea[^>]*id="permissions"[^>]*>(.*?)<\/textarea>/s', $output_edit, $permMatch);
preg_match('/<textarea[^>]*id="description"[^>]*>(.*?)<\/textarea>/s', $output_edit, $descMatch);
preg_match('/name="profileID"[^>]*value="([^"]+)"/', $output_edit, $idMatch);

$role        = $roleMatch[1] ?? '';
$permissions = $permMatch[1] ?? '';
$description = $descMatch[1] ?? '';
$profileID   = $idMatch[1] ?? 1;

// test for update for profile 1, only adding the word test into the back of the description.
ob_start();
$_GET = [];
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'profileID'   => $profileID,
    'role'        => $role,
    'permissions' => $permissions,
    'description' => $description . ' test'
];
include $boundaryFile;
$output_post = ob_get_clean();

if (stripos($output_post, 'updated') !== false ||
    stripos($output_post, 'success') !== false ||
    stripos($output_post, 'saved') !== false) {
    echo "[PASS] Update submission processed correctly.\n";
} elseif (stripos($output_post, 'All fields are required') !== false) {
    echo "[FAIL] Validation failed unexpectedly.\n";
} else {
    echo "[FAIL] Unable to detect update result.\n";
    echo substr(strip_tags($output_post), 0, 300) . "...\n";
}

echo "<br>===Test Complete.===\n";
?>
