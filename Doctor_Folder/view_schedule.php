<?php
require_once '../eclinic_database.php';
require_once 'doctor_serverside.php';

$database = new DatabaseConnection();
$conn = $database->getConnect();
$user_id = $_SESSION['user_id'];
$doctor = new Doctor($conn);
$schedule_data = $doctor->view_schedule($user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Schedule</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
</head>
<body>
    <h1>Schedule for User ID: <?php echo htmlspecialchars($user_id); ?></h1>
    <button type="button" onclick="window.location.href='../doctor_dashboard.php'">Home</button>

    <?php if (!empty($schedule_data)): ?>
        <table id="scheduleTable" class="display">
            <thead>
                <tr>
                    <th>Available Date</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($schedule_data as $schedule): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($schedule['available_date']); ?></td>
                        <td><?php echo htmlspecialchars($schedule['start_time']); ?></td>
                        <td><?php echo htmlspecialchars($schedule['end_time']); ?></td>
                        <td><?php echo htmlspecialchars($schedule['note']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No schedule available for this user.</p>
    <?php endif; ?>

    <script>
        $(document).ready(function() {
            $('#scheduleTable').DataTable();
        });

        function toggleForm() {
            const form = document.getElementById('formId');
            if (form) {
                form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
            }
        }
    </script>
</body>
</html>
