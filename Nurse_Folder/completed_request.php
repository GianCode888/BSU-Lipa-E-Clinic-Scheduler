<?php
$conn = new mysqli("localhost", "root", "", "eclinic_scheduler");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$logs = [];
$sql = "SELECT * FROM completed_requests ORDER BY completed_date DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $logs[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Completed Patient Logs</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ccc; }
    </style>
</head>
<body>
    <h2>Completed Requests</h2>
    <table>
        <tr>
            <th>Student</th>
            <th>Nurse</th>
            <th>Details</th>
            <th>Date</th>
        </tr>
        <?php if (count($logs) > 0): ?>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= htmlspecialchars($log['student_name']) ?></td>
                    <td><?= htmlspecialchars($log['nurse_name']) ?></td>
                    <td><?= htmlspecialchars($log['details']) ?></td>
                    <td><?= htmlspecialchars($log['completed_date']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4">No completed requests found.</td></tr>
        <?php endif; ?>
    </table>
</body>
</html>
