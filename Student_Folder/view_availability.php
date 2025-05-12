<?php
include '../eclinic_database.php';
include '../Student_Folder/student_serverside.php';

$database = new DatabaseConnection();
$conn = $database->getConnect();
$studentCrud = new Student($conn);

if (isset($_GET['available_date']) && $_GET['available_date'] !== '') {
    $day_name = $_GET['available_date'];
    $availableDoctors = $studentCrud->view_availableDoctorByDay($day_name);

    echo "<h3>Available Doctors on {$day_name}</h3>";

    echo '<button type="button" onclick="window.location.href=\'view_availability.html\'">Back</button>';

    echo '<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">';
    echo '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>';
    echo '<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>';
    echo '<link rel="stylesheet" href="../CSS/availibility_day.css">';

    echo "<table id='availabilityTable' class='display'>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Date</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody>";

    foreach ($availableDoctors as $row) {
        echo "<tr>
                <td>" . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . "</td>
                <td>" . htmlspecialchars($row['role']) . "</td>
                <td>" . htmlspecialchars($row['available_date']) . "</td>
                <td>" . htmlspecialchars($row['start_time']) . "</td>
                <td>" . htmlspecialchars($row['end_time']) . "</td>
                <td>" . htmlspecialchars($row['note']) . "</td>
              </tr>";
    }

    echo "</tbody>
        </table>";
} else {
    echo "<p>Please select a day to view availability.</p>";
}
?>

<script>
    $(document).ready(function() {
        $('#availabilityTable').DataTable();
    });
</script>
