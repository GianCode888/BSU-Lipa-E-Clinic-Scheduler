<?php
include '../eclinic_database.php';
include '../Student_Folder/student_crud.php';

$database = new DatabaseConnection();
$conn = $database->getConnect();
$studentCrud = new Student($conn);

if (isset($_GET['available_day']) && $_GET['available_day'] !== '') {
    $available_day = $_GET['available_day'];
    $stmt = $studentCrud->view_availableDoctor($available_day);

    echo "<h3>Available Doctors/Nurses on {$available_day}</h3>";

    echo '<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">';
    echo '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>';
    echo '<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>';
    echo "<table id='availabilityTable' class='display'>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                </tr>
            </thead>
            <tbody>";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>
                <td>{$row['first_name']} {$row['last_name']}</td>
                <td>{$row['role']}</td>
                <td>{$row['start_time']}</td>
                <td>{$row['end_time']}</td>
              </tr>";
    }

    echo "</tbody></table>";
} else {
    echo "<p>Please select a day to view availability.</p>";
}
?>

<script>
    $(document).ready(function() {
        $('#availabilityTable').DataTable();
    });
</script>
