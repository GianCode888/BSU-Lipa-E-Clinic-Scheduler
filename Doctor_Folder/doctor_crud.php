<?php

require_once '../eclinic_database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$database = new DatabaseConnection();
$conn = $database->getConnect();
$doctor_id = $_SESSION['user_id']; // Get logged-in doctor ID

class Doctor {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function updateAvailability($doctor_id, $day) {
        try {
            $stmt = $this->conn->prepare("UPDATE availability SET available = 1 WHERE doctor_id = :doctor_id AND day = :day");
            $stmt->execute([':doctor_id' => $doctor_id, ':day' => $day]);
        } catch (PDOException $e) {
            echo "Error updating availability: " . $e->getMessage();
        }
    }

    public function viewRequests($doctor_id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM appointments WHERE doctor_id = :doctor_id");
            $stmt->execute([':doctor_id' => $doctor_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error fetching appointment requests: " . $e->getMessage();
        }
    }

    public function viewMedicationRequests() {
        try {
            $stmt = $this->conn->prepare("
                SELECT mr.*, u.fullname AS user_name 
                FROM medication_requests mr 
                JOIN users u ON mr.student_id = u.user_id
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Returns requests as an array
        } catch (PDOException $e) {
            echo "Error fetching medication requests: " . $e->getMessage();
        }
    }
    
    public function approveAppointment($appointment_id, $status, $notes) {
        try {
            $stmt = $this->conn->prepare("UPDATE appointments SET status = :status, notes = :notes WHERE appointment_id = :appointment_id");
            $stmt->execute([
                ':appointment_id' => $appointment_id,
                ':status' => $status,
                ':notes' => $notes
            ]);
        } catch (PDOException $e) {
            echo "Error updating appointment status: " . $e->getMessage();
        }
    }

    public function updateMedicationRequestStatus($request_id, $status) {
        try {
            $stmt = $this->conn->prepare("UPDATE medication_requests SET status = :status WHERE request_id = :id");
            $stmt->execute(['status' => $status, 'id' => $request_id]);
            return "Request #$request_id has been $status.";
        } catch (PDOException $e) {
            return "Failed to update medication request: " . $e->getMessage();
        }
    }
}

$doctor = new Doctor($conn);

// Handle POST requests for updating medication request status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['med_action'], $_POST['request_id'])) {
    $newStatus = ($_POST['med_action'] === 'approve') ? 'approved' : 'declined';
    $medicationMessage = $doctor->updateMedicationRequestStatus($_POST['request_id'], $newStatus);
    echo $medicationMessage; // Display success or error message
    header("Location: ../doctor_dashboard.php?msg=" . urlencode($medicationMessage)); // Redirect to dashboard with message
    exit;
}

// Handle other POST requests (e.g., update availability, approve appointments)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
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
?>
