<?php
session_start();
require_once '../eclinic_database.php';
require_once 'doctor_crud.php';
require_once 'doctor_functions.php'; 

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$database = new DatabaseConnection();
$conn = $database->getConnect();

showScheduleForm($conn);
?>
