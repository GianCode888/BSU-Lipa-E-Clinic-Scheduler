<?php
session_start();
include '../eclinic_database.php';

$student_id = $_SESSION['user_id'];

$sql = "SELECT * FROM appointments WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

echo "<h3>Your Appointment Status</h3>";
echo "<table border='1'>";
echo "<tr><th>Date</th><th>Time</th><th>Status</th><th>Reason</th><th>Doctor/Nurse</th></tr>";

while ($row = $result->fetch_assoc()) {
    if ($row['doctor_id']) {
        $doctor_sql = "SELECT name FROM users WHERE id = ?";
        $doctor_stmt = $conn->prepare($doctor_sql);
        $doctor_stmt->bind_param("i", $row['doctor_id']);
        $doctor_stmt->execute();
        $doctor_result = $doctor_stmt->get_result();
        $doctor = $doctor_result->fetch_assoc()['name'];
    } else {
        $doctor = 'N/A';  
    }

    if ($row['nurse_id']) {
        $nurse_sql = "SELECT name FROM users WHERE id = ?";
        $nurse_stmt = $conn->prepare($nurse_sql);
        $nurse_stmt->bind_param("i", $row['nurse_id']);
        $nurse_stmt->execute();
        $nurse_result = $nurse_stmt->get_result();
        $nurse = $nurse_result->fetch_assoc()['name'];
    } else {
        $nurse = 'N/A';
    }

    echo "<tr>
        <td>{$row['appointment_date']}</td>
        <td>{$row['appointment_time']}</td>
        <td>{$row['status']}</td>
        <td>{$row['reason']}</td>
        <td>Doctor: {$doctor} / Nurse: {$nurse}</td>
    </tr>";
}

echo "</table>";
?>
