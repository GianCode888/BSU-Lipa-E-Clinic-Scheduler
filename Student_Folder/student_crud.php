<?php
session_start();
require_once '../eclinic_database.php';

$database = new DatabaseConnection();
$conn = $database->getConnect();

$student_id = $_SESSION['user_id'];

class Student {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function request_appointment($student_id, $appointment_date, $appointment_time, $reason){
        $stmt = $this->conn->prepare("CALL RequestAppointment(:student_id, :appointment_date, :appointment_time, :reason)");
        $stmt->execute([':student_id'=>$student_id, ':appointment_date'=>$appointment_date, ':appointment_time'=>$appointment_time, ':reason'=>$reason]);
        return $stmt;
    }

    public function request_medication($student_id, $medication){
        $stmt = $this->conn->prepare("CALL RequestMedication(:student_id, :medication)");
        $stmt->execute([':student_id'=>$student_id, 'medication'=>$medication]);
        return $stmt;
    }

    public function delete_appointment($appointment_id) {
        $stmt = $this->conn->prepare("CALL DeleteAppointment(:appointmentID)");
        $stmt->execute([':appointmentID' => $appointment_id]);
        return $stmt;
    }

    public function delete_medication($medication_id) {
        $stmt = $this->conn->prepare("CALL DeleteMedication(:medicationID)");
        $stmt->execute([':medicationID' => $medication_id]);
        return $stmt;
    }

    public function view_appointmentrequest($student_id) {
        $stmt = $this->conn->prepare("CALL ViewStudentAppointments(:appointmentID)");
        $stmt->execute([':appointmentID' => $student_id]);
        return $stmt;
    }

    public function view_medicationrequest($student_id) {
        $stmt = $this->conn->prepare("CALL ViewStudentMedication(:medicationID)");
        $stmt->execute([':medicationID' => $student_id]);
        return $stmt;
    }

    public function view_medicalHistory($student_id) {
        $stmt = $this->conn->prepare("CALL ViewStudentMedHistory(:student_id)");
        $stmt->execute([':student_id' => $student_id]);
        return $stmt;
    }    

    public function view_availableDoctor($available_day) {
        $stmt = $this->conn->prepare("CALL ViewAvailableDoctor(:available_day)");
        $stmt->execute([':available_day' => $available_day]);
        return $stmt;
    }
    
    public function view_appointmentStatus($student_id) {
        $stmt = $this->conn->prepare("CALL ViewAppointmentStatus(:student_id)");
        $stmt->execute([':student_id' => $student_id]);
        return $stmt;
    }

    public function getUserDetails($userId) {
        $stmt = $this->conn->prepare("CALL GetUserDetails(:uid)");
        $stmt->execute([':uid' => $userId]);
        return $stmt;
    }
    

    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $student = new Student($conn);
    
        if (isset($_POST['request_type'])) {
            
            if ($_POST['request_type'] === 'appointment') {
                if (isset($_POST['appointment_date'], $_POST['appointment_time'], $_POST['reason'])) {
                    $appointment_date = $_POST['appointment_date'];
                    $appointment_time = $_POST['appointment_time'];
                    $reason = $_POST['reason'];
    
                    $student->request_appointment($student_id, $appointment_date, $appointment_time, $reason);
                    header("Location: ../student_dashboard.php");
                    exit;
                }
            }
    
            elseif ($_POST['request_type'] === 'medication') {
                if (isset($_POST['medication'])) {
                    $request_details = $_POST['medication'];
    
                    $student->request_medication($student_id, $request_details);
                    header("Location: ../student_dashboard.php");
                    exit;
                }
            }
        }

        if (isset($_POST['delete'])) {
            if (isset($_POST['appointment_id'])) {
                $student->delete_appointment($_POST['appointment_id']);
                header("Location: view_appointment.php");
                exit;
            } elseif (isset($_POST['medication_id'])) {
                $student->delete_medication($_POST['medication_id']);
                header("Location: view_medication.php");
                exit;
            }
        } 
    }
 
?>
