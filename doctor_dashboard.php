<?php
session_start();
require_once 'eclinic_database.php';

$user_role = $_SESSION['role'];

if (!isset($_SESSION['user_id']) || $user_role != 'doctor') {
    header('Location: login.php'); 
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
    <link rel="stylesheet" href="CSS/doctor_dashboard.css">
</head>
<body>

<header>
    <h1>Doctor Dashboard</h1>

</header>

<nav>
    <ul>
        <li><a href="Doctor_Folder/view_requests.php">View Appointment Requests</a></li>
        <li><a href="Doctor_Folder/medication_requests.php">View Medication Requests</a></li>
        <li><a href="Doctor_Folder/view_availability.php">View Availability</a></li>
        <li><a href="Doctor_Folder/update_availability.php">Update Availability</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<footer>
    &copy; <?= date('Y'); ?> Spartan eClinic
</footer>

</body>
</html>
