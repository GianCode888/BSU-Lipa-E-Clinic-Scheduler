<?php
class NurseManager {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function getNurseInfo($nurse_id) {
        $stmt = $this->conn->prepare("CALL GetNurseInfo(:p_nurse_id)");
        $stmt->execute(['p_nurse_id' => $nurse_id]);
        
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
    
    // Added missing function
    public function getMedicationRequestDetails($request_id) {
        $stmt = $this->conn->prepare("CALL GetMedicationRequestDetails(?)");
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
    
    public function getCompletedRequests() {
        $stmt = $this->conn->prepare("CALL GetCompletedRequests()");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    
    
    
    
    

    
    public function dispenseMedication($request_id, $nurse_id, $notes = '') {
        try {
            // Call the stored procedure to dispense medication
            $stmt = $this->conn->prepare("CALL DispenseMedication(?, ?, ?)");
            
            if (!$stmt) {
                return "Failed to prepare statement: " . $this->conn->error;
            }
            
            $result = $stmt->execute([$request_id, $nurse_id, $notes]);
            
            if ($result === false) {
                return "Failed to execute stored procedure: " . $stmt->errorInfo()[2];
            }
            
            return true;
        } catch (PDOException $e) {
            return "Database error: " . $e->getMessage();
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

}
?>