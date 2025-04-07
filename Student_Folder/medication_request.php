<?php
session_start();
include '../eclinic_database.php';

$student_id = $_SESSION['user_id'];
$request_details = $_POST['medication'];

$sql = "INSERT INTO medication_requests (student_id, medication) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $student_id, $request_details);

if ($stmt->execute()) {
    echo "Medication request submitted successfully.";
} else {
    echo "Error: " . $conn->error;
}
?>
