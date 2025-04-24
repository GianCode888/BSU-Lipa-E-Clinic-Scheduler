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
        $stmt->closeCursor();
        return true;
    }

    public function request_medication($student_id, $medication){
        $stmt = $this->conn->prepare("CALL RequestMedication(:student_id, :medication)");
        $stmt->execute([':student_id'=>$student_id, 'medication'=>$medication]);
        $stmt->closeCursor();
        return true;
    }

    public function delete_appointment($appointment_id) {
        $stmt = $this->conn->prepare("CALL DeleteAppointment(:appointmentID)");
        $stmt->execute([':appointmentID' => $appointment_id]);
        $stmt->closeCursor();
        return true;
    }

    public function delete_medication($medication_id) {
        $stmt = $this->conn->prepare("CALL DeleteMedication(:medicationID)");
        $stmt->execute([':medicationID' => $medication_id]);
        $stmt->closeCursor();
        return true;
    }

    public function view_appointmentrequest($student_id) {
        $stmt = $this->conn->prepare("CALL ViewStudentAppointments(:studentID)");
        $stmt->execute([':studentID' => $student_id]);
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
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $results;
    }

    public function view_availableDoctor($available_day) {
        $stmt = $this->conn->prepare("CALL ViewAvailableDoctor(:available_day)");
        $stmt->execute([':available_day' => $available_day]);
        return $stmt;
    }

    public function view_appointmentStatus($student_id) {
        $stmt = $this->conn->prepare("CALL ViewAppointmentStatus(:student_id)");
        $stmt->execute([':student_id' => $student_id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $results;
    }

    public function getUserDetails($userId) {
        $stmt = $this->conn->prepare("CALL GetUserDetails(:uid)");
        $stmt->execute([':uid' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }

    public function addMedicalInfo($user_id, $blood_type, $allergies, $med_condition, $medications_taken, $emergency_contact_name, $relationship, $contact_number, $address) {
        $stmt = $this->conn->prepare("CALL InsertStudentMedicalInfo(?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $blood_type, $allergies, $med_condition, $medications_taken, $emergency_contact_name, $relationship, $contact_number, $address]);
        $stmt->closeCursor();
        return true;
    }

    public function getMedicalInfo($user_id) {
        $stmt = $this->conn->prepare("CALL GetMedicalInfoByUser(?)");
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor(); 
        return $result;
    }

    public function updateMedicalInfo($user_id, $blood_type, $allergies, $med_condition, $medications_taken, $emergency_contact_name, $relationship, $contact_number, $address) {
        $stmt = $this->conn->prepare("CALL UpdateStudentMedicalInfo(?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $blood_type, $allergies, $med_condition, $medications_taken, $emergency_contact_name, $relationship, $contact_number, $address]);
        $stmt->closeCursor();
        return true;
    }    
}
?>
