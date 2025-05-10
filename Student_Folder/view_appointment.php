<?php
require_once '../eclinic_database.php';
require_once 'student_serverside.php';

$database = new DatabaseConnection();
$conn = $database->getConnect();
$student_id = $_SESSION['user_id'];
$student = new Student($conn);
$appointments = $student->view_appointmentrequest($student_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Appointments</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="../CSS/view_appointment.css">
</head>
<body>
<button type="button" onclick="window.location.href='../student_dashboard.php'">Home</button>

<h3>Your Appointments</h3>

<table id="appointmentsTable" class="display">
    <thead>
        <tr>
            <th>Appointment Date</th>
            <th>Appointment Time</th>
            <th>Reason</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        while ($row = $appointments->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['appointment_date']) . "</td>
                    <td>" . htmlspecialchars($row['appointment_time']) . "</td>
                    <td>" . htmlspecialchars($row['reason']) . "</td>
                    <td>" . htmlspecialchars($row['status']) . "</td>
                    <td>
                        <form method='POST' action='student_serverside.php' style='display:inline-block;'>
                            <input type='hidden' name='appointment_id' value='" . $row['appointment_id'] . "'>
                            <button type='submit' name='delete' onclick='return confirm(\"Are you sure you want to delete this appointment?\")'>Delete</button>
                        </form>
                    </td>
                </tr>";
        }
        ?>
    </tbody>
</table>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        $('#appointmentsTable').DataTable(); 
    });
</script>

</body>
</html>
