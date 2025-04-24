<?php

$conn = new mysqli("localhost", "root", "", "eclinic_scheduler");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$logs = [];

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
        <?php foreach ($logs as $log): ?>
            <tr>
                <td><?= $log['first_name'] . ' ' . $log['last_name'] ?></td>
                <td><?= $log['nurse_first_name'] . ' ' . $log['nurse_last_name'] ?></td>
                <td><?= $log['log_details'] ?></td>
                <td><?= $log['log_date'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
