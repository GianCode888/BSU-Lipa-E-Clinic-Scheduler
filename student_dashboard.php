<?php
session_start();
require_once 'eclinic_database.php';

$user_role = $_SESSION['role'];

if (!isset($_SESSION['user_id']) || $user_role != 'student') {
    header('Location: login.php'); 
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="../CSS/student_dashboard.css">
</head>
<body>

<header>
    <img src="Images/Red.png" alt="Logo" class="logo">
    <h1>Student Dashboard</h1>
    <p class="quote">Leading Innovation, Transforming Lives, Building the Motion</p>
</header>

    <nav>
        <ul>
            <li><a href="Student_Folder/appointment_req.html">Request an Appointment</a></li>
            <li><a href="Student_Folder/medication_request.html">Request Medication</a></li>
            <li><a href="Student_Folder/view_appointment.php">View Appointment Request</a></li>
            <li><a href="Student_Folder/view_medication.php">View Medication Request</a></li>
            <li><a href="Student_Folder/view_availability.html">View Available Doctors</a></li>
            <li><a href="Student_Folder/request_status.php">Check Request Status</a></li>
            <li><a href="Student_Folder/student_profile.php">View Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <footer>
        &copy; <?php echo date('Y'); ?> Spartan eClinic Scheduler
    </footer>

</body>
</html>
