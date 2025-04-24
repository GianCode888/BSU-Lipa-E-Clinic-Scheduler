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
$todayAppointments = $nurseManager->countTodayAppointments($nurse_id);
$recentActivity = $nurseManager->getRecentActivity($nurse_id);
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
                <li><a href="nurse_dashboard.php">Dashboard</a></li>
                <li><a href="Nurse_Folder/completed_medications.php">Completed Medications</a></li>
                <li><a href="patient_logs.php">Patient Logs</a></li>
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
                    <a href="Nurse_Folder/medication_requests.php" class="btn">View Requests</a>
                </div>
                
                <div class="card">
                    <h3>Today's Appointments</h3>
                    <p class="count"><?php echo $todayAppointments; ?></p>
                    <a href="appointments.php" class="btn">View Appointments</a>
                </div>
            </div>
        </section>
        
        <section class="recent-activity">
            <h2>Recent Activity</h2>
            <table>
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($recentActivity) > 0): ?>
                        <?php foreach ($recentActivity as $activity): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($activity['first_name'] . ' ' . $activity['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($activity['reason']); ?></td>
                                <td><?php echo ucfirst(htmlspecialchars($activity['status'])); ?></td>
                                <td><?php echo date('M d, Y H:i', strtotime($activity['appointment_date'])); ?></td>
                                <td>
                                    <?php if ($activity['status'] == 'pending'): ?>
                                        <form method="POST" action="nurse_dashboard_process.php">
                                            <input type="hidden" name="action" value="update_appointment">
                                            <input type="hidden" name="appointment_id" value="<?php echo $activity['appointment_id']; ?>">
                                            <select name="new_status">
                                                <option value="approved">Approve</option>
                                                <option value="rejected">Reject</option>
                                            </select>
                                            <button type="submit">Update</button>
                                        </form>
                                    <?php endif; ?>
                                    <a href="view_appointment.php?id=<?php echo $activity['appointment_id']; ?>" class="btn-small">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No recent appointments found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
    
    <footer>
        <p>&copy; <?php echo date('Y'); ?> eClinic Scheduler</p>
    </footer>
</body>
</html>