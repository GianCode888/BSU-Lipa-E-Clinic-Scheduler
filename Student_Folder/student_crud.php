<?php
session_start();
include '../eclinic_database.php';

$database = new DatabaseConnection();
$conn = $database->getConnect();

$student_id = $_SESSION['user_id'];

class Student {
    private $conn;
    private $appointments_table = "appointments";
    private $medication_requests_table = "medication_requests";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function request_appointment($student_id, $appointment_date, $appointment_time, $reason) {
        $sql = "INSERT INTO " . $this->appointments_table . " (student_id, appointment_date, appointment_time, status, reason, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(1, $student_id);
        $stmt->bindParam(2, $appointment_date);
        $stmt->bindParam(3, $appointment_time);
        $status = 'Pending';
        $stmt->bindParam(4, $status);
        $stmt->bindParam(5, $reason);

        if ($stmt->execute()) {
            echo "Appointment request submitted!";
        } else {
            echo "Error: " . $this->conn->errorInfo()[2];
        }
    }



    public function medical_request($student_id, $request_details) {
        $sql = "INSERT INTO " . $this->medication_requests_table . " (student_id, medication) 
                VALUES (?, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(1, $student_id);
        $stmt->bindParam(2, $request_details);

        if ($stmt->execute()) {
            echo "Medication request submitted successfully.";
        } else {
            echo "Error: " . $this->conn->errorInfo()[2];
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student = new Student($conn);

    if (isset($_POST['request_type']) && $_POST['request_type'] === 'appointment') {
        $appointment_date = $_POST['date']; 
        $appointment_time = $_POST['time']; 
        $reason = $_POST['reason'];
        $student->request_appointment($student_id, $appointment_date, $appointment_time, $reason);
    }

    elseif (isset($_POST['request_type']) && $_POST['request_type'] === 'medication') {
        $request_details = $_POST['medication'];
        $student->medical_request($student_id, $request_details);
    }
}
?>
