<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header("Location: /login.php");
    exit();
}

include '../admin_sidebar.php';
require_once '../Admin_Functions/SuperAdminService.php';
require_once '../Admin_Functions/notification_service.php';

$notificationService = new NotificationService();
$notification = '';

if (isset($_SESSION['login_notification'])) {
    $notification = $_SESSION['login_notification'];
    unset($_SESSION['login_notification']); 
}

$adminService = new SuperAdminService();
$stats = $adminService->getDashboardStats();

$recentActivity = $adminService->getRecentActivity(5); 
$recentAppointments = $adminService->getRecentAppointments(5);
$recentMedicalRecords = $adminService->getRecentMedicalRecords(5);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../admin_styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<div class="main-content">
    <h1>Super Admin Dashboard</h1>
    
    <div class="dashboard-cards">
        <div class="card">
            <h3>Total Users</h3>
            <div class="card-stat"><?php echo isset($stats['total_users']) ? $stats['total_users'] : 0; ?></div>
        </div>
        
        <div class="card">
            <h3>Total Doctors/Nurse</h3>
            <div class="card-stat"><?php echo isset($stats['available_medical_staff']) ? $stats['available_medical_staff'] : 0; ?></div>
        </div>
        
        <div class="card">
            <h3>Pending Medication</h3>
            <div class="card-stat"><?php echo isset($stats['pending_med_requests']) ? $stats['pending_med_requests'] : 0; ?></div>
        </div>

        <div class="card">
            <h3>Pending Appointments</h3>
            <div class="card-stat"><?php echo isset($stats['pending_appointments']) ? $stats['pending_appointments'] : 0; ?></div>
        </div>
    </div>
    


    <div class="row mt-5">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Recent Appointments</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Date</th>
                                    <th>Purpose</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recentAppointments)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No recent appointments found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recentAppointments as $appointment): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($appointment['appointment_date'])); ?></td>
                                            <td><?php echo htmlspecialchars($appointment['reason']); ?></td>
                                            <td>
                                                <?php if ($appointment['status'] == 'approved'): ?>
                                                    <span class="badge bg-success">Approved</span>
                                                <?php elseif ($appointment['status'] == 'pending'): ?>
                                                    <span class="badge bg-warning">Pending</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Declined</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Medical Records Section -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Recent Medical Records</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Date</th>
                                    <th>Medication</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recentMedicalRecords)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No recent medical records found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recentMedicalRecords as $record): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($record['patient_name']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($record['request_date'])); ?></td>
                                            <td><?php echo htmlspecialchars($record['medication']); ?></td>
                                            <td>
                                                <?php if ($record['status'] == 'approved'): ?>
                                                    <span class="badge bg-success">Approved</span>
                                                <?php elseif ($record['status'] == 'pending'): ?>
                                                    <span class="badge bg-warning">Pending</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Declined</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php echo $notification; ?>
</body>
</html>