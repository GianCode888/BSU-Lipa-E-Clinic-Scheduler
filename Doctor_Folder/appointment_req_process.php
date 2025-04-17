<?php
session_start();
include '../eclinic_database.php';

$student_id = $_SESSION['user_id'];
$date = $_POST['date'] . ' ' . $_POST['time'];
$reason = $_POST['reason'];

$sql = "INSERT INTO appointments (student_id, appointment_date, appointment_time, reason) 
        VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isss", $student_id, $date, $time, $reason);

if ($stmt->execute()) {
    echo "Appointment request submitted!";
} else {
    echo "Error: " . $conn->error;
}
?>
