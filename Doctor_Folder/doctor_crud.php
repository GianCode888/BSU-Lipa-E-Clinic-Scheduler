<?php
session_start();
require_once '../eclinic_database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    die("You must be logged in as a doctor to access this page.");
}

$user_id = $_SESSION['user_id'];
$database = new DatabaseConnection();
$conn = $database->getConnect();
$doctor = new Doctor($conn);

class Doctor {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function student_appointment_request() {
        $stmt = $this->conn->prepare("CALL StudentAppointmentRequest()");
        $stmt->execute();
        return $stmt;
    }

    public function approve_appointment_request($request_id, $approver_id) {
        $stmt = $this->conn->prepare("CALL ApproveAppointmentRequest(:appointmentID, :approver_id)");
        $stmt->execute(['appointmentID' => $request_id, 'approver_id' => $approver_id]);
        return $stmt;
    }

    public function decline_appointment_request($appointment_id) {
        $stmt = $this->conn->prepare("CALL DeclineAppointmentRequest(:appointmentID)");
        $stmt->execute(['appointmentID' => $appointment_id]);
        return $stmt;   
    }

    public function add_approval_notes_to_appointment($appointment_id, $approval_notes, $user_id) {
        $stmt = $this->conn->prepare("CALL AddApprovalNotesToAppointment(:appointment_id, :approval_notes, :user_id)");
        $stmt->execute(['appointment_id' => $appointment_id, 'approval_notes' => $approval_notes, 'user_id' => $user_id]);
        return $stmt;
    }

    public function get_approval_notes_for_appointment($appointment_id) {
        $stmt = $this->conn->prepare("CALL GetApprovalNotesForAppointment(:appointmentID)");
        $stmt->execute(['appointmentID' => $appointment_id]);
        return $stmt->fetchColumn();
    }

    public function approve_medication_request($request_id, $approver_id) {
        $stmt = $this->conn->prepare("CALL ApproveMedicationRequest(:medicationID, :approver_id)");
        $stmt->execute(['medicationID' => $request_id, 'approver_id' => $approver_id]);
        return $stmt;
    }

    public function decline_medication_request($request_id) {
        $stmt = $this->conn->prepare("CALL DeclineMedicationRequest(:medicationID)");
        $stmt->execute(['medicationID' => $request_id]);
        return $stmt;
    }

    public function add_approval_notes_to_medication($medication_id, $approval_notes) {
        $stmt = $this->conn->prepare("CALL AddApprovalNotesToMedicationRequest(:medication_id, :approval_notes)");
        $stmt->execute([
            'medication_id' => $medication_id,
            'approval_notes' => $approval_notes
        ]);
        return $stmt;
    }

    public function get_approval_notes_for_medication($request_id) {
        $stmt = $this->conn->prepare("CALL GetApprovalNotesForMedication(:request_id)");
        $stmt->execute(['request_id' => $request_id]);
        return $stmt->fetchColumn();
    }

    public function get_request_details($request_id, $request_type) {
        if ($request_type == 'Appointment') {
            $stmt = $this->conn->prepare("CALL GetAppointmentRequestDetails(:request_id)");
        } elseif ($request_type == 'Medication') {
            $stmt = $this->conn->prepare("CALL GetMedicationRequestDetails(:request_id)");
        } else {
            return null;
        }

        $stmt->execute(['request_id' => $request_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function get_all_approved_requests() {
        $stmt = $this->conn->prepare("CALL GetAllApprovedRequests()");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_availability_for_day($doctor_id, $available_day) {
        $stmt = $this->conn->prepare("CALL GetDoctorAvailability(:doctor_id)");
        $stmt->execute(['doctor_id' => $doctor_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_approved_requests($doctor_id) {
        try {
            $stmt = $this->conn->prepare("CALL GetAllApprovedRequests(:doctor_id)");
            $stmt->bindParam(':doctor_id', $doctor_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }

    public function add_availability($user_id, $available_date, $start_time, $end_time, $note) {
        $stmt = $this->conn->prepare("CALL AddDoctorAvailability(?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $available_date, $start_time, $end_time, $note]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function view_schedule($user_id) {
        $stmt = $this->conn->prepare("CALL ViewSchedule(?)");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

}
?>
