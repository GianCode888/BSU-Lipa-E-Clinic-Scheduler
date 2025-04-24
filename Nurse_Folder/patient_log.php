<?php
$conn = new mysqli("localhost", "root", "", "eclinic_scheduler");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$logs = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT 
            p.first_name, p.last_name,
            n.first_name AS nurse_first_name, n.last_name AS nurse_last_name,
            l.log_details, l.log_date
        FROM patient_logs l
        JOIN users p ON l.student_id = p.user_id  
        JOIN users n ON l.nurse_id = n.user_id   
        ORDER BY l.log_date DESC";


    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $logs[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Patient Logs</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
    </style>
</head>
<body>
    <h2>Patient Logs</h2>
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
                    <td><?= htmlspecialchars($log['first_name'] . ' ' . $log['last_name']) ?></td>
                    <td><?= htmlspecialchars($log['nurse_first_name'] . ' ' . $log['nurse_last_name']) ?></td>
                    <td><?= htmlspecialchars($log['log_details']) ?></td>
                    <td><?= htmlspecialchars($log['log_date']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4">No patient logs found.</td></tr>
        <?php endif; ?>
    </table>
</body>
</html>