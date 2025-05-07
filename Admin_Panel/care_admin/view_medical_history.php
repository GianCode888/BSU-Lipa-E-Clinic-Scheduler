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
$pendingRequests = $medDataService->getPendingMedicationRequestsCount();
$approvedRequests = $medDataService->getApprovedMedicationRequestsCount();
$rejectedRequests = $medDataService->getRejectedMedicationRequestsCount();

$pendingToday = $medDataService->getPendingTodayCount();
$approvedToday = $medDataService->getApprovedTodayCount();
$rejectedToday = $medDataService->getRejectedTodayCount();

// Fetch all records by status
$pendingRecords = $medDataService->getMedicalRecordsByStatus('pending');
$approvedRecords = $medDataService->getMedicalRecordsByStatus('approved');
$rejectedRecords = $medDataService->getMedicalRecordsByStatus('declined');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../admin_styles.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.3/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.3/js/jquery.dataTables.min.js"></script>
</head>
<body>

<div class="main-content">
    <h1>Medical History</h1>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Medication</h5>
                    <div class="card-stat"><?php echo $medDataService->getTotalMedicationCount(); ?></div>
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
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title">Rejected Today</h5>
                    <div class="card-stat"><?php echo $rejectedToday; ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Medical Records</h5>
            <ul class="nav nav-tabs card-header-tabs" id="requestTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="true">Pending (<?php echo $pendingRequests; ?>)</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved" type="button" role="tab" aria-controls="approved" aria-selected="false">Approved (<?php echo $approvedRequests; ?>)</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected" type="button" role="tab" aria-controls="rejected" aria-selected="false">Rejected (<?php echo $rejectedRequests; ?>)</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="all-records-tab" data-bs-toggle="tab" data-bs-target="#all-records" type="button" role="tab" aria-controls="all-records" aria-selected="false">All Records (<?php echo $medDataService->getTotalMedicationCount(); ?>)</button>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content" id="requestTabsContent">
                <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                    <table id="pendingTable" class="display table table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>Medication ID</th>
                                <th>Date</th>
                                <th>Patient</th>
                                <th>Medication</th>
                                <th>Notes</th>
                                <th>Status</th>
                                <th>Medical Info</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pendingRecords)): ?>
                            <tr>
                                <td colspan="7" class="text-center">No pending records found</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($pendingRecords as $record): ?>
                                <tr>
                                    <td><?php echo $record['medication_id']; ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($record['request_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($record['patient_name']); ?></td>
                                    <td><?php echo htmlspecialchars($record['medication']); ?></td>
                                    <td><?php echo htmlspecialchars($record['notes'] ?? ''); ?></td>
                                    <td><span class="badge bg-warning">Pending</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary view-record" data-bs-toggle="modal" data-bs-target="#viewModal" 
                                            data-id="<?php echo $record['medication_id']; ?>"
                                            data-student-id="<?php echo $record['student_id']; ?>">
                                            View
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="tab-pane fade" id="approved" role="tabpanel" aria-labelledby="approved-tab">
                    <table id="approvedTable" class="display table table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>Medication ID</th>
                                <th>Date</th>
                                <th>Patient</th>
                                <th>Medication</th>
                                <th>Notes</th>
                                <th>Approved By</th>
                                <th>Status</th>
                                <th>Medical Info</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($approvedRecords)): ?>
                            <tr>
                                <td colspan="8" class="text-center">No approved records found</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($approvedRecords as $record): ?>
                                <tr>
                                    <td><?php echo $record['medication_id']; ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($record['request_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($record['patient_name']); ?></td>
                                    <td><?php echo htmlspecialchars($record['medication']); ?></td>
                                    <td><?php echo htmlspecialchars($record['notes'] ?? ''); ?></td>
                                    <td>
                                        <?php 
                                        if (isset($record['approved_by']) && $record['approved_by']) {
                                            // Get the approver's name from the users table
                                            $approver = "User ID: " . $record['approved_by'];
                                            echo htmlspecialchars($approver);
                                        } else {
                                            echo "Not specified";
                                        }
                                        ?>
                                    </td>
                                    <td><span class="badge bg-success">Approved</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary view-record" data-bs-toggle="modal" data-bs-target="#viewModal" 
                                            data-id="<?php echo $record['medication_id']; ?>"
                                            data-student-id="<?php echo $record['student_id']; ?>">
                                            View
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="tab-pane fade" id="rejected" role="tabpanel" aria-labelledby="rejected-tab">
                    <table id="rejectedTable" class="display table table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>Medication ID</th>
                                <th>Date</th>
                                <th>Patient</th>
                                <th>Medication</th>
                                <th>Notes</th>
                                <th>Rejected By</th>
                                <th>Status</th>
                                <th>Medical Info</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($rejectedRecords)): ?>
                            <tr>
                                <td colspan="8" class="text-center">No rejected records found</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($rejectedRecords as $record): ?>
                                <tr>
                                    <td><?php echo $record['medication_id']; ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($record['request_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($record['patient_name']); ?></td>
                                    <td><?php echo htmlspecialchars($record['medication']); ?></td>
                                    <td><?php echo htmlspecialchars($record['notes'] ?? ''); ?></td>
                                    <td>
                                        <?php 
                                        if (isset($record['approved_by']) && $record['approved_by']) {
                                            // Get the approver's name from the users table
                                            $approver = "User ID: " . $record['approved_by'];
                                            echo htmlspecialchars($approver);
                                        } else {
                                            echo "Not specified";
                                        }
                                        ?>
                                    </td>
                                    <td><span class="badge bg-danger">Rejected</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary view-record" data-bs-toggle="modal" data-bs-target="#viewModal"
                                            data-id="<?php echo $record['medication_id']; ?>"
                                            data-student-id="<?php echo $record['student_id']; ?>">
                                            View
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
    
                <div class="tab-pane fade" id="all-records" role="tabpanel" aria-labelledby="all-records-tab">
                    <table id="allRecordsTable" class="display table table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>Medication ID</th>
                                <th>Date</th>
                                <th>Patient</th>
                                <th>Medication</th>
                                <th>Notes</th>
                                <th>Approved/Rejected By</th>
                                <th>Status</th>
                                <th>Medical Info</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $allRecords = $medDataService->getAllMedicalRecords();
                            if (empty($allRecords)): 
                            ?>
                            <tr>
                                <td colspan="8" class="text-center">No records found</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($allRecords as $record): ?>
                                <tr>
                                    <td><?php echo $record['medication_id']; ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($record['request_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($record['patient_name']); ?></td>
                                    <td><?php echo htmlspecialchars($record['medication']); ?></td>
                                    <td><?php echo htmlspecialchars($record['notes'] ?? ''); ?></td>
                                    <td>
                                        <?php 
                                        if (isset($record['approved_by']) && $record['approved_by']) {
                                            // Get the approver's name from the users table
                                            $approver = "User ID: " . $record['approved_by'];
                                            echo htmlspecialchars($approver);
                                        } else {
                                            echo "Not specified";
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($record['status'] == 'pending'): ?>
                                            <span class="badge bg-warning">Pending</span>
                                        <?php elseif ($record['status'] == 'approved'): ?>
                                            <span class="badge bg-success">Approved</span>
                                        <?php elseif ($record['status'] == 'declined'): ?>
                                            <span class="badge bg-danger">Rejected</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary view-record" data-bs-toggle="modal" data-bs-target="#viewModal"
                                            data-id="<?php echo $record['medication_id']; ?>"
                                            data-student-id="<?php echo $record['student_id']; ?>">
                                            View
                                        </button>
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
    
    <!-- View Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewModalLabel">Student Medical Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="medicalInfoContent">
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p>Loading medical information...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize DataTables for all tables
    $('#pendingTable').DataTable({
        order: [[0, 'desc']]
    });
    
    $('#approvedTable').DataTable({
        order: [[0, 'desc']]
    });
    
    $('#rejectedTable').DataTable({
        order: [[0, 'desc']]
    });

    $('#allRecordsTable').DataTable({
        order: [[0, 'desc']]
    });
    
    // Handle tab switching
    $('button[data-bs-toggle="tab"]').on('click', function (e) {
        var targetTab = $(this).data('bs-target');
        $('.tab-pane').removeClass('show active');
        $(targetTab).addClass('show active');
        
        // Update active state on tabs
        $('.nav-link').removeClass('active');
        $(this).addClass('active');
        
        // Adjust DataTables when shown in a previously hidden tab
        $(targetTab).find('table.dataTable').each(function() {
            $(this).DataTable().columns.adjust().draw();
        });
    });
    
    // Handle view modal data
    $('.view-record').on('click', function() {
        var studentId = $(this).data('student-id');
        console.log("Student ID:", studentId);
        
        // Redirect to a page that will fetch and display the medical info
        var medicalInfoUrl = 'view_student_medical_info.php?user_id=' + studentId;
        $('#medicalInfoContent').html('<iframe src="' + medicalInfoUrl + '" style="width:100%;height:400px;border:none;"></iframe>');
    });
    
    // Also handle the modal event for backward compatibility
    $('#viewModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var studentId = button.data('student-id');
        
        if (studentId) {
            console.log("Student ID from modal event:", studentId);
            
            // Redirect to a page that will fetch and display the medical info
            var medicalInfoUrl = 'view_student_medical_info.php?user_id=' + studentId;
            $('#medicalInfoContent').html('<iframe src="' + medicalInfoUrl + '" style="width:100%;height:400px;border:none;"></iframe>');
        }
    });
});
</script>
</body>
</html>
