<?php
include '../eclinic_database.php';
include '../Student_Folder/student_crud.php';

$student_id = $_SESSION['student_id'] ?? null;

$database = new DatabaseConnection();
$conn = $database->getConnect();
$studentCrud = new Student($conn);
$stmt = $studentCrud->view_appointmentStatus($student_id);

echo "<h3>Your Appointment Status</h3>";
echo '<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">';
echo '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>';
echo '<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>';

echo "<table id='appointmentsTable' class='display'>
    <thead>
        <tr>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Reason</th>
            <th>Name</th>
            <th>Role</th>
            <th>Approval Notes</th>
        </tr>
    </thead>
    <tbody>";

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $doctor = (!empty($row['doctor_first_name']) && !empty($row['doctor_last_name'])) ? $row['doctor_first_name'] . ' ' . $row['doctor_last_name'] : null;
    $nurse = (!empty($row['nurse_first_name']) && !empty($row['nurse_last_name'])) ? $row['nurse_first_name'] . ' ' . $row['nurse_last_name'] : null;

    if ($doctor) {
        $name = $doctor;
        $role = 'Doctor';
    } elseif ($nurse) {
        $name = $nurse;
        $role = 'Nurse';
    } else {
        $name = 'N/A';
        $role = 'N/A';
    }

    $approval_notes = !empty($row['approval_notes']) ? htmlspecialchars($row['approval_notes']) : 'No notes';

    echo "<tr>
        <td>{$row['appointment_date']}</td>
        <td>{$row['appointment_time']}</td>
        <td>{$row['status']}</td>
        <td>{$row['reason']}</td>
        <td>{$name}</td>
        <td>{$role}</td>
        <td>{$approval_notes}</td>
    </tr>";
}

echo "</tbody></table>";
?>

<script>
    $(document).ready(function() {
        $('#appointmentsTable').DataTable(); 
    });
</script>
