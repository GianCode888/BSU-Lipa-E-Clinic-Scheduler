<?php
session_start();

require_once '../eclinic_database.php';
require_once 'nurse_dashboard_crud.php';

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access. Please log in.");
}

$nurse_id = $_SESSION['user_id'];

$database = new DatabaseConnection();
$conn = $database->getConnect();
$nurseManager = new NurseManager($conn);

$allRequests = $nurseManager->student_appointment_request();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_appointment_id'])) {
        $appointment_id = $_POST['delete_appointment_id'];
        $nurseManager->delete_appointment($appointment_id);
    } elseif (isset($_POST['delete_medication_id'])) {
        $medication_id = $_POST['delete_medication_id'];
        $nurseManager->delete_medication($medication_id);
    }

    header("Location: view_medication_requests.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Requests</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
</head>
<body>
<button type="button" onclick="window.location.href='../nurse_dashboard.php'">Home</button>

<h3>Pending Requests</h3>

<table id="requestsTable" class="display">
    <thead>
        <tr>
            <th>Request Type</th>
            <th>Details</th>
            <th>Request Date</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($allRequests as $row) {
            if ($row['request_type'] === 'Appointment') {
                $requestType = "Appointment";
                $details = "Reason: " . htmlspecialchars($row['reason']) . ", Time: " . htmlspecialchars($row['appointment_time']);
                $requestDate = htmlspecialchars($row['appointment_date']);
                $status = ucfirst(htmlspecialchars($row['status']));
                $id = $row['request_id'];
            } else {
                $requestType = "Medication";
                $details = "Medication: " . htmlspecialchars($row['reason']);
                $requestDate = htmlspecialchars($row['appointment_date']);
                $status = ucfirst(htmlspecialchars($row['status']));
                $id = $row['request_id'];
            }

            echo "<tr>
                    <td>" . $requestType . "</td>
                    <td>" . $details . "</td>
                    <td>" . $requestDate . "</td>
                    <td>" . $status . "</td>
                    <td>
                        <form method='POST' action='view_medication_requests.php' style='display:inline-block;'>
                            <input type='hidden' name='" . ($requestType == 'Medication' ? 'delete_medication_id' : 'delete_appointment_id') . "' value='" . $id . "'>
                            <button type='submit' name='delete' onclick='return confirm(\"Are you sure you want to delete this " . strtolower($requestType) . " request?\")'>Delete</button>
                        </form>
                    </td>
                </tr>";
        }
        ?>
    </tbody>
</table>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        $('#requestsTable').DataTable();
    });
</script>

</body>
</html>
