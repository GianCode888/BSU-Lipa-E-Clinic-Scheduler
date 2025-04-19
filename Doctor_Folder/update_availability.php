<?php
session_start();
require_once '../eclinic_database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$availabilityId = $_GET['id'] ?? '';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Availability</title>
</head>
<body>

<h2>Update Day for Availability</h2>

<form action="doctor_crud.php" method="POST">
    <input type="hidden" name="action" value="update_availability">
    <input type="hidden" name="availability_id" value="<?php echo htmlspecialchars($availabilityId); ?>">

    <label>Available Day:</label>
    <select name="available_day" required>
        <option value="">Choose...</option>
        <?php
        $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
        foreach ($days as $day) {
            echo "<option value=\"$day\">$day</option>";
        }
        ?>
    </select>

    <button type="submit">Update</button>
</form>

</body>
</html>
