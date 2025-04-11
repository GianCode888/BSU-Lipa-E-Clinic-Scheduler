<?php
include 'db.php';

$id = $_GET['id'];

$sql = "UPDATE medication_requests SET status='dispensed' WHERE request_id=$id";
$conn->query($sql);

header("Location: nurse_medication.php");
exit;
?>
