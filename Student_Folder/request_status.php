<?php
require_once '../eclinic_database.php';
require_once 'student_serverside.php';

$database = new DatabaseConnection();
$conn = $database->getConnect();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = 'You must be logged in to view your request status.';
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['user_id'];
$student = new Student($conn);
$requests = $student->view_requestStatus($student_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Status</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
</head>
<body>
<button type="button" onclick="window.location.href='../student_dashboard.php'">Home</button>
    
    <h3>Your Request Status</h3>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="message"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="error-message"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
    <?php endif; ?>
    
    <table id="requestsTable" class="display">
        <thead>
            <tr>
                <th>Request Type</th>  
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Reason</th>
                <th>Approval Notes</th>
                <th>Approved By</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($requests as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['request_type']); ?></td>
                    <td><?php echo htmlspecialchars($row['request_date']); ?></td>
                    <td><?php echo $row['request_time'] ?? '-'; ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td><?php echo htmlspecialchars($row['reason']); ?></td>
                    <td><?php echo htmlspecialchars($row['approval_notes']) ?: 'No notes'; ?></td>
                    <td><?php echo htmlspecialchars($row['approved_by_first_name'] . ' ' . $row['approved_by_last_name'] ?: 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($row['approved_by_role'] ?: 'N/A'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        $(document).ready(function() {
            $('#requestsTable').DataTable();
        });
    </script>
</body>
</html>