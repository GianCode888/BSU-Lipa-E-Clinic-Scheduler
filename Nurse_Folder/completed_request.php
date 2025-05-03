<?php
include '../eclinic_database.php';
include 'nurse_dashboard_crud.php';

$database = new DatabaseConnection();
$db = $database->getConnect();
$nurseManager = new NurseManager($db);

// Handle delete request
if (isset($_GET['id'])) {
    $log_id = $_GET['id'];

    if ($nurseManager->deleteCompletedRequest($log_id)) {
        echo "<script>alert('Request deleted successfully!'); window.location.href='completed_request.php';</script>";
    } else {
        echo "<script>alert('Failed to delete request.');</script>";
    }
}

// Fetch completed logs
$completedLogs = $nurseManager->getCompletedRequests();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Completed Patient Logs</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        th, td {
            border: 1px solid #aaa;
            padding: 10px;
        }
        th {
            background-color: #eee;
        }
        .btn-delete {
            color: red;
            cursor: pointer;
            text-decoration: underline;
        }
    </style>
</head>
<body>

<h2>Completed Patient Logs</h2>

<table>
    <thead>
        <tr>
            <th>Student Name</th>
            <th>Contact</th>
            <th>Address</th>
            <th>Nurse Name</th>
            <th>Details</th>
            <th>Completed Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($completedLogs)): ?>
            <?php foreach ($completedLogs as $log): ?>
                <tr>
                    <td><?= htmlspecialchars($log['student_name']) ?></td>
                    <td><?= htmlspecialchars($log['contact']) ?></td>
                    <td><?= htmlspecialchars($log['address']) ?></td>
                    <td><?= htmlspecialchars($log['nurse_name']) ?></td>
                    <td><?= htmlspecialchars($log['details']) ?></td>
                    <td><?= htmlspecialchars($log['completed_date']) ?></td>
                    <td><a href="delete_completed_request.php?id=<?= $log['id'] ?>" class="btn-delete">Delete</a></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">No completed requests found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<form method="POST">
<a href="../nurse_dashboard.php" class="btn">Back to Dashboard</a>
</form>

</body>
</html>
