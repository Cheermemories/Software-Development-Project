<?php
session_start();

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'Platform Manager') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../../../PlatformManager/Controller/Category/SearchCategoryCon.php';

$controller = new SearchCategoryCon($conn);
$results = [];
$message = "";

// store search input
$name = $_GET['name'] ?? '';
$description = $_GET['description'] ?? '';
$status = $_GET['status'] ?? '';

// call for search
if ($_SERVER['REQUEST_METHOD'] === 'GET' && (isset($_GET['search']) || !empty($name) || !empty($description) || !empty($status))) {
    $results = $controller->searchCategories($name, $description, $status);

    if (empty($results)) {
        $message = "No categories found matching your search criteria.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Categories</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f6f7fb; margin: 30px; }
        h2 { text-align: center; }
        form { width: 80%; margin: 20px auto; background: #fff; padding: 20px;
               border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        label { font-weight: bold; display: block; margin-top: 10px; margin-bottom: 5px; }
        input[type=text], select { width: 40%; padding: 5px; margin-bottom: 10px; }
        textarea { width: 60%; padding: 5px; margin-bottom: 10px; font-family: Arial, sans-serif; resize: vertical; }
        button { padding: 6px 15px; background-color: #3498db; color: white;
                 border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #2980b9; }
        table { border-collapse: collapse; width: 85%; margin: 20px auto;
                background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .message { text-align: center; color: red; }
    </style>
</head>
<body>
    <h2>Search Volunteer Service Categories</h2>

    <form method="GET">
        <label>Category Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($name) ?>"><br>

        <label>Description:</label>
        <textarea name="description" rows="3"><?= htmlspecialchars($description) ?></textarea><br>

        <label>Status:</label>
        <select name="status">
            <option value="">All</option>
            <option value="Active" <?= $status === 'Active' ? 'selected' : '' ?>>Active</option>
            <option value="Inactive" <?= $status === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
        </select><br>

        <button type="submit" name="search">Search</button>
    </form>

    <?php if ($message): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <?php if (!empty($results)): ?>
        <table>
            <tr>
                <th>Category Name</th>
                <th>Description</th>
                <th>Status</th>
            </tr>
            <?php foreach ($results as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['name']) ?></td>
                    <td><?= htmlspecialchars($r['description']) ?></td>
                    <td><?= htmlspecialchars($r['status']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>
