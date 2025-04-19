<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header("Location: /login.php");
    exit();
}

include '../admin_sidebar.php';
require_once '../Admin_Functions/SuperAdminService.php';

// Initialize the service
$adminService = new SuperAdminService();

// Get dashboard statistics
$stats = $adminService->getDashboardStats();

// Get recent activity
$recentActivity = $adminService->getRecentActivity(5);

// Get recent appointments
$recentAppointments = $adminService->getAppointmentsByStatus('pending');

// Get recent medication requests
$recentMedRequests = $adminService->getMedicationRequestsByStatus('pending');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../admin_styles.css">
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
            <h3>Available Doctors/Nurse</h3>
            <div class="card-stat"><?php echo isset($stats['available_medical_staff']) ? $stats['available_medical_staff'] : 0; ?></div>
        </div>
        
        <div class="card">
            <h3>Pending requests</h3>
            <div class="card-stat"><?php echo isset($stats['pending_med_requests']) ? $stats['pending_med_requests'] : 0; ?></div>
        </div>

        <div class="card">
            <h3>Pending Appointments</h3>
            <div class="card-stat"><?php echo isset($stats['pending_appointments']) ? $stats['pending_appointments'] : 0; ?></div>
        </div>
        

    </div>
    
    <div class="mt-4">
    <h2>Recent Activity</h2>

    <div class="activity-container">
        
        <div class="activity-table">
            <h3>User</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Last Visit</th>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentActivity as $activity): ?>
                        <?php if (isset($activity['activity_type']) && $activity['activity_type'] === 'user_created'): ?>
                        <tr>
                            <td><?php echo date('Y-m-d', strtotime($activity['activity_date'])); ?></td>
                            <td><?php echo htmlspecialchars($activity['name']); ?></td>
                            <td><?php echo htmlspecialchars($activity['role']); ?></td>
                            <td>Active</td>
                        </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="activity-table">
            <h3>Medication Requests</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Patient</th>
                        <th>Medication</th>
                        <th>Doctor</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentMedRequests as $request): ?>
                    <tr>
                        <td><?php echo date('Y-m-d', strtotime($request['request_date'])); ?></td>
                        <td><?php echo htmlspecialchars($request['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($request['medication']); ?></td>
                        <td><?php echo isset($request['doctor_name']) ? htmlspecialchars($request['doctor_name']) : 'Not Assigned'; ?></td>
                        <td><?php echo ucfirst(htmlspecialchars($request['status'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="activity-table">
            <h3>Appointments</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Patient</th>
                        <th>Nurse</th>
                        <th>Reason</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentAppointments as $appointment): ?>
                    <tr>
                        <td><?php echo date('h:i A', strtotime($appointment['appointment_date'])); ?></td>
                        <td><?php echo htmlspecialchars($appointment['student_name']); ?></td>
                        <td><?php echo isset($appointment['nurse_name']) ? htmlspecialchars($appointment['nurse_name']) : 'Not Assigned'; ?></td>
                        <td><?php echo htmlspecialchars($appointment['reason']); ?></td>
                        <td><?php echo ucfirst(htmlspecialchars($appointment['status'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
