<?php
session_start();
require_once '../eclinic_database.php';
require_once 'doctor_crud.php';

// ✅ Session Check
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$database = new DatabaseConnection();
$conn = $database->getConnect();
$doctor_id = $_SESSION['user_id'];
$doctor = new Doctor($conn);

// ✅ Handle Appointment Approval/Decline
$apptMessage = '';
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['appt_id'], $_POST['appt_action'])) {
    $appt_id = $_POST['appt_id'];
    $action = $_POST['appt_action'];

    try {
        $updateQuery = "UPDATE appointments SET status = ? WHERE appointment_id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->execute([$action, $appt_id]);
        $apptMessage = "✅ Appointment #$appt_id has been " . htmlspecialchars($action) . ".";
    } catch (PDOException $e) {
        $apptMessage = "❌ Failed to update appointment: " . $e->getMessage();
    }
}

// ✅ Fetch All Appointments
$appointments = [];
try {
    $query = "SELECT * FROM appointments ORDER BY appointment_date DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}

// ✅ Display Appointment Table
function showAppointments($appointments) {
    echo "<div id='appointments' class='section'>";
    echo "<h2>📅 All Appointment List</h2>";

    if (empty($appointments)) {
        echo "<p>No appointments found.</p>";
    } else {
        echo '<table id="appttable" class="display">';
        echo "<thead>
                <tr>
                    <th>ID</th>
                    <th>Student ID</th>
                    <th>Nurse ID</th>
                    <th>Doctor ID</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Reason</th>
                    <th>Action</th>
                </tr>
              </thead><tbody>";

        foreach ($appointments as $appt) {
            $status = htmlspecialchars($appt['status']);
            $statusColor = match ($status) {
                'approved' => 'green',
                'declined' => 'red',
                default => 'orange'
            };

            echo "<tr>
                    <td>" . htmlspecialchars($appt['appointment_id']) . "</td>
                    <td>" . htmlspecialchars($appt['student_id']) . "</td>
                    <td>" . htmlspecialchars($appt['nurse_id']) . "</td>
                    <td>" . htmlspecialchars($appt['doctor_id']) . "</td>
                    <td>" . htmlspecialchars($appt['appointment_date']) . "</td>
                    <td style='color:$statusColor'>" . ucfirst($status) . "</td>
                    <td>" . htmlspecialchars($appt['reason']) . "</td>
                    <td>
                        <form method='post' style='display:inline'>
                            <input type='hidden' name='appt_id' value='" . $appt['appointment_id'] . "'>
                            <button name='appt_action' value='approved' onclick=\"return confirm('Approve this appointment?')\">✅</button>
                            <button name='appt_action' value='declined' onclick=\"return confirm('Decline this appointment?')\">❌</button>
                        </form>
                    </td>
                  </tr>";
        }

        echo "</tbody></table>";
    }

    echo "</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor - Appointment Management</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    
</head>
<body>

    <?php if ($apptMessage): ?>
        <p class="message"><?= $apptMessage ?></p>
    <?php endif; ?>

    <?php showAppointments($appointments); ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#appttable').DataTable();
        });
    </script>
</body>
</html>
