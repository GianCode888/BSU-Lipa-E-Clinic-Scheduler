<?php
<<<<<<< HEAD
session_start();
include 'eclinic_database.php';
=======
include '../eclinic_database.php';
include '../Student_Folder/student_serverside.php';

$student_id = $_SESSION['student_id'] ?? null;
>>>>>>> aac356d49dff9a1dff7c8b4211ddb319cb451d5f

$database = new DatabaseConnection();
$conn = $database->getConnect();
$studentCrud = new Student($conn);
$stmt = $studentCrud->view_medicalHistory($student_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical History</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
</head>
<body>

<h3>Your Medical History</h3>

<table id="medicalHistoryTable" class="display">
    <thead>
        <tr>
            <th>Date</th>
            <th>Handled By</th>
            <th>Role</th>
            <th>Log</th>
        </tr>
    </thead>
    <tbody>
    <?php
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        
        $created_at = date('Y-m-d H:i:s', strtotime($row['created_at']));
        $handled_by = htmlspecialchars(trim($row['first_name'] . ' ' . $row['last_name']));
        $role = htmlspecialchars($row['created_by_role']);
        $log = nl2br(htmlspecialchars($row['log']));
        
        echo "<tr>
            <td>{$created_at}</td>
            <td>{$handled_by}</td>
            <td>{$role}</td>
            <td>{$log}</td>
        </tr>";
    }
    ?>
    </tbody>
</table>

<script>
    $(document).ready(function() {
        $('#medicalHistoryTable').DataTable(); 
    });
</script>

</body>
</html>
