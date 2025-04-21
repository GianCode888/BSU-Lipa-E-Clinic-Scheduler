<?php
include 'doctor_crud.php';
require_once '../eclinic_database.php';

session_start();
$database = new DatabaseConnection();
$conn = $database->getConnect();

$doctor_id = $_SESSION['doctor_id'];
$doctor = new Doctor($conn);
$appointments = $doctor->student_appointment_request($doctor_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Appointment Requests</title>

    <!-- DataTables CSS and JS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#appointmentsTable').DataTable(); // Set the ID properly
        });

        function toggleForm() {
            const form = document.getElementById('yourFormIdHere'); // Not used yet, but ready
            form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
        }
    </script>
</head>
<body>

<h2>Student Appointment Requests</h2>

<table id="appointmentsTable" class="display">
    <thead>
        <tr>
            <th>Appointment ID</th>
            <th>Student ID</th>
            <th>Appointment Date</th>
            <th>Appointment Time</th>
            <th>Status</th>
            <th>Reason</th>
            <th>Created At</th>
            <th>Approval Notes</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $appointments->fetch(PDO::FETCH_ASSOC)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['appointment_id']); ?></td>
                <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                <td><?php echo htmlspecialchars($row['appointment_time']); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
                <td><?php echo htmlspecialchars($row['reason']); ?></td>
                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                <td><?php echo htmlspecialchars($row['approval_notes']); ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>

</body>
</html>
