<?php
session_start();

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'Platform Manager') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../../../PlatformManager/Controller/Category/CreateCategoryCon.php';

$controller = new CreateCategoryCon($conn);
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($name) || empty($description)) {
        $message = "All fields are required.";
    } else {
        $message = $controller->createCategory($name, $description);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Category</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f9fc; padding: 30px; }
        .container { width: 60%; margin: 0 auto; background: white; padding: 25px;
                     border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        h2 { text-align: center; margin-bottom: 20px; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input[type=text], textarea {
            width: 90%; padding: 10px; border: 1px solid #ccc;
            border-radius: 4px; margin-top: 5px;
        }
        textarea { resize: vertical; height: 100px; font-family: Arial, sans-serif; }
        button {
            margin-top: 20px; padding: 10px 20px; background-color: #3498db;
            color: white; border: none; border-radius: 4px; cursor: pointer;
        }
        button:hover { background-color: #2980b9; }
        .message { text-align: center; margin-top: 15px; font-weight: bold; color: #333; }
    </style>
</head>
<body>
<div class="container">
    <h2>Create Volunteer Service Category</h2>

    <form method="POST">
        <label for="name">Category Name:</label>
        <input type="text" id="name" name="name">

        <label for="description">Description:</label>
        <textarea id="description" name="description"></textarea>

        <button type="submit">Create Category</button>
    </form>

    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
</div>
</body>
</html>
