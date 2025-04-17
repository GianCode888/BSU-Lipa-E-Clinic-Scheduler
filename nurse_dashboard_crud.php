<?php
class NurseManager {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function getNurseInfo($nurse_id) {
        $stmt = $this->conn->prepare("CALL GetNurseInfo(?)");
        $stmt->execute([$nurse_id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function countPendingDispensing() {
        $stmt = $this->conn->prepare("CALL CountPendingDispensing()");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'];
    }
    
    public function countTodayAppointments($nurse_id) {
        $today = date('Y-m-d');
        $stmt = $this->conn->prepare("CALL CountTodayAppointments(?, ?)");
        $stmt->execute([$today, $nurse_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'];
    }
    
    public function getRecentActivity($nurse_id) {
        $stmt = $this->conn->prepare("CALL GetRecentActivity()");
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function updateAppointmentStatus($appointment_id, $new_status, $nurse_id) {
        $stmt = $this->conn->prepare("CALL UpdateAppointmentStatus(?, ?, ?)");
        
        return $stmt->execute([$appointment_id, $new_status, $nurse_id]);
    }
    
    public function getAppointmentDetails($appointment_id) {
        $stmt = $this->conn->prepare("CALL GetAppointmentDetails(?)");
        $stmt->execute([$appointment_id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getMedicationRequests($status = 'pending') {
        $stmt = $this->conn->prepare("CALL GetMedicationRequests(?)");
        $stmt->execute([$status]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get details for a specific medication request without using a stored procedure
    public function getMedicationRequestDetails($request_id) {
        $query = "SELECT mr.*, s.student_id, s.first_name, s.last_name, m.medication_name, m.dosage 
                 FROM medication_requests mr
                 JOIN students s ON mr.student_id = s.student_id 
                 JOIN medications m ON mr.medication_id = m.medication_id
                 WHERE mr.request_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$request_id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function updateMedicationStatus($request_id, $new_status, $nurse_id) {
        $stmt = $this->conn->prepare("CALL UpdateMedicationStatus(?, ?, ?)");
        
        return $stmt->execute([$request_id, $new_status, $nurse_id]);
    }
    
    public function getCompletedMedications() {
        $stmt = $this->conn->prepare("CALL GetCompletedMedications()");
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getPatientLogs() {
        $stmt = $this->conn->prepare("CALL GetPatientLogs()");
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function addPatientLog($student_id, $nurse_id, $log_details) {
        $stmt = $this->conn->prepare("CALL AddPatientLog(?, ?, ?)");
        
        return $stmt->execute([$student_id, $nurse_id, $log_details]);
    }
}
?>