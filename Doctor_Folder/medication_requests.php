<?php
session_start();
require_once '../eclinic_database.php';
require_once 'doctor_crud.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$database = new DatabaseConnection();
$conn = $database->getConnect();

$doctor = new Doctor($conn);
$requests = $doctor->viewMedicationRequests(); // Returns an array of medication requests

echo "<h3>Medication Requests</h3>";
echo "<table border='1'><tr><th>Student</th><th>Request</th><th>Action</th></tr>";

foreach ($requests as $row) {
    echo "<tr>
        <td>{$row['student_name']}</td>
        <td>{$row['request_details']}</td>
        <td>
            <form action='doctor_crud.php' method='POST'>
                <input type='hidden' name='request_id' value='{$row['request_id']}'>
                <button type='submit' name='med_action' value='approve'>Approve</button>
                <button type='submit' name='med_action' value='decline'>Decline</button>
            </form>
        </td>
    </tr>";
}
echo "</table>";
?>
