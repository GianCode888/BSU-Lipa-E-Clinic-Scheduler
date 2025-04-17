<?php
require_once '../eclinic_database.php';
require_once 'student_crud.php';

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
    <title>View Appointments</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
</head>
<body>

    <div class="container">
        <h1>Your Appointments</h1>

        <?php if ($appointments->rowCount() > 0): ?>
            <table id="appointmentsTable">
                <thead>
                    <tr>
                        <th>Appointment ID</th>
                        <th>Appointment Date</th>
                        <th>Appointment Time</th>
                        <th>Reason</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $appointments->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['appointment_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['appointment_time']); ?></td>
                            <td><?php echo htmlspecialchars($row['reason']); ?></td>
                            <td>
                                <form method="POST" action="view_appointment.php">
                                    <input type="hidden" name="appointment_id" value="<?php echo $row['appointment_id']; ?>">
                                    <button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete this appointment?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No appointments found.</p>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#appointmentsTable').DataTable();
        });

        function toggleForm() {
            const form = document.getElementById('formId');
            form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
        }
    </script>

</body>
</html>
