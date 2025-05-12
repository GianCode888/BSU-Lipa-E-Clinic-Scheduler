<?php
session_start();
require_once '../eclinic_database.php';

$database = new DatabaseConnection();
$conn = $database->getConnect();

$user_id = $_SESSION['user_id'];

class Student {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function request_appointment($user_id, $appointment_date, $appointment_time, $reason){
        $stmt = $this->conn->prepare("CALL RequestAppointment(:user_id, :appointment_date, :appointment_time, :reason)");
        $stmt->execute([':user_id'=>$user_id, ':appointment_date'=>$appointment_date, ':appointment_time'=>$appointment_time, ':reason'=>$reason]);
        $stmt->closeCursor();
        return true;
    }

    public function request_medication($user_id, $medication){
        $stmt = $this->conn->prepare("CALL RequestMedication(:user_id, :medication)");
        $stmt->execute([':user_id'=>$user_id, 'medication'=>$medication]);
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

    public function view_appointmentrequest($user_id) {
        $stmt = $this->conn->prepare("CALL ViewStudentAppointments(:userID)");
        $stmt->execute([':userID' => $user_id]);
        return $stmt; 
    }
    
    public function view_medicationrequest($user_id) {
        $stmt = $this->conn->prepare("CALL ViewStudentMedication(:userID)");
        $stmt->execute([':userID' => $user_id]);
        return $stmt;
    }

    public function view_medicalHistory($user_id) {
        $stmt = $this->conn->prepare("CALL ViewStudentMedHistory(:user_id)");
        $stmt->execute([':user_id' => $user_id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $results;
    }

    public function view_availableDoctorByDay($day_name) {
        $stmt = $this->conn->prepare("CALL ViewAvailableDoctorByDay(:i_day_name)");
        $stmt->execute([':i_day_name' => $day_name]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    public function view_requestStatus($user_id) {
        $stmt = $this->conn->prepare("CALL ViewRequestStatus(:userID)");
        $stmt->execute([':userID' => $user_id]);
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
    
    public function get_prescription($userId) {
        $stmt = $this->conn->prepare("CALL GetPrescription(?)");
        $stmt->execute([$userId]);
        $prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $prescriptions;
    }
     
}
?>