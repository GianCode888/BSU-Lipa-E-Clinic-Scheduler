<?php
require '../eclinic_database.php';

$db = new DatabaseConnection();
$conn = $db->getConnect();

try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE");
    $stmt->execute();
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching student records: " . $e->getMessage());
}

// Make data available to HTML
include 'student_record.html';
?>
