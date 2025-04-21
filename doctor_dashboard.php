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
    <link rel="stylesheet" href="../CSS/student_dashboard.css">
</head>
<body>

<header>
    <img src="Images/Red.png" alt="Logo" class="logo">
    <h1>Doctor Dashboard</h1>
    <p class="quote">Leading Innovation, Transforming Lives, Building the Motion</p>
</header>

    <nav>
        <ul>
            <li><a href="Doctor_Folder/student_request.php">View Student Request</a></li>
            
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <footer>
        &copy; <?php echo date('Y'); ?> Spartan eClinic Scheduler
    </footer>

</body>
</html>
