<?php
require_once '../eclinic_database.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$database = new DatabaseConnection();
$conn = $database->getConnect();

class StudentMedicalRecords {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function fetchStudentRecords() {
        try {
            $stmt = $this->conn->prepare("
                SELECT mh.*, s.fullname AS student_name
                FROM medical_history mh
                JOIN students s ON mh.student_id = s.student_id
                ORDER BY mh.date_recorded DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error fetching student records: " . $e->getMessage());
        }
    }
}

$studentRecords = new StudentMedicalRecords($conn);
$records = $studentRecords->fetchStudentRecords();

include 'student_record.html';
?>
