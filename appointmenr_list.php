<?php
require_once 'eclinic_database.php';

$database = new DatabaseConnection();
$conn = $database->getConnect();

try {
    $query = "SELECT * FROM appointments ORDER BY appointment_date DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Appointments</title>
</head>
<body>
    <h2>Appointments</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Student ID</th>
            <th>Nurse ID</th>
            <th>Date</th>
            <th>Status</th>
            <th>Reason</th>
            <th>Created At</th>
        </tr>
        <?php if (!empty($appointments)): ?>
            <?php foreach ($appointments as $appt): ?>
                <tr>
                    <td><?= htmlspecialchars($appt['appointment_id']) ?></td>
                    <td><?= htmlspecialchars($appt['student_id']) ?></td>
                    <td><?= htmlspecialchars($appt['nurse_id']) ?></td>
                    <td><?= htmlspecialchars($appt['appointment_date']) ?></td>
                    <td><?= htmlspecialchars($appt['status']) ?></td>
                    <td><?= htmlspecialchars($appt['reason']) ?></td>
                    <td><?= htmlspecialchars($appt['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="7">No appointments found.</td></tr>
        <?php endif; ?>
    </table>
</body>
</html>
