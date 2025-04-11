<?php
include 'db.php';

$id = $_GET['id'];
$sql = "UPDATE appointments SET status='completed' WHERE appointment_id=$id";
$conn->query($sql);

header("Location: nurse_appointments.php");
exit;
?>
