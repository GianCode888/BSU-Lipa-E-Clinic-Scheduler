<?php
require_once '../eclinic_database.php';
require_once 'student_serverside.php';

$database = new DatabaseConnection();
$conn = $database->getConnect();
$student_id = $_SESSION['user_id'];
$student = new Student($conn);
$appointments = $student->view_medicationrequest($student_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Medication Requests</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
</head>
<body>
    <button type="button" onclick="history.back()">Home</button>

    <h3>Your Medication Requests</h3>

    <table id="medicationTable" class="display">
        <thead>
            <tr>
                <th>Medication</th>
                <th>Request Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = $appointments->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['medication']) . "</td>
                        <td>" . htmlspecialchars($row['request_date']) . "</td>
                        <td>" . htmlspecialchars($row['status']) . "</td>
                        <td>
                            <form method='POST' action='student_serverside.php' style='display:inline-block;'>
                                <input type='hidden' name='medication_id' value='" . $row['medication_id'] . "'>
                                <button type='submit' name='delete' onclick='return confirm(\"Are you sure you want to delete this medication request?\")'>Delete</button>
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
            $('#medicationTable').DataTable(); 
        });
    </script>

</body>
</html>
