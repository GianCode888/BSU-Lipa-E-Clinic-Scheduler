<?php
$conn = new mysqli("localhost", "root", "", "eclinic_scheduler");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student = $_POST['student'];
    $nurse = $_POST['nurse'];
    $details = $_POST['details'];
    $date = $_POST['date'];

    $stmt = $conn->prepare("INSERT INTO completed_requests (student_name, nurse_name, details, completed_date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $student, $nurse, $details, $date);
    
    if ($stmt->execute()) {
        echo "Log submitted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manual Patient Log Form</title>
</head>
<body>
    <h2>Enter Patient Log</h2>
    <form method="POST">
        <label>Student Name:</label><br>
        <input type="text" name="student" required><br><br>

        <label>Nurse Name:</label><br>
        <input type="text" name="nurse" required><br><br>

        <label>Details:</label><br>
        <textarea name="details" required></textarea><br><br>

        <label>Date:</label><br>
        <input type="datetime-local" name="date" required><br><br>

        <input type="submit" value="Submit Log">
    </form>
</body>
</html>
