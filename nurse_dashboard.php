<?php
$conn = new mysqli("localhost", "root", "", "eclinic_scheduler");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission for patient log
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_name = $_POST['student_name'];
    $nurse_name = $_POST['nurse_name'];
    $log_details = $_POST['log_details'];
    $log_date = $_POST['log_date'];

    // Extract first and last names
    [$student_first, $student_last] = explode(' ', $student_name, 2) + ["", ""];
    [$nurse_first, $nurse_last] = explode(' ', $nurse_name, 2) + ["", ""];

    // Get user IDs from names
    $student_id = null;
    $nurse_id = null;

    $stmt1 = $conn->prepare("SELECT user_id FROM users WHERE first_name = ? AND last_name = ? AND role = 'student'");
    $stmt1->bind_param("ss", $student_first, $student_last);
    $stmt1->execute();
    $stmt1->bind_result($student_id);
    $stmt1->fetch();
    $stmt1->close();

    $stmt2 = $conn->prepare("SELECT user_id FROM users WHERE first_name = ? AND last_name = ? AND role = 'nurse'");
    $stmt2->bind_param("ss", $nurse_first, $nurse_last);
    $stmt2->execute();
    $stmt2->bind_result($nurse_id);
    $stmt2->fetch();
    $stmt2->close();

    if ($student_id && $nurse_id) {
        $stmt = $conn->prepare("INSERT INTO patient_logs (student_id, nurse_id, log_details, log_date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $student_id, $nurse_id, $log_details, $log_date);
        $stmt->execute();
    }
}

// Get patient logs
$logs = [];
$sql = "SELECT 
            p.first_name, p.last_name,
            n.first_name AS nurse_first_name, n.last_name AS nurse_last_name,
            l.log_details, l.log_date
        FROM patient_logs l
        JOIN users p ON l.student_id = p.user_id  
        JOIN users n ON l.nurse_id = n.user_id   
        ORDER BY l.log_date DESC";

$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $logs[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Patient Logs</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        form { margin-bottom: 30px; }
        label { display: block; margin: 10px 0 5px; }
        input, textarea { width: 100%; padding: 8px; }
        button { padding: 10px 15px; background-color: #4CAF50; color: white; border: none; cursor: pointer; margin-top: 10px; }
        button:hover { background-color: #45a049; }
    </style>
</head>
<body>

    <h2>Patient Log Form</h2>
    <form method="POST">
        <label for="student_name">Student Name (First Last):</label>
        <input type="text" name="student_name" id="student_name" required>

        <label for="nurse_name">Nurse Name (First Last):</label>
        <input type="text" name="nurse_name" id="nurse_name" required>

        <label for="log_details">Log Details:</label>
        <textarea name="log_details" id="log_details" rows="4" required></textarea>

        <label for="log_date">Log Date & Time:</label>
        <input type="datetime-local" name="log_date" id="log_date" required>

        <button type="submit">Submit Log</button>
    </form>

    <h2>Patient Logs</h2>
    <table>
        <tr>
            <th>Student</th>
            <th>Nurse</th>
            <th>Log Details</th>
            <th>Log Date</th>
        </tr>
        <?php if (count($logs) > 0): ?>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= htmlspecialchars($log['first_name'] . ' ' . $log['last_name']) ?></td>
                    <td><?= htmlspecialchars($log['nurse_first_name'] . ' ' . $log['nurse_last_name']) ?></td>
                    <td><?= htmlspecialchars($log['log_details']) ?></td>
                    <td><?= htmlspecialchars($log['log_date']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4">No patient logs found.</td></tr>
        <?php endif; ?>
    </table>

</body>
</html>
