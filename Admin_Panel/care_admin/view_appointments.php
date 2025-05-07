<?php
session_start();

// Check if user is logged in and has the correct role
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'care_admin' && $_SESSION['role'] !== 'super_admin')) {
    header("Location: /login.php");
    exit();
}

include '../admin_sidebar.php';
require_once '../Admin_Functions/MedDataService.php';

$medDataService = new MedDataService();
$totalAppointments = $medDataService->getTotalAppointmentsCount();
$pendingToday = $medDataService->getPendingAppointmentsTodayCount();
$approvedToday = $medDataService->getApprovedAppointmentsTodayCount();
$declinedToday = $medDataService->getDeclinedAppointmentsTodayCount();

// Get all patients with appointments for the dropdown
$patients = $medDataService->getPatientsWithAppointments();

// Process filter form submission
$userId = isset($_GET['user_id']) ? $_GET['user_id'] : null;
$status = isset($_GET['status']) ? $_GET['status'] : null;

// Get filtered appointments
$appointments = $medDataService->getFilteredAppointments($userId, $status);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Appointments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../admin_styles.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.3/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.3/js/dataTables.bootstrap5.min.js"></script>
</head>
<body>

<div class="main-content">
    <h1>Appointment History</h1>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total</h5><h5>Appointments</h5>
                    <div class="card-stat"><?php echo $totalAppointments; ?></div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Pending Today</h5>
                    <div class="card-stat"><?php echo $pendingToday; ?></div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Approved Today</h5>
                    <div class="card-stat"><?php echo $approvedToday; ?></div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <h5 class="card-title">Declined Today</h5>
                    <div class="card-stat"><?php echo $declinedToday; ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Appointment Records</h5>
        </div>
        
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <form method="GET" action="">
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Patient</label>
                            <select class="form-select" id="user_id" name="user_id">
                                <option value="">All Patients</option>
                                <?php foreach ($patients as $patient): ?>
                                <option value="<?php echo $patient['user_id']; ?>" <?php echo ($userId == $patient['user_id']) ? 'selected' : ''; ?>>
                                    <?php echo $patient['first_name'] . ' ' . $patient['last_name']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Statuses</option>
                                <option value="pending" <?php echo ($status == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="approved" <?php echo ($status == 'approved') ? 'selected' : ''; ?>>Approved</option>
                                <option value="declined" <?php echo ($status == 'declined') ? 'selected' : ''; ?>>Declined</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                    </form>
                </div>
                
                <div class="col-md-9">
                    <div class="table-responsive">
                        <table id="appointmentsTable" class="table table-striped table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Appointment Date</th>
                                    <th>Patient</th>
                                    <th>Status</th>
                                    <th>Purpose</th>
                                    <th>Approved By</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($appointments as $appointment): ?>
                                <tr>
                                    <td><?php echo $appointment['appointment_time']; ?></td>
                                    <td><?php echo $appointment['appointment_date']; ?></td>
                                    <td><?php echo $appointment['patient_name']; ?></td>
                                    <td>
                                        <span class="badge <?php 
                                            echo match($appointment['status']) {
                                                'pending' => 'bg-warning',
                                                'approved' => 'bg-success',
                                                'cancelled' => 'bg-danger',
                                                'declined' => 'bg-secondary',
                                                'rescheduled' => 'bg-info',
                                                default => 'bg-primary'
                                            };
                                        ?>">
                                            <?php echo ucfirst($appointment['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $appointment['reason']; ?></td>
                                    <td>
                                        <?php 
                                        if (isset($appointment['approver_name']) && $appointment['approver_name']) {
                                            echo $appointment['approver_name'];
                                        } else {
                                            echo 'N/A';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                        if (isset($appointment['approval_notes']) && $appointment['approval_notes']) {
                                            echo '<span class="d-inline-block text-truncate" style="max-width: 150px;" title="' . htmlspecialchars($appointment['approval_notes']) . '">' . 
                                                htmlspecialchars($appointment['approval_notes']) . 
                                                '</span>';
                                        } else {
                                            echo 'N/A';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#appointmentsTable').DataTable({
            responsive: true,
            order: [[1, 'asc'], [0, 'asc']], // Sort by date, then time
            language: {
                search: "Search appointments:",
                lengthMenu: "Show _MENU_ appointments per page",
                info: "Showing _START_ to _END_ of _TOTAL_ appointments",
                emptyTable: "No appointments found for the selected filters"
            }
        });
    });
</script>
</body>
</html>
