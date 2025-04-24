<?php
require_once '../eclinic_database.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$database = new DatabaseConnection();
$conn = $database->getConnect();
$doctor_id = $_SESSION['user_id'];

class Doctor {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function updateAvailability($doctor_id, $day) {
        $stmt = $this->conn->prepare("CALL UpdateDoctorAvailability(:doctor_id, :day)");
        $stmt->execute([':doctor_id' => $doctor_id, ':day' => $day]);
        $stmt->closeCursor();
    }

    public function viewRequests($doctor_id) {
        $stmt = $this->conn->prepare("CALL ViewAppointmentsByDoctor(:doctor_id)");
        $stmt->execute([':doctor_id' => $doctor_id]);
        $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $appointments;
    }

    public function viewMedicationRequests() {
        $stmt = $this->conn->prepare("CALL ViewMedicationRequests()");
        $stmt->execute();
        $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $requests;
    }

    public function approveAppointment($appointment_id, $status, $notes) {
        $stmt = $this->conn->prepare("CALL UpdateAppointmentStatus(:appointment_id, :status, :notes)");
        $stmt->execute([
            ':appointment_id' => $appointment_id,
            ':status' => $status,
            ':notes' => $notes
        ]);
        $stmt->closeCursor();
    }
}

$doctor = new Doctor($conn);

// Optional: auto-run availability update if called via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_availability':
                if (!empty($_POST['available_day'])) {
                    $doctor->updateAvailability($doctor_id, $_POST['available_day']);
                    header("Location: ../doctor_dashboard.php");
                }
                break;
        }
    }
}
?>
