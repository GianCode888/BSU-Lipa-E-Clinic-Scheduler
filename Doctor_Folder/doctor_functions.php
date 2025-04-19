<?php
function showScheduleForm($conn) {
    $doctorId = $_SESSION['user_id'];

    echo "<div id='schedule' class='section'>
            <h2>🕒 Doctor's Weekly Availability</h2>";

    // Use stored procedure
    $stmt = $conn->prepare("CALL GetDoctorAvailability(?)");
    $stmt->execute([$doctorId]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    $availability = [];
    foreach ($results as $row) {
        $day = $row['available_day'];
        $timeRange = date('h:i A', strtotime($row['start_time'])) . ' - ' . date('h:i A', strtotime($row['end_time']));
        $availability[$day][] = $timeRange;
    }

    $daysOfWeek = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];

    echo "<table class='schedule-table'>
            <thead><tr><th>Day</th><th>Available Time</th></tr></thead><tbody>";
    foreach ($daysOfWeek as $day) {
        $slots = $availability[$day] ?? ['-- Not Available --'];
        echo "<tr><td>$day</td><td>" . implode('<br>', $slots) . "</td></tr>";
    }
    echo "</tbody></table>";

    // 🔽 Add Availability Form
    echo '<h3>Add New Availability</h3>
          <form method="POST" action="add_availability.php">
            <label>Day:
                <select name="available_day" required>
                    <option value="">Choose...</option>';
    foreach ($daysOfWeek as $day) {
        echo "<option value=\"$day\">$day</option>";
    }
    echo '      </select>
            </label>
            <label>Start Time: <input type="time" name="start_time" required></label>
            <label>End Time: <input type="time" name="end_time" required></label>
            <input type="hidden" name="doctor_id" value="' . htmlspecialchars($doctorId) . '">
            <button type="submit">Add Availability</button>
          </form>
        </div>';
}
?>
