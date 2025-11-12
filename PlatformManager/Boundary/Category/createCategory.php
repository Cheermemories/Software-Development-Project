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
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <link rel="stylesheet" href="../../../assets/css/table.css">
    <style>
        main.card {
            padding: 24px 32px;
            max-width: 700px;
            margin: 0 auto;
        }

        .form.single-column .form-row {
            display: block;
            margin-bottom: 18px;
        }

        .form.single-column .field {
            width: 100%;
        }

        .input, .textarea {
            width: 100%;
            padding: 10px 12px;
            border-radius: 6px;
            border: 1.5px solid #bbb;
            background-color: #fff;
            font-family: Arial, sans-serif;
            font-size: 15px;
            color: #222;
            box-sizing: border-box;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .input:focus, .textarea:focus {
            border-color: #007BFF;
            box-shadow: 0 0 4px rgba(0,123,255,0.4);
            outline: none;
        }

        .textarea {
            min-height: 120px;
            resize: vertical;
        }

        .button-row {
            margin-top: 15px;
        }

        .message {
            margin-top: 18px;
            text-align: center;
            font-weight: 600;
            color: #333;
        }
    </style>
</head>
<body>
  <div class="page-wrap">
    <header class="header-inline" role="banner" aria-label="Page header">
      <button type="button" class="back-btn" onclick="location.href='../../../Misc/Dashboards/pmDash.php';" aria-label="Go back">← Back</button>
      <div class="page-title" role="heading" aria-level="1">Create Volunteer Service Category</div>
    </header>

    <main class="card" role="main" aria-labelledby="create-category-heading">
      <div class="card-heading">
        <div class="card-title" id="create-category-heading">New Category</div>
      </div>

      <form method="POST" class="form single-column">
        <div class="form-row">
          <div class="field">
            <label class="label" for="name">Category Name</label>
            <input type="text" id="name" name="name" class="input">
          </div>
        </div>

        <div class="form-row">
          <div class="field">
            <label class="label" for="description">Description</label>
            <textarea id="description" name="description" class="textarea"></textarea>
          </div>
        </div>

        <div class="action-row">
          <button type="submit" class="btn">Create Category</button>
        </div>
      </form>

      <?php if ($message): ?>
        <div class="message" role="status">
          <?= htmlspecialchars($message) ?>
        </div>
      <?php endif; ?>
    </main>
  </div>
</body>
</html>
