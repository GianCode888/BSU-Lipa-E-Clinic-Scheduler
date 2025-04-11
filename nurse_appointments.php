<?php
include 'db.php';

$sql = "SELECT a.*, u.first_name, u.last_name 
        FROM appointments a 
        LEFT JOIN users u ON a.student_id = u.user_id 
        WHERE a.status != 'completed'
        ORDER BY a.appointment_date, a.appointment_time";
$result = $conn->query($sql);
?>

<h2>Appointment Queue</h2>
<table border="1">
    <tr>
        <th>Student Name</th>
        <th>Date</th>
        <th>Time</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['first_name'] . ' ' . $row['last_name'] ?></td>
        <td><?= $row['appointment_date'] ?></td>
        <td><?= $row['appointment_time'] ?></td>
        <td><?= $row['status'] ?></td>
        <td>
            <a href="mark_complete.php?id=<?= $row['appointment_id'] ?>">Mark Complete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
