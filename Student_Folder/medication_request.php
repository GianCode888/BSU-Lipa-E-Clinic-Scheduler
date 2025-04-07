<?php
session_start();
include '../eclinic_database.php';

$database = new DatabaseConnection();     
$conn = $database->getConnect();

$student_id = $_SESSION['user_id'];
$request_details = $_POST['medication'];

$sql = "INSERT INTO medication_requests (student_id, medication) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bindparam(1, $student_id);
$stmt->bindparam(2, $request_details);


if ($stmt->execute()) {
    echo "Medication request submitted successfully.";
} else {
    echo "Error: " . $conn->errorInfo()[2];
}
?>
