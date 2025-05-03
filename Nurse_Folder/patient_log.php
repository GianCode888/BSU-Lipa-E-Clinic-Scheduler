<?php
include '../eclinic_database.php';
include 'nurse_dashboard_crud.php';

$database = new DatabaseConnection();
$db = $database->getConnect();
$nurseManager = new NurseManager($db);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_name = $_POST['student_name'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];
    $nurse_name = $_POST['nurse_name'];
    $log_details = $_POST['log_details'];

    if ($nurseManager->addPatientLog($student_name, $contact, $address, $nurse_name, $log_details)) {
        echo "<script>alert('Log added successfully!'); window.location.href='patient_log.php';</script>";
    } else {
        echo "<script>alert('Failed to add log.');</script>";
    }
}

// Fetch completed logs
$completedLogs = $nurseManager->getCompletedRequests();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Log</title>
    <style>
        form {
            margin-bottom: 30px;
        }
        input, textarea {
            display: block;
            width: 300px;
            margin-bottom: 10px;
            padding: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        th, td {
            border: 1px solid #aaa;
            padding: 10px;
        }
        th {
            background-color: #eee;
        }
    </style>
</head>
<body>

<h2>Add Patient Log</h2>

<form method="POST">
    <input type="text" name="student_name" placeholder="Student Name" required>
    <input type="text" name="contact" placeholder="Contact Number" required>
    <input type="text" name="address" placeholder="Address" required>
    <input type="text" name="nurse_name" placeholder="Nurse Name" required>
    <textarea name="log_details" placeholder="Log Details" rows="4" required></textarea>
    <button type="submit">Submit</button>
    <a href="../nurse_dashboard.php" class="btn">Back to Dashboard</a>
</form>


</html>
