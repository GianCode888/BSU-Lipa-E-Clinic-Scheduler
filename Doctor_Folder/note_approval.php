<?php
require_once '../Doctor_Folder/doctor_serverside.php';
require_once '../eclinic_database.php';

if (!isset($_GET['request_id']) || !isset($_GET['request_type'])) {
    die("Invalid request.");
}

$request_id = $_GET['request_id'];
$request_type = $_GET['request_type'];

$database = new DatabaseConnection();
$conn = $database->getConnect();
$doctor = new Doctor($conn);
$request_details = $doctor->get_request_details($request_id, $request_type);

if (!$request_details) {
    die("Request not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Approval Notes</title>
</head>
<body>

    <h2>Add Approval Notes</h2>

    <h3>Student Details</h3>
    <p><strong>Student Name:</strong> <?php echo htmlspecialchars($request_details['student_name']); ?></p>

    <?php if ($request_type === 'Appointment'): ?>
        <p><strong>Appointment Date:</strong> <?php echo htmlspecialchars($request_details['appointment_date']); ?></p>
        <p><strong>Appointment Time:</strong> <?php echo htmlspecialchars($request_details['appointment_time']); ?></p>
        <p><strong>Reason:</strong> <?php echo htmlspecialchars($request_details['reason']); ?></p>
    <?php elseif ($request_type === 'Medication'): ?>
        <p><strong>Request Date:</strong> <?php echo htmlspecialchars($request_details['request_date']); ?></p>
        <p><strong>Medication Requested:</strong> <?php echo htmlspecialchars($request_details['medication']); ?></p>
    <?php endif; ?>

    <form method="POST" action="doctor_serverside.php">
        <label for="approval_notes">Approval Notes:</label><br>
        <textarea name="approval_notes" id="approval_notes" rows="4" cols="50" required></textarea><br><br>

        <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($request_id); ?>">
        <input type="hidden" name="request_type" value="<?php echo htmlspecialchars($request_type); ?>">
        <input type="hidden" name="action" value="submit_approval_notes">

        <button type="submit">Submit Approval Notes</button>
    </form>

</body>
</html>
