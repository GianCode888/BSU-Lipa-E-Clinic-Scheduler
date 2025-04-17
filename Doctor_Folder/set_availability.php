<?php
require 'db_connection.php';
require 'doctor_crud.php';

session_start();
$doctor = new Doctor($conn);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $doctor->set_availability($_SESSION['user_id'], $_POST['day'], $_POST['start_time'], $_POST['end_time']);
    header("Location: set_availability.html");
}
?>
