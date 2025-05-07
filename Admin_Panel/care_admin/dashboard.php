<?php
session_start();

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'care_admin' && $_SESSION['role'] !== 'super_admin')) {
    header("Location: /login.php");
    exit();
}

include '../admin_sidebar.php';
require_once '../Admin_Functions/MedDataService.php';
require_once '../Admin_Functions/notification_service.php';

$notificationService = new NotificationService();
$notification = '';

if (isset($_SESSION['login_notification'])) {
    $notification = $_SESSION['login_notification'];
    unset($_SESSION['login_notification']); 
}

$medDataService = new MedDataService();
$totalPatients = $medDataService->getTotalPatients();
$newRecords = $medDataService->getNewRecordsLastWeek();
$pendingRequests = $medDataService->getPendingRequests();

// Get appointment statistics
$todaysAppointments = $medDataService->getTodaysAppointments();
$pendingAppointments = $medDataService->getPendingAppointments();
$availableDoctors = $medDataService->getAvailableDoctors();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Care Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../admin_styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="main-content">
    <h1>Care Admin Dashboard</h1>
    
    <!-- Medical Data Section -->
    <h2 class="mt-4">Medical Data Overview</h2>
    <div class="dashboard-cards">
        <div class="card">
            <h3>Total Patients</h3>
            <div class="card-stat"><?php echo $totalPatients; ?></div>
            <p>Registered in the system</p>
        </div>
        
        <div class="card">
            <h3>New Records</h3>
            <div class="card-stat"><?php echo $newRecords; ?></div>
            <p>Added in the last 7 days</p>
        </div>
        
        <div class="card">
            <h3>Pending Requests</h3>
            <div class="card-stat"><?php echo $pendingRequests; ?></div>
            <p>Medication requests awaiting approval</p>
        </div>
    </div>
    
    <!-- Appointment Section -->
    <h2 class="mt-4">Appointment Overview</h2>
    <div class="dashboard-cards">
        <div class="card">
            <h3>Today's Appointments</h3>
            <div class="card-stat"><?php echo $todaysAppointments; ?></div>
            <p>Scheduled for today</p>
        </div>
        
        <div class="card">
            <h3>Pending Appointments</h3>
            <div class="card-stat"><?php echo $pendingAppointments; ?></div>
            <p>Awaiting confirmation</p>
        </div>
        
        <div class="card">
            <h3>Available Doctors</h3>
            <div class="card-stat"><?php echo $availableDoctors; ?></div>
            <p></p>
        </div>
    </div>
    


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?php echo $notification; ?>
</body>
</html>