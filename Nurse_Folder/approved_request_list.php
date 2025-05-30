<?php
session_start();
include("../eclinic_database.php");
include("nurse_dashboard_crud.php");

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access. Please log in.");
}

$nurse_id = $_SESSION['user_id'];
$database = new DatabaseConnection();
$nurseManager = new NurseManager($database->getConnect());

$nurse = $nurseManager->getNurseInfo($nurse_id);
$approvedRequests = $nurseManager->student_appointment_request();

if (isset($_GET['request_id']) && isset($_GET['user_id'])) {
    $log_id = $_GET['request_id'];
    $user_id = $_GET['user_id'];

    if ($nurseManager->deleteCompletedLogs($log_id, $user_id)) {
        echo "<script>alert('Request deleted successfully!'); window.location.href='view_completed_logs.php';</script>";
    } else {
        echo "<script>alert('Failed to delete request.');</script>";
    }
}

$completedLogs = $nurseManager->getCompletedLogs();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Appointment and Medication Requests</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#requestsTable').DataTable();
        });
    </script>
</head>
<body>
    <button type="button" onclick="window.location.href='../nurse_dashboard.php'">Home</button>

    <h2>Student Requests (Appointments & Medications)</h2>

    <table id="requestsTable" class="display">
        <thead>
            <tr>
                <th>Request ID</th>
                <th>Student Name</th>
                <th>Request Type</th>
                <th>Request Date</th>
                <th>Appointment Time</th>
                <th>Status</th>
                <th>Reason</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if (!empty($approvedRequests)) {
                foreach ($approvedRequests as $row) {
                    $status = strtolower(trim($row['status']));
            ?>
                <tr>
                    <td><?= htmlspecialchars($row['request_id']) ?></td>
                    <td><?= htmlspecialchars($row['student_name']) ?></td>
                    <td><?= htmlspecialchars($row['request_type']) ?></td>
                    <td><?= htmlspecialchars($row['appointment_date']) ?></td>
                    <td><?= $row['appointment_time'] ?? '-' ?></td>
                    <td><?= ucfirst(htmlspecialchars($status)) ?></td>
                    <td><?= htmlspecialchars($row['reason']) ?></td>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                    <td>
                        <?php if ($status === 'pending'): ?>
                            <form method="GET" action="">
                                <input type="hidden" name="request_id" value="<?= $row['request_id'] ?>">
                                <input type="hidden" name="user_id" value="<?= $nurse_id ?>">
                                <button type="submit" onclick="return confirm('Are you sure you want to delete this request?')">Delete</button>
                            </form>
                        <?php else: ?>
                            <span>Already <?= ucfirst(htmlspecialchars($status)) ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php 
                }
            } else {
            ?>
                <tr>
                    <td colspan="9" style="text-align:center;">No student requests found.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>
