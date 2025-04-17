<?php
session_start();
include '.eclinic_database.php';

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
        $sql = "INSERT INTO " . $this->appointments_table . " 
                (student_id, appointment_date, appointment_time, status, reason, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())";
    
        $stmt = $this->conn->prepare($sql);
    
        $status = 'pending';
        $stmt->bindParam(1, $student_id);
        $stmt->bindParam(2, $appointment_date);
        $stmt->bindParam(3, $appointment_time);
        $stmt->bindValue(4, $status); 
        $stmt->bindParam(5, $reason);
    
        if ($stmt->execute()) {
            header ("Location: ../student_dashboard.php");
        } else {
            return "Error: " . implode(" | ", $stmt->errorInfo());
        }
    }
    

    public function medical_request($student_id, $request_details) {
        $sql = "INSERT INTO " . $this->medication_requests_table . " (student_id, medication) 
                VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(1, $student_id);
        $stmt->bindParam(2, $request_details);

        if ($stmt->execute()) {
            header ("Location: ../student_dashboard.php");
        } else {
            return "Error: " . implode(" | ", $stmt->errorInfo());
        }
    }

    public function delete_appointment($appointment_id) {
        $sql = "DELETE FROM appointments WHERE appointment_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$appointment_id]);
    }

}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student = new Student($conn);

    if (isset($_POST['request_type'])) {
        if ($_POST['request_type'] === 'appointment') {
            if (isset($_POST['appointment_date'], $_POST['appointment_time'], $_POST['reason'])) {
                $appointment_date = $_POST['appointment_date']; 
                $appointment_time = $_POST['appointment_time']; 
                $reason = $_POST['reason'];
                echo $student->request_appointment($student_id, $appointment_date, $appointment_time, $reason);
            }
        } elseif ($_POST['request_type'] === 'medication') {
            if (isset($_POST['medication'])) {
                $request_details = $_POST['medication'];
                $student->medical_request($student_id, $request_details);
            }
        }
    } elseif (isset($_POST['delete'])) {
        if (isset($_POST['appointment_id'])) {
            $student->delete_appointment($_POST['appointment_id']);
        }
    }
}
?>
