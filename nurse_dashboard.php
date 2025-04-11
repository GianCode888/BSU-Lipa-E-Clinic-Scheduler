<?php
session_start();
include 'db.php';
//sample id
$nurse_id = 1;

// Count of pending appointments
$pendingAppointments = $conn->query("SELECT COUNT(*) AS total FROM appointments WHERE status='pending'")->fetch_assoc()['total'];

// Count of approved medication requests
$approvedMeds = $conn->query("SELECT COUNT(*) AS total FROM medication_requests WHERE status='approved'")->fetch_assoc()['total'];

// Count of logs entered by this nurse (nurse uses doctor_id field)
$logsHandled = $conn->query("SELECT COUNT(*) AS total FROM medical_history WHERE doctor_id=$nurse_id")->fetch_assoc()['total'];

// Nurse details using prepared statement (secure)
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $nurse_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $nurseData = $result->fetch_assoc();
} else {
    $nurseData = [
        'first_name' => 'Unknown',
        'last_name' => 'User',
        'email' => 'Not Available'
    ];
}

?>

<h2>Nurse Dashboard</h2>

<p><strong>Welcome, <?= $nurseData['first_name'] . ' ' . $nurseData['last_name'] ?>!</strong></p>
<p>Email: <?= $nurseData['email'] ?></p>

<hr>

<h3>Overview</h3>
<ul>
    <li>Pending Appointments: <strong><?= $pendingAppointments ?></strong></li>
    <li>Approved Medication Requests: <strong><?= $approvedMeds ?></strong></li>
    <li>Medical Logs You've Handled: <strong><?= $logsHandled ?></strong></li>
</ul>

<hr>

<h3>Quick Links</h3>
<ul>
    <li><a href="nurse_appointments.php">View Appointment Queue</a></li>
    <li><a href="nurse_medication.php">Manage Medication Requests</a></li>
    <li><a href="nurse_logs.php">Add Medical Log</a></li>
</ul>
