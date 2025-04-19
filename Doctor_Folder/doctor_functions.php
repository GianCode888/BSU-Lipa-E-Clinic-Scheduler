<?php
function showScheduleForm($conn) {
    $doctorId = $_SESSION['user_id'];

    echo "<div id='schedule' class='section'>
            <h2>🕒 Doctor's Weekly Availability</h2>";

    // Fetch availability from DB
    $query = "SELECT available_day, start_time, end_time 
              FROM availability 
              WHERE doctor_id = ? 
              ORDER BY FIELD(available_day, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'), start_time";
    $stmt = $conn->prepare($query);
    $stmt->execute([$doctorId]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
