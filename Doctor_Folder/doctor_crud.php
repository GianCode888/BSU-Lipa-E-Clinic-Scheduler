<?php
session_start();
require_once '../eclinic_database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    die("You must be logged in as a doctor to approve the request.");
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
        $stmt->execute(['medicationID' => $request_id, ':approver_id' => $approver_id]);
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
        $stmt = null;

        if ($request_type == 'Appointment') {
            $stmt = $this->conn->prepare("CALL GetAppointmentRequestDetails(:request_id)");
        } elseif ($request_type == 'Medication') {
            $stmt = $this->conn->prepare("CALL GetMedicationRequestDetails(:request_id)");
        }

        $stmt->execute(['request_id' => $request_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function get_all_approved_requests() {
        $stmt = $this->conn->prepare("CALL GetAllApprovedRequests()");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'];
    $request_type = $_POST['request_type'];
    $action = $_POST['action'];

    if ($action === 'decline') {
        if ($request_type === 'Appointment') {
            $doctor->decline_appointment_request($request_id);
        } elseif ($request_type === 'Medication') {
            $doctor->decline_medication_request($request_id);
        }

        header("Location: student_request.php");
        exit();
    }

    if ($action === 'approve') {
        header("Location: note_approval.php?request_id={$request_id}&request_type={$request_type}");
        exit();
    }

    if (isset($_POST['approval_notes'])) {
        $approval_notes = $_POST['approval_notes'];

        if ($request_type === 'Appointment') {
            $doctor->add_approval_notes_to_appointment($request_id, $approval_notes, $user_id);
            $doctor->approve_appointment_request($request_id, $user_id);
        } elseif ($request_type === 'Medication') {
            $doctor->add_approval_notes_to_medication($request_id, $approval_notes);
            $doctor->approve_medication_request($request_id, $user_id);
        }

        header("Location: student_request.php");
        exit();
    }
}
?>
