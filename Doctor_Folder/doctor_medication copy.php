<?php
require '../eclinic_database.php';

$db = new DatabaseConnection();
$conn = $db->getConnect();

try {
    $stmt = $conn->prepare("SELECT * FROM doctor_medications ORDER BY date_prescribed DESC");
    $stmt->execute();
    $medications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching medications: " . $e->getMessage());
}

// Make data available to HTML
include 'doctor_medication.html';
?>