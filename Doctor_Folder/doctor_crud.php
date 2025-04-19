<?php
require_once '../eclinic_database.php';

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
        $stmt = $this->conn->prepare("UPDATE availability SET available = 1 WHERE doctor_id = :doctor_id AND day = :day");
        $stmt->execute([':doctor_id' => $doctor_id, ':day' => $day]);
    }

    public function viewRequests($doctor_id) {
        $stmt = $this->conn->prepare("SELECT * FROM appointments WHERE doctor_id = :doctor_id");
        $stmt->execute([':doctor_id' => $doctor_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function viewMedicationRequests() {
        // Optional: JOIN to include student name
        $stmt = $this->conn->prepare("
            SELECT mr.*, s.fullname AS student_name 
            FROM medication_requests mr 
            JOIN students s ON mr.student_id = s.student_id
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // ✅ Return as array
    }

    public function approveAppointment($appointment_id, $status, $notes) {
        $stmt = $this->conn->prepare("UPDATE appointments SET status = :status, notes = :notes WHERE appointment_id = :appointment_id");
        $stmt->execute([
            ':appointment_id' => $appointment_id,
            ':status' => $status,
            ':notes' => $notes
        ]);
    }
}

$doctor = new Doctor($conn);

// ✅ Handle POST Requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_availability':
                if (!empty($_POST['available_day'])) {
                    $doctor->updateAvailability($doctor_id, $_POST['available_day']);
                    header("Location: ../doctor_dashboard.php");
                    exit;
                }
                break;

            case 'approve_appointment':
                $doctor->approveAppointment($_POST['appointment_id'], $_POST['status'], $_POST['notes']);
                header("Location: ../doctor_dashboard.php");
                exit;
        }
    }

    // ✅ Medication Request Update
    if (isset($_POST['med_action'], $_POST['request_id'])) {
        $newStatus = ($_POST['med_action'] === 'approve') ? 'approved' : 'declined';
        try {
            $stmt = $conn->prepare("UPDATE medication_requests SET status = :status WHERE request_id = :id");
            $stmt->execute(['status' => $newStatus, 'id' => $_POST['request_id']]);
            $medicationMessage = "Request #{$_POST['request_id']} has been <strong>$newStatus</strong>.";
        } catch (PDOException $e) {
            $medicationMessage = "Failed to update medication request: " . $e->getMessage();
        }
    }

    // ✅ Appointment Quick Action
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'approve_appointment') {
        $appt_id = $_POST['appointment_id'];
        $status = $_POST['status'];
        $notes = $_POST['notes'] ?? '';
    
        try {
            $update = "UPDATE appointments SET status = ?, notes = ? WHERE appointment_id = ?";
            $stmt = $conn->prepare($update);
            $stmt->execute([$status, $notes, $appt_id]);
    
            header("Location: appointment_requests.php?msg=Appointment+updated+successfully");
            exit();
        } catch (PDOException $e) {
            die("Error updating appointment: " . $e->getMessage());
        }
    }
}