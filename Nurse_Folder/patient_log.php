<?php
include '../eclinic_database.php';
include 'nurse_dashboard_crud.php';

$database = new DatabaseConnection();
$db = $database->getConnect();
$nurseManager = new NurseManager($db);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_name = $_POST['student_name'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];
    $nurse_name = $_POST['nurse_name'];
    $log_details = $_POST['log_details'];

    if ($nurseManager->addPatientLog($student_name, $contact, $address, $nurse_name, $log_details)) {
        echo "<script>alert('Log added successfully!'); window.location.href='../nurse_dashboard.php';</script>";
    } else {
        echo "<script>alert('Failed to add log.');</script>";
    } 
}
// Fetch completed logs
$completedLogs = $nurseManager->getCompletedLogs();


?>