<?php
include '../Doctor_Folder/doctor_crud.php';
require_once '../eclinic_database.php';

// Assume doctor is logged in
$doctor_id = $_SESSION['doctor_id'];

$database = new DatabaseConnection();
$conn = $database->getConnect();
$doctor = new Doctor($conn);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $available_day = $_POST['available_day'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    $existing_availability = $doctor->get_availability_for_day($doctor_id, $available_day);

    if ($existing_availability) {
        $doctor->update_availability($doctor_id, $available_day, $start_time, $end_time);
    } else {
        $doctor->add_availability($doctor_id, $available_day, $start_time, $end_time);
    }

    header("Location: availability_form.php"); // Redirect to prevent form resubmission
    exit();
}

$availability_list = $doctor->get_all_availability($doctor_id);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Doctor Availability</title>
</head>
<body>
    <h2>Set Your Availability</h2>
    <form method="POST" action="">
        <label>Available Day:</label>
        <select name="available_day" required>
            <option>Monday</option>
            <option>Tuesday</option>
            <option>Wednesday</option>
            <option>Thursday</option>
            <option>Friday</option>
        </select>

        <label>Start Time:</label>
        <input type="time" name="start_time" required>

        <label>End Time:</label>
        <input type="time" name="end_time" required>

        <button type="submit" name="submit">Add/Update Availability</button>
    </form>

    <h3>Your Current Availability</h3>
    <?php if (!empty($availability_list)): ?>
        <table border="1" cellpadding="8">
            <tr>
                <th>Day</th>
                <th>Start Time</th>
                <th>End Time</th>
            </tr>
            <?php foreach ($availability_list as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['available_day']); ?></td>
                    <td><?php echo htmlspecialchars($row['start_time']); ?></td>
                    <td><?php echo htmlspecialchars($row['end_time']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No availability set yet.</p>
    <?php endif; ?>
</body>
</html>
