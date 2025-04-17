<?php
require 'db_connection.php';
require 'doctor_crud.php';

$doctor = new Doctor($conn);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $doctor->update_appointment_status($_POST['appointment_id'], $_POST['status'], $_POST['comment']);
    exit;
}

$appointments = $doctor->view_appointments();

echo '<table border="1">
<tr>
    <th>Student Name</th>
    <th>Date</th>
    <th>Time</th>
    <th>Status</th>
    <th>Action</th>
</tr>';

foreach ($appointments as $appointment) {
    echo '<tr>
        <td>' . htmlspecialchars($appointment['student_name']) . '</td>
        <td>' . htmlspecialchars($appointment['appointment_date']) . '</td>
        <td>' . htmlspecialchars($appointment['appointment_time']) . '</td>
        <td>' . htmlspecialchars($appointment['status']) . '</td>
        <td>
            <form method="POST" action="view_appointments.php">
                <input type="hidden" name="appointment_id" value="' . $appointment['appointment_id'] . '">
                <select name="status" required>
                    <option value="">Select</option>
                    <option value="Approved">Approve</option>
                    <option value="Declined">Decline</option>
                </select><br>
                <textarea name="comment" placeholder="Optional comment"></textarea><br>
                <button type="submit" name="update_status">Submit</button>
            </form>
        </td>
    </tr>';
}
echo '</table>';
?>
