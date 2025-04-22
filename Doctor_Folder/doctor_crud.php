<?php
session_start();
require_once '../eclinic_database.php';

$database = new DatabaseConnection();
$conn = $database->getConnect();

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

  public function approve_appointment_request($appointment_id) {
    $stmt = $this->conn->prepare("CALL ApproveAppointmentRequest(:appointmentID)");
    $stmt->execute(['appointmentID' => $appointment_id]);
    return $stmt;
  }

  public function decline_appointment_request($appointment_id) {
  $stmt = $this->conn->prepare("CALL DeclineAppointmentRequest(:appointmentID)");
  $stmt->execute(['appointmentID' => $appointment_id]);
  return $stmt;
  }

  public function approve_medication_request($request_id) {
    $stmt = $this->conn->prepare("CALL ApproveMedicationRequest(:medicationID)");
    $stmt->execute(['medicationID' => $request_id]);
    return $stmt;
  }

  public function decline_medication_request($request_id) {
  $stmt = $this->conn->prepare("CALL DeclineMedicationRequest(:medicationID)");
  $stmt->execute(['medicationID' => $request_id]);
  return $stmt;
  }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && isset($_POST['request_id'])) {
  $doctor = new Doctor($conn);
  $request_id = $_POST['request_id'];
  $action = $_POST['action'];

  if (isset($_POST['action']) && isset($_POST['request_id']) && isset($_POST['request_type'])) {
    $doctor = new Doctor($conn);
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];
    $request_type = $_POST['request_type'];

    if ($request_type == 'Appointment') {
        if ($action == 'approve') {
            $doctor->approve_appointment_request($request_id);
        } elseif ($action == 'decline') {
            $doctor->decline_appointment_request($request_id);
        }
    } elseif ($request_type == 'Medication') {
        if ($action == 'approve') {
            $doctor->approve_medication_request($request_id);
        } elseif ($action == 'decline') {
            $doctor->decline_medication_request($request_id);
        }
    }
    
    header("Location: student_request.php");
    exit();
}

}
?>