<?php
session_start();
ob_start();

// simulate login
$_SESSION['userID'] = 999;
$_SESSION['role'] = 'User Admin';

$boundaryFile = __DIR__ . '/../UserAdmin/Boundary/UserAccount/updateUserAccount.php';

// test for user list
unset($_GET);
$_SERVER['REQUEST_METHOD'] = 'GET';
include $boundaryFile;
$output_list = ob_get_clean();

if (stripos($output_list, 'All Users') !== false) {
    echo "[PASS] Page with user list loaded correctly.\n";
} elseif (stripos($output_list, 'No users found') !== false) {
    echo "[PASS] Page handled empty user list correctly.\n";
} else {
    echo "[FAIL] Page is unable to load user list.\n";
    echo substr(strip_tags($output_list), 0, 300) . "...\n";
}

echo "<br>";

// test for edit form
ob_start();
$_GET['id'] = 5;
$_SERVER['REQUEST_METHOD'] = 'GET';
include $boundaryFile;
$output_edit = ob_get_clean();

if (stripos($output_edit, 'Edit User Account') !== false ||
    stripos($output_edit, 'Save Changes') !== false) {
    echo "[PASS] Edit form loaded correctly.\n";
} else {
    echo "[FAIL] Unable to load edit form.\n";
    echo substr(strip_tags($output_edit), 0, 300) . "...\n";
}

echo "<br>";

// test for update (no change in these data)
preg_match('/name="email" value="([^"]+)"/', $output_edit, $emailMatch);
preg_match('/name="password" value="([^"]+)"/', $output_edit, $passMatch);
preg_match('/<option value="([^"]+)" selected/', $output_edit, $roleMatch);

$email    = $emailMatch[1] ?? '';
$password = $passMatch[1] ?? '';
$role     = $roleMatch[1] ?? '';

// test for update for user 5, only changing the name
ob_start();
$_GET = [];
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'userID'   => 5,
    'name'     => 'test',
    'email'    => $email,
    'password' => $password,
    'role'     => $role
];
include $boundaryFile;
$output_post = ob_get_clean();

// check result
if (stripos($output_post, 'updated') !== false ||
    stripos($output_post, 'success') !== false ||
    stripos($output_post, 'saved') !== false) {
    echo "[PASS] Update processed successfully.\n";
} elseif (stripos($output_post, 'All fields are required') !== false) {
    echo "[FAIL] Validation failed.\n";
} else {
    echo "[INFO] Update completed silently or no change detected.\n";
}

echo "<br>===Test Complete.===\n";
?>
