<?php

require_once '../../eclinic_database.php';

class MedDataService {
    private $conn;

    public function __construct() {
        $db = new DatabaseConnection();
        $this->conn = $db->getConnect();    
    }

    //dashboard
    public function getTotalPatients() {
        $stmt = $this->conn->prepare("CALL GetTotalPatients()");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_patients'];
    }

    public function getNewRecordsLastWeek() {
        $stmt = $this->conn->prepare("CALL GetNewRecordsLastWeek()");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['new_records'];
    }

    public function getPendingRequests() {
        $stmt = $this->conn->prepare("CALL GetPendingMedicationRequests()");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['pending_requests'];
    }

    public function getRecentMedicalRecords($limit = 10) {
        $stmt = $this->conn->prepare("CALL GetRecentMedicalRecords(?)");
        $stmt->bindParam(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
       //view_medical_history
    public function getTodaysAppointments() {
        $stmt = $this->conn->prepare("CALL GetTodaysAppointments()");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['today_appointments'];
    }
    
    public function getPendingAppointments() {
        $stmt = $this->conn->prepare("CALL GetPendingAppointments()");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['pending_appointments'];
    }
    
    public function getAvailableDoctors() {
        $stmt = $this->conn->prepare("CALL GetAvailableDoctors()");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['available_doctors'];
    }
    
    public function getPendingMedicationRequestsCount() {
        $stmt = $this->conn->prepare("CALL GetPendingMedicationRequestsCount()");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['pending_requests'];
    }

    public function getApprovedMedicationRequestsCount() {
        $stmt = $this->conn->prepare("CALL GetApprovedMedicationRequestsCount()");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['approved_requests'];
    }

    public function getRejectedMedicationRequestsCount() {
        $stmt = $this->conn->prepare("CALL GetRejectedMedicationRequestsCount()");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['rejected_requests'];
    }

    public function getPendingTodayCount() {
        $stmt = $this->conn->prepare("CALL GetPendingTodayCount()");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['pending_today'];
    }

    public function getApprovedTodayCount() {
        $stmt = $this->conn->prepare("CALL GetApprovedTodayCount()");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['approved_today'];
    }

    public function getRejectedTodayCount() {
        $stmt = $this->conn->prepare("CALL GetRejectedTodayCount()");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['rejected_today'];
    }

    public function getTotalMedicationCount() {
        $stmt = $this->conn->prepare("CALL GetTotalMedicationCount()");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_medication'];
    }

    public function getMedicalRecordsByStatus($status) {
        $stmt = $this->conn->prepare("CALL GetMedicalRecordsByStatus(?)");
        $stmt->bindParam(1, $status, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllMedicalRecords() {
        $stmt = $this->conn->prepare("CALL GetAllMedicalRecords()");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStudentMedicalInfo($userId) {
        $stmt = $this->conn->prepare("CALL GetStudentMedicalInfo(?)");
        $stmt->bindParam(1, $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //appointments

    public function getTotalAppointmentsCount() {
        $stmt = $this->conn->prepare("CALL GetTotalAppointmentsCount()");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_appointments'];
    }
    
    public function getPendingAppointmentsTodayCount() {
        $stmt = $this->conn->prepare("CALL GetPendingAppointmentsTodayCount()");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['pending_appointments_today'];
    }
    
    public function getApprovedAppointmentsTodayCount() {
        $stmt = $this->conn->prepare("CALL GetApprovedAppointmentsTodayCount()");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['approved_appointments_today'];
    }
    
    
    public function getDeclinedAppointmentsTodayCount() {
        $stmt = $this->conn->prepare("CALL GetDeclinedAppointmentsTodayCount()");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['declined_appointments_today'];
    }


    public function getFilteredAppointments($userId = null, $status = null) {
        $stmt = $this->conn->prepare("CALL GetFilteredAppointments(?, ?)");
        $stmt->bindParam(1, $userId, PDO::PARAM_INT);
        $stmt->bindParam(2, $status, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPatientsWithAppointments() {
        $stmt = $this->conn->prepare("CALL GetPatientsWithAppointments()");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //doctors availability

    public function getTotalDoctors() {
        $stmt = $this->conn->prepare("CALL GetTotalDoctors()");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_doctors'];
    }
    
    public function getCurrentlyAvailableDoctors() {
        $stmt = $this->conn->prepare("CALL GetCurrentlyAvailableDoctors()");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['available_doctors'];
    }
    
    public function getDoctorAvailabilityByWeek($userId = null, $weekStart = null, $weekEnd = null) {
        // If week start/end not provided, use current week
        if ($weekStart === null) {
            $weekStart = date('Y-m-d', strtotime('monday this week'));
        }
        if ($weekEnd === null) {
            $weekEnd = date('Y-m-d', strtotime('friday this week'));
        }
        
        $stmt = $this->conn->prepare("CALL GetDoctorAvailabilityByWeek(?, ?, ?)");
        $stmt->bindParam(1, $userId, PDO::PARAM_INT);
        $stmt->bindParam(2, $weekStart, PDO::PARAM_STR);
        $stmt->bindParam(3, $weekEnd, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAllDoctors() {
        $sql = "SELECT user_id, first_name, last_name FROM users WHERE role = 'doctor' ORDER BY first_name, last_name";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function getCombinedDoctorAvailability($weekStart, $weekEnd) {
        $stmt = $this->conn->prepare("CALL GetCombinedDoctorAvailability(?, ?)");
        $stmt->bindParam(1, $weekStart, PDO::PARAM_STR);
        $stmt->bindParam(2, $weekEnd, PDO::PARAM_STR);
        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Organize the results by time slot and day
        $organizedResults = [];
        foreach ($results as $row) {
            $slotTime = $row['slot_time'];
            $dayName = $row['day_name'];
            
            if (!isset($organizedResults[$slotTime])) {
                $organizedResults[$slotTime] = [
                    'display' => $row['display_time'],
                    'Monday' => ['status' => 'Unavailable', 'doctors' => ''],
                    'Tuesday' => ['status' => 'Unavailable', 'doctors' => ''],
                    'Wednesday' => ['status' => 'Unavailable', 'doctors' => ''],
                    'Thursday' => ['status' => 'Unavailable', 'doctors' => ''],
                    'Friday' => ['status' => 'Unavailable', 'doctors' => '']
                ];
            }
            
            $organizedResults[$slotTime][$dayName] = [
                'status' => $row['status'],
                'doctors' => $row['available_doctors'],
                'count' => $row['doctor_count']
            ];
        }
        
        return $organizedResults;
    }

}
?>
