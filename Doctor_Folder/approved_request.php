<?php
include '../Doctor_Folder/doctor_crud.php';
require_once '../eclinic_database.php';

$database = new DatabaseConnection();
$conn = $database->getConnect();
$doctor = new Doctor($conn);

$approved_requests = $doctor->get_all_approved_requests();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Approved Requests</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="../CSS/approved_request.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#approvedRequestsTable').DataTable();
        });

        function toggleForm() {
            const form = document.getElementById('approvalForm');
            form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
        }
    </script>
</head>
<body>

    <button type="button" onclick="history.back()">Home</button>

    <div class="container">
        <h2>All Approved Requests</h2>

        <?php if (!empty($approved_requests)): ?>
            <table id="approvedRequestsTable" class="display">
                <thead>
                    <tr>
                        <th>Request Type</th>
                        <th>Student Name</th>
                        <th>Request Date</th>
                        <th>Appointment Date</th>
                        <th>Appointment Time</th>
                        <th>Description</th>
                        <th>Approval Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($approved_requests as $request): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($request['request_type']); ?></td>
                            <td><?php echo htmlspecialchars($request['student_name']); ?></td>
                            <td><?php echo htmlspecialchars($request['request_date']); ?></td>
                            <td><?php echo htmlspecialchars($request['appointment_date'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($request['appointment_time'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($request['description']); ?></td>
                            <td><?php echo htmlspecialchars($request['approval_notes'] ?? 'No notes provided'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No approved requests found.</p>
        <?php endif; ?>

    </div>

</body>
</html>
