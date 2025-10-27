<?php
session_start();

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'Platform Manager') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../../../PlatformManager/Controller/Category/UpdateCategoryCon.php';

$controller = new UpdateCategoryCon($conn);
$message = "";
$selectedCategory = null;

// get all categories for display
$categories = $controller->getAllCategories();

// use fetched data to find the category we want to update
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    foreach ($categories as $c) {
        if ($c['categoryID'] == (int)$_GET['id']) {
            $selectedCategory = $c;
            break;
        }
    }
}

// calls to update the form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['categoryID'])) {
    $id = (int)$_POST['categoryID'];
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($name) || empty($description)) {
        $message = "All fields are required.";
    } else {
        $message = $controller->updateCategory($id, $name, $description);

        // updates the values to it displays changes without refetching
        foreach ($categories as &$c) {
            if ($c['categoryID'] == $id) {
                $c['name'] = $name;
                $c['description'] = $description;
                $selectedCategory = $c;
                break;
            }
        }
        unset($c);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Categories</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f6f7fb; margin: 30px; }
        h2 { text-align: center; }
        table { border-collapse: collapse; width: 80%; margin: 20px auto;
                background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        a.edit-link { text-decoration: none; color: blue; }
        a.edit-link:hover { text-decoration: underline; }
        .container { width: 60%; margin: 30px auto; background: #fff;
                     padding: 20px; border-radius: 8px;
                     box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        input, textarea { width: 95%; padding: 6px; margin-top: 5px;
                          margin-bottom: 15px; }
        textarea { resize: vertical; height: 100px; font-family: Arial, sans-serif; }
        button { background-color: #3498db; color: white;
                 padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #2980b9; }
        .back-link { display: block; text-align: center; margin-top: 15px;
                     text-decoration: none; color: blue; }
        .back-link:hover { text-decoration: underline; }
        .message { text-align: center; color: green; }
    </style>
</head>
<body>
<h2>Update Volunteer Service Categories</h2>

<?php if ($message): ?>
    <p class="message"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<?php if ($selectedCategory): ?>
    <div class="container">
        <form method="POST">
            <input type="hidden" name="categoryID" value="<?= htmlspecialchars($selectedCategory['categoryID']) ?>">

            <label>Category Name:</label><br>
            <input type="text" name="name" value="<?= htmlspecialchars($selectedCategory['name']) ?>" required><br>

            <label>Description:</label><br>
            <textarea name="description" required><?= htmlspecialchars($selectedCategory['description']) ?></textarea><br>

            <button type="submit">Save Changes</button>
        </form>
        <a href="updateCategory.php" class="back-link">Back</a>
    </div>
<?php else: ?>
    <table>
        <tr>
            <th>Category Name</th>
            <th>Description</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php if (empty($categories)): ?>
            <tr><td colspan="4">No categories found.</td></tr>
        <?php else: ?>
            <?php foreach ($categories as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c['name']) ?></td>
                    <td><?= htmlspecialchars($c['description']) ?></td>
                    <td><?= htmlspecialchars($c['status']) ?></td>
                    <td><a class="edit-link" href="updateCategory.php?id=<?= urlencode($c['categoryID']) ?>">Edit</a></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
<?php endif; ?>
</body>
</html>
