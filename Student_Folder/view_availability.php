<?php
include '../eclinic_database.php';

$database = new DatabaseConnection();
$conn = $database->getConnect();
$availability = [];

if (isset($_GET['available_day']) && $_GET['available_day'] !== '') {
    $day = $_GET['available_day'];

    // Fetch availability data for the selected day
    $sql = "SELECT a.*, u.first_name, u.last_name, u.email, u.role 
            FROM availability a 
            LEFT JOIN users u ON a.doctor_id = u.user_id
            WHERE a.available_day = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $day, PDO::PARAM_STR);
    $stmt->execute();
    $availability = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h3>Available Doctors/Nurses on {$day}</h3>";

    // DataTable integration (CSS & JS)
    echo '<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">';
    echo '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>';
    echo '<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>';

    // Table with DataTable functionality
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

    foreach ($availability as $row) {
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
        // Initialize DataTable
        $('#availabilityTable').DataTable();
    });
</script>
