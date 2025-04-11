<?php
include 'db.php';

$sql = "SELECT r.*, u.first_name, u.last_name 
        FROM medication_requests r 
        LEFT JOIN users u ON r.student_id = u.user_id 
        WHERE r.status = 'approved'";
$result = $conn->query($sql);
?>

<h2>Approved Medication Requests</h2>
<table border="1">
    <tr>
        <th>Student</th>
        <th>Medication</th>
        <th>Date</th>
        <th>Action</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['first_name'] . ' ' . $row['last_name'] ?></td>
        <td><?= $row['medication'] ?></td>
        <td><?= $row['request_date'] ?></td>
        <td>
            <a href="medication_dispense.php?id=<?= $row['request_id'] ?>">Dispense</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
