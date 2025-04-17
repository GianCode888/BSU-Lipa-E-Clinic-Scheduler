<?php
require 'db_connection.php';
require 'doctor_crud.php';

$doctor = new Doctor($conn);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_medication'])) {
    $doctor->update_medication_request($_POST['request_id'], $_POST['status']);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM medication_requests WHERE status = 'Pending'");
$stmt->execute();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo '<table border="1">
<tr>
    <th>Student Name</th>
    <th>Medication</th>
    <th>Status</th>
    <th>Action</th>
</tr>';

foreach ($requests as $request) {
    echo '<tr>
        <td>' . htmlspecialchars($request['student_name']) . '</td>
        <td>' . htmlspecialchars($request['medication']) . '</td>
        <td>' . htmlspecialchars($request['status']) . '</td>
        <td>
            <form method="POST" action="view_medication_requests.php">
                <input type="hidden" name="request_id" value="' . $request['request_id'] . '">
                <select name="status" required>
                    <option value="">Select</option>
                    <option value="Approved">Approve</option>
                    <option value="Declined">Decline</option>
                </select><br>
                <button type="submit" name="update_medication">Submit</button>
            </form>
        </td>
    </tr>';
}
echo '</table>';
?>
