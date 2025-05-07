<?php
session_start();
// Include required files
include("eclinic_database.php");
include("Nurse_Folder/nurse_dashboard_crud.php");

// Check if user is logged in and is a nurse
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'nurse') {
    header('Location: login.php');
    exit();
}

$nurse_id = $_SESSION['user_id'];
$database = new DatabaseConnection();
$nurseManager = new NurseManager($database->getConnect());

// Get nurse information
$nurse = $nurseManager->getNurseInfo($nurse_id);

// Get dashboard data
$pendingDispensing = $nurseManager->countPendingDispensing();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nurse Dashboard - eClinic</title>
    <link rel="stylesheet" href="Nurse_Folder/nurse_dashboard.css">
</head>
<body>
    <header>
        <h1>eClinic Scheduler</h1>
        <p>Welcome, Nurse <?php echo htmlspecialchars($nurse['first_name'] . ' ' . $nurse['last_name']); ?></p>
        <nav>
            <ul>
                <li><a href="Nurse_Folder/patient_log.html">Patient Logs</a></li>
                <li><a href="Nurse_Folder/approved_request_list.php">Approved List</a></li>
                <li><a href="Nurse_Folder/view_completed_logs.php">View Completed Logs</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <!-- Display success/error messages if any -->
        <?php if(isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <?php 
                    echo $_SESSION['message']; 
                    unset($_SESSION['message']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php 
                    echo $_SESSION['error']; 
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
    
        <section class="dashboard-summary">
            <h2>Dashboard Summary</h2>
            <div class="dashboard-cards">
                <div class="card">
                    <h3>Pending Dispensing</h3>
                    <p class="count"><?php echo $pendingDispensing; ?></p>
                    <a href="Nurse_Folder/view_medication_requests.php" class="btn">View Requests</a>
                </div>
    </main>
    
    <footer>
        <p>&copy; <?php echo date('Y'); ?> eClinic Scheduler</p>
    </footer>
</body>
</html>