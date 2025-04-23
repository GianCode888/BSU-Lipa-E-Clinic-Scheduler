<?php
require_once '../eclinic_database.php';

class MedicalInfo {
    private $conn;

    public function __construct() {
        $database = new DatabaseConnection();
        $this->conn = $database->getConnect();
    }

    public function addMedicalInfo($user_id, $blood_type, $allergies, $med_condition, $medications_taken, $emergency_contact_name, $relationship, $contact_number, $address) {
        $stmt = $this->conn->prepare("CALL InsertStudentMedicalInfo(?, ?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$user_id, $blood_type, $allergies, $med_condition, $medications_taken, $emergency_contact_name, $relationship, $contact_number, $address]);
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
        return $stmt->execute([$user_id, $blood_type, $allergies, $med_condition, $medications_taken, $emergency_contact_name, $relationship, $contact_number, $address]);
    }
}
?>
