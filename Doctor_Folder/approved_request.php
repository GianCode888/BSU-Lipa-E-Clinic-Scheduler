<?php
include '../Doctor_Folder/doctor_crud.php';
require_once '../eclinic_database.php';

$database = new DatabaseConnection();
$conn = $database->getConnect();

$doctor = new Doctor($conn);
$requests = $doctor->student_appointment_request();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Approved Requests</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#approvedRequestsTable').DataTable();
        });
    </script>
</head>
<body>

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
                    <th>Actions</th>
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
                        <td>
                            <a href="note_approval.php?request_id=<?php echo $request['request_id']; ?>&request_type=<?php echo $request['request_type']; ?>">
                                <button type="button">Add/Edit Notes</button>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No approved requests found.</p>
    <?php endif; ?>

</body>
</html>
