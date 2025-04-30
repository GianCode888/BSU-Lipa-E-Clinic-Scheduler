<?php
require_once '../eclinic_database.php';
require_once 'doctor_crud.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    die("You must be logged in as a doctor to perform this action.");
}

$user_id = $_SESSION['user_id'];
$database = new DatabaseConnection();
$conn = $database->getConnect();
$doctor = new Doctor($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['doctor_name'], $_POST['available_date'], $_POST['start_time'], $_POST['end_time'], $_POST['notes'])) {
        handleDoctorAvailability($doctor, $user_id, $_POST['available_date'], $_POST['start_time'], $_POST['end_time'], $_POST['notes']);
    }
    handleRequestActions($doctor);
}

$schedule_data = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['view_schedule'])) {
    try {
        $schedule_data = $doctor->view_schedule($user_id);
    } catch (Exception $e) {
        echo "Error fetching schedule: " . $e->getMessage();
    }
}

function handleDoctorAvailability($doctor, $user_id, $available_date, $start_time, $end_time, $note) {
    if ($available_date && $start_time && $end_time) {
        try {
            $doctor->add_availability($user_id, $available_date, $start_time, $end_time, $note);
            header("Location: ../doctor_dashboard.php");
            exit();
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Please provide all required availability fields.";
    }
}

function handleRequestActions($doctor) {
    $request_id = $_POST['request_id'] ?? '';
    $request_type = $_POST['request_type'] ?? '';
    $action = $_POST['action'] ?? '';

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
            $doctor->add_approval_notes_to_appointment($request_id, $approval_notes, $_SESSION['user_id']);
            $doctor->approve_appointment_request($request_id, $_SESSION['user_id']);
        } elseif ($request_type === 'Medication') {
            $doctor->add_approval_notes_to_medication($request_id, $approval_notes);
            $doctor->approve_medication_request($request_id, $_SESSION['user_id']);
        }
        header("Location: student_request.php");
        exit();
    }
}
?>
