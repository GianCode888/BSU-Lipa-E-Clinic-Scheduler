<?php
session_start();
include '../eclinic_database.php';

$database = new DatabaseConnection();
$conn = $database->getConnect();

$student_id = $_SESSION['user_id'];
$date = $_POST['date'] . ' ' . $_POST['time'];
$reason = $_POST['reason'];

$sql = "INSERT INTO appointments (student_id, appointment_date, appointment_time, reason) 
        VALUES (?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bindParam(1, $student_id);
$stmt->bindParam(2, $date);
$stmt->bindParam(3, $reason);

if ($stmt->execute()) {
    echo "Appointment request submitted!";
} else {
    echo "Error: " . $conn->errorInfo()[2];
}
?>
