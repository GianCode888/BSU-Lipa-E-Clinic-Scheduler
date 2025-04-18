<?php
session_start();
include 'eclinic_database.php';

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
    }

    public function request_medication($student_id, $medication){
        $stmt = $this->conn->prepare("CALL RequestMedication(:student_id, :medication)");
        $stmt->execute([':student_id'=>$student_id, 'medication'=>$medication]);
    }

    public function delete_appointment($appointment_id) {
        $stmt = $this->conn->prepare("CALL DeleteAppointment(:appointmentID)");
        $stmt->execute(['appointmentID' => $appointment_id]);
    }

    public function delete_medication($request_id) {
        $stmt = $this->conn->prepare("CALL DeleteMedication(:medicationID)");
        $stmt->execute(['medicationID' => $request_id]);
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
            } elseif (isset($_POST['request_id'])) {
                $student->delete_medication($_POST['request_id']);
                header("Location: view_medication.php");
                exit;
            }
        } 
    }
    
?>
