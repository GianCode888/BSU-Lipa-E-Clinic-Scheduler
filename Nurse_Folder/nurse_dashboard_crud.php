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
        return $result['count'] ?? 0;
    }

    public function getMedicationRequests($status = 'pending') {
        $stmt = $this->conn->prepare("CALL GetMedicationRequests(?)");
        $stmt->execute([$status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMedicationRequestDetails($request_id) {
        $stmt = $this->conn->prepare("CALL GetMedicationRequestDetails(?)");
        $stmt->execute([$request_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
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


    public function addPatientLog($student_name, $contact, $address, $nurse_name, $log_details) {
        $stmt = $this->conn->prepare("CALL AddPatientLog(?, ?, ?, ?, ?)");
        return $stmt->execute([$student_name, $contact, $address, $nurse_name, $log_details]);
    }

    public function deleteCompletedLogs($log_id) {
        try {
            $stmt = $this->conn->prepare("CALL DeleteCompletedLogs(?)");
            $stmt->execute([$log_id]);
            if ($stmt->rowCount() > 0) {
                return true; 
            } else {
                return false; 
            }
        } catch (PDOException $e) {
            error_log("Error in deleteCompletedLogs: " . $e->getMessage());
            return false;
        }
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

    public function getCompletedLogs() {
        $stmt = $this->conn->prepare("CALL GetCompletedLogs()");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>