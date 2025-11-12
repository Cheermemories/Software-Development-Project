<?php
session_start();
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'PIN') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../Controller/CreateRequestCon.php';

$pinID = $_SESSION['userID'];
$controller = new CreateRequestCon($conn);

$message = "";
// to make dropdown for category
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
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <link rel="stylesheet" href="../../assets/css/table.css">
    <style>
        .message {
            margin-top: 16px;
            text-align: center;
            font-weight: 600;
            color: var(--muted);
        }

        main.card {
            padding: 24px 32px;
        }

        .form.single-column .form-row {
            display: block;
        }
        .form.single-column .field {
            width: 100%;
            margin-bottom: 18px;
        }

        .button-row {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            gap: 8px;
            margin-top: 8px;
        }
    </style>
</head>
<body>
  <div class="page-wrap">
    <header class="header-inline" role="banner" aria-label="Page header">
      <button type="button" class="back-btn" onclick="location.href='../../Misc/Dashboards/pinDash.php';" aria-label="Go back">← Back</button>
      <div class="page-title" role="heading" aria-level="1">Create Request</div>
    </header>

    <main class="card" role="main" aria-labelledby="create-request-heading">
      <h2 id="create-request-heading" class="page-heading">New Request Details</h2>

      <form method="POST" action="" class="form single-column">
        <div class="form-row">
          <div class="field">
            <label class="label" for="title">Title</label>
            <input type="text" name="title" id="title" class="input" required>
          </div>
        </div>

        <div class="form-row">
          <div class="field">
            <label class="label" for="category">Category</label>
            <select name="categoryID" id="category" class="select" required>
              <option value="">-- Select Category --</option>
              <?php foreach ($categories as $c): ?>
                <option value="<?= htmlspecialchars($c['categoryID']) ?>"><?= htmlspecialchars($c['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="field">
            <label class="label" for="description">Description</label>
            <textarea name="description" id="description" class="textarea" required></textarea>
          </div>
        </div>

        <div class="button-row">
          <button type="submit" class="btn">Submit Request</button>
        </div>
      </form>

      <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
      <?php endif; ?>
    </main>
  </div>
</body>
</html>
