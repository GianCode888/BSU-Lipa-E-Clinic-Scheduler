<?php
require_once '../eclinic_database.php';
require_once '../Student_Folder/student_serverside.php';

$database = new DatabaseConnection();
$conn = $database->getConnect();
$student_id = $_SESSION['user_id'];
$student = new Student($conn);
$prescriptions = $student->get_prescription($student_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Prescriptions</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="../CSS/doctor_prescription.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#prescriptionTable').DataTable();
        });
    </script>
</head>
<body>
    <button type="button" onclick="history.back()">Home</button>
    <h2>Your Prescriptions</h2>

    <table id="prescriptionTable" class="display">
        <thead>
            <tr>
                <th>Prescription ID</th>
                <th>Diagnosis</th>
                <th>Prescription</th>
                <th>Created At</th>
                <th>Prescribed By</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($prescriptions) {
                foreach ($prescriptions as $prescription) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($prescription['prescription_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($prescription['diagnosis']) . "</td>";
                    echo "<td>" . htmlspecialchars($prescription['prescription']) . "</td>";
                    echo "<td>" . htmlspecialchars($prescription['created_at']) . "</td>";
                    echo "<td>" . htmlspecialchars($prescription['prescribed_by']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No prescriptions found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
