<?php
include '../eclinic_database.php';
include 'nurse_dashboard_crud.php';

$database = new DatabaseConnection();
$db = $database->getConnect();
$nurseManager = new NurseManager($db);

// Handle deletion kapag may na-click na delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if ($nurseManager->deleteCompletedRequest($id)) {
        header("Location: completed_request.php");
        exit();
    } else {
        echo "Failed to delete log.";
    }
}

// Fetch all completed logs
$completedLogs = $nurseManager->getCompletedRequests();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Completed Requests</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #999;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #eee;
        }
        button {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>

<h2>Completed Requests</h2>

<table>
    <thead>
        <tr>
            <th>Student Name</th>
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
                <td><?= htmlspecialchars($log['nurse_name']) ?></td>
                <td><?= htmlspecialchars($log['details']) ?></td>
                <td><?= htmlspecialchars($log['completed_date']) ?></td>
                <td>
                    <form method="GET" style="display:inline;">
                        <input type="hidden" name="delete" value="<?= $log['id'] ?>">
                        <button type="submit" onclick="return confirm('Are you sure you want to delete this log?');">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="5">No completed requests found.</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
<a href="../nurse_dashboard.php" class="btn">Back to Dashboard</a>
</body>
</html>
