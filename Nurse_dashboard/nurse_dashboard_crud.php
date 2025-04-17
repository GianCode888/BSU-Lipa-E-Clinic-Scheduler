<?php
class NurseManager {
    private $conn;
    private $users_table = "users";
    private $appointments_table = "appointments";
    private $medication_requests_table = "medication_requests";
    private $patient_logs_table = "patient_logs";
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Get nurse information
     */
    public function getNurseInfo($nurse_id) {
        try {
            $query = "SELECT first_name, last_name FROM " . $this->users_table . " WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $nurse_id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['first_name' => 'Unknown', 'last_name' => 'User'];
        }
    }
    
    /**
     * Count medication requests pending dispensing
     */
    public function countPendingDispensing() {
        try {
            $query = "SELECT COUNT(*) FROM " . $this->medication_requests_table . " WHERE status = 'approved'";
            $stmt = $this->conn->query($query);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }
    
    /**
     * Count today's appointments for a nurse
     */
    public function countTodayAppointments($nurse_id) {
        try {
            $today = date('Y-m-d');
            $query = "SELECT COUNT(*) FROM " . $this->appointments_table . " 
                      WHERE nurse_id = :nurse_id AND DATE(appointment_date) = :today";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':nurse_id', $nurse_id);
            $stmt->bindParam(':today', $today);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }
    
    /**
     * Get recent appointments for a nurse
     */
    public function getRecentActivity($nurse_id) {
        try {
            $query = "SELECT a.appointment_id, a.reason, a.status, a.appointment_date, 
                      u.first_name, u.last_name 
                      FROM " . $this->appointments_table . " a
                      JOIN " . $this->users_table . " u ON a.student_id = u.user_id
                      WHERE a.nurse_id = :nurse_id
                      ORDER BY a.appointment_date DESC LIMIT 10";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':nurse_id', $nurse_id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Update appointment status
     */
    public function updateAppointmentStatus($appointment_id, $new_status, $nurse_id) {
        try {
            // Validate status
            $valid_statuses = ['pending', 'approved', 'rejected', 'completed'];
            if (!in_array($new_status, $valid_statuses)) {
                return "Invalid status provided.";
            }
            
            // Check if appointment exists and nurse has permission
            $check_query = "SELECT COUNT(*) FROM " . $this->appointments_table . " 
                           WHERE appointment_id = :appointment_id 
                           AND (nurse_id = :nurse_id OR nurse_id IS NULL)";
            $check_stmt = $this->conn->prepare($check_query);
            $check_stmt->bindParam(':appointment_id', $appointment_id);
            $check_stmt->bindParam(':nurse_id', $nurse_id);
            $check_stmt->execute();
            
            if ($check_stmt->fetchColumn() == 0) {
                return "You don't have permission to update this appointment or it doesn't exist.";
            }
            
            // Update the appointment status
            $query = "UPDATE " . $this->appointments_table . " 
                      SET status = :status, 
                          nurse_id = :nurse_id,
                          updated_at = NOW()
                      WHERE appointment_id = :appointment_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':status', $new_status);
            $stmt->bindParam(':nurse_id', $nurse_id);
            $stmt->bindParam(':appointment_id', $appointment_id);
            $stmt->execute();
            
            return true;
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }
    
    /**
     * Dispense medication
     */
    public function dispenseMedication($request_id, $nurse_id, $notes = '') {
        try {
            // Check if medication request exists and is approved
            $check_query = "SELECT COUNT(*) FROM " . $this->medication_requests_table . " 
                           WHERE request_id = :request_id AND status = 'approved'";
            $check_stmt = $this->conn->prepare($check_query);
            $check_stmt->bindParam(':request_id', $request_id);
            $check_stmt->execute();
            
            if ($check_stmt->fetchColumn() == 0) {
                return "Medication request not found or not approved for dispensing.";
            }
            
            // Update the medication request to dispensed
            $query = "UPDATE " . $this->medication_requests_table . " 
                      SET status = 'dispensed', 
                          dispensed_by = :nurse_id,
                          dispensed_notes = :notes,
                          dispensed_at = NOW()
                      WHERE request_id = :request_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':nurse_id', $nurse_id);
            $stmt->bindParam(':notes', $notes);
            $stmt->bindParam(':request_id', $request_id);
            $stmt->execute();
            
            return true;
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }
    
    /**
     * Add patient log entry
     */
    public function addPatientLog($student_id, $nurse_id, $log_entry) {
        try {
            // Verify student exists
            $check_query = "SELECT COUNT(*) FROM " . $this->users_table . " 
                           WHERE user_id = :student_id AND role = 'student'";
            $check_stmt = $this->conn->prepare($check_query);
            $check_stmt->bindParam(':student_id', $student_id);
            $check_stmt->execute();
            
            if ($check_stmt->fetchColumn() == 0) {
                return "Student not found.";
            }
            
            // Add the log entry
            $query = "INSERT INTO " . $this->patient_logs_table . " 
                      (student_id, staff_id, log_entry, created_at) 
                      VALUES (:student_id, :nurse_id, :log_entry, NOW())";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':student_id', $student_id);
            $stmt->bindParam(':nurse_id', $nurse_id);
            $stmt->bindParam(':log_entry', $log_entry);
            $stmt->execute();
            
            return true;
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }
    
    /**
     * Get all pending medication requests
     */
    public function getPendingMedicationRequests() {
        try {
            $query = "SELECT mr.*, u.first_name, u.last_name 
                      FROM " . $this->medication_requests_table . " mr
                      JOIN " . $this->users_table . " u ON mr.student_id = u.user_id
                      WHERE mr.status = 'approved'
                      ORDER BY mr.created_at ASC";
            $stmt = $this->conn->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Get completed medication requests
     */
    public function getCompletedMedicationRequests() {
        try {
            $query = "SELECT mr.*, 
                      u_student.first_name as student_first_name, u_student.last_name as student_last_name,
                      u_staff.first_name as staff_first_name, u_staff.last_name as staff_last_name
                      FROM " . $this->medication_requests_table . " mr
                      JOIN " . $this->users_table . " u_student ON mr.student_id = u_student.user_id
                      LEFT JOIN " . $this->users_table . " u_staff ON mr.dispensed_by = u_staff.user_id
                      WHERE mr.status = 'dispensed'
                      ORDER BY mr.dispensed_at DESC";
            $stmt = $this->conn->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Get patient logs for a specific student
     */
    public function getPatientLogs($student_id) {
        try {
            $query = "SELECT pl.*, 
                      u_staff.first_name as staff_first_name, u_staff.last_name as staff_last_name
                      FROM " . $this->patient_logs_table . " pl
                      JOIN " . $this->users_table . " u_staff ON pl.staff_id = u_staff.user_id
                      WHERE pl.student_id = :student_id
                      ORDER BY pl.created_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':student_id', $student_id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>