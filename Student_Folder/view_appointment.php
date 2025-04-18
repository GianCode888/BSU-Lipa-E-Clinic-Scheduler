<?php
session_start();
include '../eclinic_database.php';

$database = new DatabaseConnection();
$conn = $database->getConnect();

$student_id = $_SESSION['user_id'];

$sql = "SELECT * FROM appointments WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bindParam(1, $student_id, PDO::PARAM_INT);
$stmt->execute();

echo "<h3>Your Appointment Status</h3>";
echo '<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">';
echo '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>';
echo '<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>';

echo "<table id='appointmentsTable' class='display'>";
echo "<thead>
        <tr>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Reason</th>
            <th>Actions</th>
        </tr>
      </thead>";
echo "<tbody>";

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>
            <td>{$row['appointment_date']}</td>
            <td>{$row['appointment_time']}</td>
            <td>{$row['status']}</td>
            <td>{$row['reason']}</td>
            <td>
                <form method='post' action='student_crud.php' style='display:inline-block;' >
                    <input type='hidden' name='appointment_id' value='{$row['appointment_id']}'>
                    <button type='submit' name='delete' onclick='return confirm(\"Are you sure you want to delete this appointment?\")'>Delete</button>
                </form>
            </td>
        </tr>";
}

echo "</tbody></table>";
echo "<script>
        $(document).ready(function() {
            $('#appointmentsTable').DataTable(); 
        });
      </script>";
?>
