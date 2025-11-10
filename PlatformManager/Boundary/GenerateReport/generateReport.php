<?php
session_start();

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'Platform Manager') {
    header("Location: ../../Misc/Login/Boundary/login.php");
    exit();
}

require_once __DIR__ . '/../../../PlatformManager/Controller/GenerateReport/GenerateReportCon.php';

$controller = new GenerateReportCon($conn);

$reportType = $_GET['reportType'] ?? '';
$requestsCreated = [];
$requestsActive = [];
$requestsMatched = [];
$requestsCompleted = [];
$requestsCancelled = [];
$message = "";

if ($reportType) {
    $data = $controller->generateReport($reportType);
    $requestsCreated = $data['created'];
    $requestsActive = $data['active'];
    $requestsMatched = $data['matched'];
    $requestsCompleted = $data['completed'];
    $requestsCancelled = $data['cancelled'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Generate Reports</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <link rel="stylesheet" href="../../../assets/css/table.css">
    <style>
        .message {
            margin-top: 16px;
            text-align: center;
            font-weight: 600;
            color: var(--danger);
        }

        main.card {
            padding: 24px 32px;
        }

        .form.inline-row {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }

        .form.inline-row select {
            width: auto;
            min-width: 200px;
        }

        .form.inline-row .btn {
            margin-top: 0;
        }

        h3.section-title {
            margin-top: 32px;
            text-align: left;
        }
    </style>
</head>
<body>
  <div class="page-wrap">
    <header class="header-inline" role="banner" aria-label="Page header">
      <button type="button" class="back-btn" onclick="location.href='../../../Misc/Dashboards/pmDash.php';" aria-label="Go back">← Back</button>
      <div class="page-title" role="heading" aria-level="1">Generate Volunteer Service Report</div>
    </header>

    <main class="card" role="main" aria-labelledby="report-form-heading">
      <h2 id="report-form-heading" class="page-heading">Report Selection</h2>

      <form method="GET" class="form inline-row">
        <select name="reportType" class="select" required>
          <option value="">-- Select Report Type --</option>
          <option value="daily" <?= $reportType === 'daily' ? 'selected' : '' ?>>Daily Report</option>
          <option value="weekly" <?= $reportType === 'weekly' ? 'selected' : '' ?>>Weekly Report</option>
          <option value="monthly" <?= $reportType === 'monthly' ? 'selected' : '' ?>>Monthly Report</option>
        </select>
        <button type="submit" class="btn">Generate Report</button>
      </form>

      <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
      <?php endif; ?>
    </main>

    <?php if ($reportType): ?>
      <main class="card" role="region" aria-label="Report results">
        <div class="card-heading">
          <div class="card-title"><?= ucfirst($reportType) ?> Report Results</div>
        </div>

        <h3 class="section-title">Requests Created</h3>
        <div class="table-wrapper" role="region">
          <table class="data-table" role="table" aria-label="Requests created table">
            <thead>
              <tr>
                <th>Title</th>
                <th>PIN Name</th>
                <th>Date Created</th>
                <th>Description</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($requestsCreated)): ?>
                <tr><td colspan="5" class="no-data">No requests created found.</td></tr>
              <?php else: ?>
                <?php foreach ($requestsCreated as $r): ?>
                  <tr>
                    <td><?= htmlspecialchars($r['title']) ?></td>
                    <td><?= htmlspecialchars($r['pinName'] ?? 'Unknown') ?></td>
                    <td><?= htmlspecialchars($r['dateCreated']) ?></td>
                    <td><?= htmlspecialchars($r['description']) ?></td>
                    <td><?= htmlspecialchars($r['status']) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <h3 class="section-title">Active Requests</h3>
        <div class="table-wrapper" role="region">
          <table class="data-table" role="table" aria-label="Active requests table">
            <thead>
              <tr>
                <th>Title</th>
                <th>PIN Name</th>
                <th>Date Created</th>
                <th>Description</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($requestsActive)): ?>
                <tr><td colspan="4" class="no-data">No active requests found.</td></tr>
              <?php else: ?>
                <?php foreach ($requestsActive as $r): ?>
                  <tr>
                    <td><?= htmlspecialchars($r['title']) ?></td>
                    <td><?= htmlspecialchars($r['pinName'] ?? 'Unknown') ?></td>
                    <td><?= htmlspecialchars($r['dateCreated']) ?></td>
                    <td><?= htmlspecialchars($r['description']) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <h3 class="section-title">Requests Matched</h3>
        <div class="table-wrapper" role="region">
          <table class="data-table" role="table" aria-label="Requests matched table">
            <thead>
              <tr>
                <th>Title</th>
                <th>PIN Name</th>
                <th>CSR Rep In-charge</th>
                <th>Date Matched</th>
                <th>Description</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($requestsMatched)): ?>
                <tr><td colspan="5" class="no-data">No matched requests found.</td></tr>
              <?php else: ?>
                <?php foreach ($requestsMatched as $r): ?>
                  <tr>
                    <td><?= htmlspecialchars($r['title']) ?></td>
                    <td><?= htmlspecialchars($r['pinName'] ?? 'Unknown') ?></td>
                    <td><?= htmlspecialchars($r['csrName']) ?></td>
                    <td><?= htmlspecialchars($r['dateMatched']) ?></td>
                    <td><?= htmlspecialchars($r['description']) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <h3 class="section-title">Requests Completed</h3>
        <div class="table-wrapper" role="region">
          <table class="data-table" role="table" aria-label="Requests completed table">
            <thead>
              <tr>
                <th>Title</th>
                <th>PIN Name</th>
                <th>CSR Rep In-charge</th>
                <th>Date Matched</th>
                <th>Date Completed</th>
                <th>Description</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($requestsCompleted)): ?>
                <tr><td colspan="6" class="no-data">No completed requests found.</td></tr>
              <?php else: ?>
                <?php foreach ($requestsCompleted as $r): ?>
                  <tr>
                    <td><?= htmlspecialchars($r['title']) ?></td>
                    <td><?= htmlspecialchars($r['pinName'] ?? 'Unknown') ?></td>
                    <td><?= htmlspecialchars($r['csrName']) ?></td>
                    <td><?= htmlspecialchars($r['dateMatched']) ?></td>
                    <td><?= htmlspecialchars($r['dateCompleted']) ?></td>
                    <td><?= htmlspecialchars($r['description']) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <h3 class="section-title">Cancelled Requests</h3>
        <div class="table-wrapper" role="region">
          <table class="data-table" role="table" aria-label="Cancelled requests table">
            <thead>
              <tr>
                <th>Title</th>
                <th>PIN Name</th>
                <th>Date Created</th>
                <th>Description</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($requestsCancelled)): ?>
                <tr><td colspan="4" class="no-data">No cancelled requests found.</td></tr>
              <?php else: ?>
                <?php foreach ($requestsCancelled as $r): ?>
                  <tr>
                    <td><?= htmlspecialchars($r['title']) ?></td>
                    <td><?= htmlspecialchars($r['pinName'] ?? 'Unknown') ?></td>
                    <td><?= htmlspecialchars($r['dateCreated']) ?></td>
                    <td><?= htmlspecialchars($r['description']) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </main>
    <?php endif; ?>
  </div>
</body>
</html>
