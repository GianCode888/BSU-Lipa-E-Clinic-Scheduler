<?php
session_start();
include '../eclinic_database.php';

$database = new DatabaseConnection();
$conn = $database->getConnect();

$student_id = $_SESSION['user_id'];

$sql = "SELECT * FROM medication_requests WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bindParam(1, $student_id, PDO::PARAM_INT);
$stmt->execute();

echo "<h3>Your Medication Requests</h3>";
echo '<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">';
echo '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>';
echo '<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>';

echo "<table id='medicationTable' class='display'>";
echo "<thead>
        <tr>
            <th>Medication</th>
            <th>Request Date</th>
            <th>Status</th>
        </tr>
      </thead>";
echo "<tbody>";

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>
            <td>{$row['medication']}</td>
            <td>{$row['request_date']}</td>
            <td>{$row['status']}</td>
          </tr>";
}

echo "</tbody></table>";

echo "<script>
        $(document).ready(function() {
            $('#medicationTable').DataTable(); 
        });
      </script>";
?>
