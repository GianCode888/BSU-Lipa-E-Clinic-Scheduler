<?php
session_start();
require_once '../eclinic_database.php'; 

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$database = new DatabaseConnection();
$conn = $database->getConnect();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: view_availability.php');
    exit();
}

$day = $_POST['available_day'] ?? '';
$start = $_POST['start_time'] ?? '';
$end = $_POST['end_time'] ?? '';
$doctorId = $_POST['doctor_id'] ?? '';

if ($day && $start && $end && $doctorId) {
    $stmt = $conn->prepare("CALL AddDoctorAvailability(?, ?, ?, ?)");
    $stmt->execute([$doctorId, $day, $start, $end]);
    $stmt->closeCursor();

    header('Location: view_availability.php'); 
    exit();
} else {
    echo "All fields are required.";
}

?>
