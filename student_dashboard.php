<?php
// session_start();
// require_once 'eclinic_database.php';

// $user_role = $_SESSION['role'];

// if (!isset($_SESSION['user_id']) || $user_role != 'student') {
//     header('Location: login.php'); 
//     exit();
// }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
</head>
<body>
    <h1>Welcome to the Student Dashboard</h1>
    <nav>
        <ul>
        <li><a href="Student_Folder/appointment_req.html">Request an Appointment</a></li>
        <li><a href="Student_Folder/medication_request.html">Request Medication</a></li>
            <li><a href="Student_Folder/view_appointment.php">View Appointments</a></li>
            <li><a href="Student_Folder/medical_history.php">View Medical History</a></li>
            <li><a href="Student_Folder/view_availability.html">View Available Doctors</a></li>
            <li><a href="Student_Folder/appointment_status.php">Check Appointment Status</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
</body>
</html>
