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
    <style>
        body { font-family: Arial, sans-serif; background: #f6f7fb; margin: 30px; }
        h2, h3 { text-align: center; }
        form { text-align: center; margin-bottom: 30px; }
        select { padding: 6px 10px; margin-right: 10px; }
        button { padding: 6px 15px; background-color: #3498db; color: white;
                 border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #2980b9; }
        table { border-collapse: collapse; width: 90%; margin: 20px auto;
                background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .message { text-align: center; color: red; }
    </style>
</head>
<body>
    <h2>Generate Volunteer Service Report</h2>

    <form method="GET">
        <select name="reportType" required>
            <option value="">-- Select Report Type --</option>
            <option value="daily" <?= $reportType === 'daily' ? 'selected' : '' ?>>Daily Report</option>
            <option value="weekly" <?= $reportType === 'weekly' ? 'selected' : '' ?>>Weekly Report</option>
            <option value="monthly" <?= $reportType === 'monthly' ? 'selected' : '' ?>>Monthly Report</option>
        </select>
        <button type="submit">Generate Report</button>
    </form>

    <?php if ($reportType): ?>
        <h3><?= ucfirst($reportType) ?> Report Results</h3>

        <h3>Requests Created</h3>
        <table>
            <tr>
                <th>Title</th>
                <th>PIN Name</th>
                <th>Date Created</th>
                <th>Description</th>
                <th>Status</th>
            </tr>
            <?php if (empty($requestsCreated)): ?>
                <tr><td colspan="5">No requests created found.</td></tr>
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
        </table>

        <h3>Active Requests</h3>
        <table>
            <tr>
                <th>Title</th>
                <th>PIN Name</th>
                <th>Date Created</th>
                <th>Description</th>
            </tr>
            <?php if (empty($requestsActive)): ?>
                <tr><td colspan="4">No active requests found.</td></tr>
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
        </table>

        <h3>Requests Matched</h3>
        <table>
            <tr>
                <th>Title</th>
                <th>PIN Name</th>
                <th>CSR Rep In-charge</th>
                <th>Date Matched</th>
                <th>Description</th>
            </tr>
            <?php if (empty($requestsMatched)): ?>
                <tr><td colspan="5">No matched requests found.</td></tr>
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
        </table>

        <h3>Requests Completed</h3>
        <table>
            <tr>
                <th>Title</th>
                <th>PIN Name</th>
                <th>CSR Rep In-charge</th>
                <th>Date Matched</th>
                <th>Date Completed</th>
                <th>Description</th>
            </tr>
            <?php if (empty($requestsCompleted)): ?>
                <tr><td colspan="6">No completed requests found.</td></tr>
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
        </table>

        <h3>Cancelled Requests</h3>
        <table>
            <tr>
                <th>Title</th>
                <th>PIN Name</th>
                <th>Date Created</th>
                <th>Description</th>
            </tr>
            <?php if (empty($requestsCancelled)): ?>
                <tr><td colspan="4">No cancelled requests found.</td></tr>
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
        </table>
    <?php endif; ?>
</body>
</html>