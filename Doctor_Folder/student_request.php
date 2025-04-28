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
<button type="button" onclick="history.back()">Home</button>

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
            <th>Actions</th> 
        </tr>
    </thead>
    <tbody>
        <?php 
        if ($requests && $requests->rowCount() > 0) {
            while ($row = $requests->fetch(PDO::FETCH_ASSOC)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['request_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['request_type']); ?></td>
                    <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['appointment_time']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td><?php echo htmlspecialchars($row['reason']); ?></td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    <td>
                       
                        <a href="note_approval.php?request_id=<?php echo $row['request_id']; ?>&request_type=<?php echo $row['request_type']; ?>">
                            <button type="button">Approve</button>
                        </a>

                        
                        <form method="POST" action="doctor_crud.php" style="display:inline;">
                            <input type="hidden" name="request_id" value="<?php echo $row['request_id']; ?>">
                            <input type="hidden" name="request_type" value="<?php echo $row['request_type']; ?>">
                            <input type="hidden" name="action" value="decline">
                            <button type="submit" style="background-color:red;color:white;">Decline</button>
                        </form>
                    </td>
                </tr>
        <?php 
            }
        } else { ?>
            <tr>
                <td colspan="9" style="text-align:center;">No student requests found.</td>
            </tr>
        <?php } ?>
    </tbody>
</table>

</body>
</html>
