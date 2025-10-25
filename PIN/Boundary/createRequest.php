<?php
session_start();
$pinID = $_SESSION['userID'] ?? null;
if (!$pinID) {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../Controller/CreateRequestCon.php';

$controller = new CreateRequestCon($conn);
$message = "";
$categories = $controller->getCategories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $categoryID = (int)($_POST['categoryID'] ?? 0);

    if ($title && $description && $categoryID) {
        if ($controller->saveRequest($pinID, $title, $description, $categoryID)) {
            $message = "Request created successfully!";
        } else {
            $message = "Error creating request. Please try again.";
        }
    } else {
        $message = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create a Request</title>
    <style>
        body { font-family: Arial; background: #f6f7fb; padding: 40px; }
        .container { width: 500px; margin: auto; background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h2 { text-align: center; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input, textarea, select { width: 95%; padding: 10px; margin-top: 8px; border: 1px solid #ccc; border-radius: 5px; }
        textarea { height: 100px; resize: vertical; }
        button { width: 100%; margin-top: 20px; padding: 10px; background: #3498db; color: #fff; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #2980b9; }
        .message { text-align: center; color: green; margin-top: 10px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Create a Request</h2>
    <?php if ($message): ?><p class="message"><?= htmlspecialchars($message) ?></p><?php endif; ?>
    <form method="POST">
        <label for="title">Title</label>
        <input type="text" name="title" id="title" required>

        <label for="category">Category</label>
        <select name="categoryID" id="category" required>
            <option value="">-- Select Category --</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= htmlspecialchars($c['categoryID']) ?>"><?= htmlspecialchars($c['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="description">Description</label>
        <textarea name="description" id="description" required></textarea>

        <button type="submit">Submit Request</button>
    </form>
</div>
</body>
</html>
