<?php
require_once '../eclinic_database.php';
require_once 'doctor_crud.php';

$database = new DatabaseConnection();
$conn = $database->getConnect();

$doctor = new Doctor($conn);
$requests = $doctor->viewMedicationRequests(); // <- this is now assumed to return an array

echo "<h3>Medication Requests</h3>";
echo "<table border='1'><tr><th>Student</th><th>Request</th></tr>";

foreach ($requests as $row) {
    echo "<tr>
        <td>{$row['student_name']}</td>
        <td>{$row['request_details']}</td>
    </tr>";
}
echo "</table>";
?>
