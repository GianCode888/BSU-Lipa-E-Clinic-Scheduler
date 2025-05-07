<?php 
class NurseManager {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function getNurseInfo($nurse_id) {
        try {
            $stmt = $this->conn->prepare("CALL GetNurseInfo(?)");
            $stmt->execute([$nurse_id]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getNurseInfo: " . $e->getMessage());
            return [];
        }
    }
    
    public function countPendingDispensing() {
        try {
            $stmt = $this->conn->prepare("CALL CountPendingDispensing()");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            error_log("Error in countPendingDispensing: " . $e->getMessage());
            return 0;
        }
    }
    
    
    public function getMedicationRequests($status = 'pending') {
        try {
            $stmt = $this->conn->prepare("CALL GetMedicationRequests(?)");
            $stmt->execute([$status]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getMedicationRequests: " . $e->getMessage());
            return [];
        }
    }
    public function getMedicationRequestDetails($request_id) {
        try {
            $stmt = $this->conn->prepare("CALL GetMedicationRequestDetails(?)");
            $stmt->execute([$request_id]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getMedicationRequestDetails: " . $e->getMessage());
            return [];
        }
    }

    public function updateMedicationStatus($request_id, $new_status, $nurse_id) {
        try {
            $stmt = $this->conn->prepare("CALL UpdateMedicationStatus(?, ?, ?)");
            return $stmt->execute([$request_id, $new_status, $nurse_id]);
        } catch (PDOException $e) {
            error_log("Error in updateMedicationStatus: " . $e->getMessage());
            return false;
        }
    }

    public function student_appointment_request() {
        $stmt = $this->conn->prepare("CALL StudentAppointmentRequest()");
        $stmt->execute();
        return $stmt;
    }

    public function get_all_approved_requests() {
        $stmt = $this->conn->prepare("CALL GetAllApprovedRequests()");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getPatientLogs() {
        try {
            $stmt = $this->conn->prepare("CALL GetPatientLogs()");
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getPatientLogs: " . $e->getMessage());
            return [];
        }
    }
    
    // Insert patient log using stored procedure
    public function addPatientLog($student_name, $contact, $address, $nurse_name, $log_details) {
        $stmt = $this->conn->prepare("CALL AddPatientLog(?, ?, ?, ?, ?)");
        return $stmt->execute([$student_name, $contact, $address, $nurse_name, $log_details]);
    }
    
    
    public function deleteCompletedRequest($log_id) {
        $stmt = $this->conn->prepare("CALL DeleteCompletedRequest(?)");
        $stmt->execute([$log_id]);
    
        // Check how many rows were affected by the DELETE operation
        if ($stmt->rowCount() > 0) {
            return true;  // Successfully deleted the log
        } else {
            return false;  // No rows affected, meaning no log found with that ID
        }
    }
    public function getCompletedRequests() {
        try {
            $stmt = $this->conn->prepare("CALL GetCompletedRequests()");
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getCompletedRequests: " . $e->getMessage());
            return [];
        }
    }
}
?>