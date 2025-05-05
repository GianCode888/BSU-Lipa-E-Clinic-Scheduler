<?php
require_once '../eclinic_database.php';
require_once 'doctor_crud.php';

// Ensure the user is logged in as a doctor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    // If not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$database = new DatabaseConnection();
$conn = $database->getConnect();
$doctor = new Doctor($conn);

// Check if the form is being submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle doctor availability submission
    if (isset($_POST['available_date'], $_POST['start_time'], $_POST['end_time'], $_POST['notes'])) {
        handleDoctorAvailability($doctor, $user_id, $_POST['available_date'], $_POST['start_time'], $_POST['end_time'], $_POST['notes']);
    }

    // Handle prescription submission (diagnosis, prescription, student user_id)
    if (isset($_POST['diagnosis'], $_POST['prescription'], $_POST['student_user_id'])) {
        handlePrescriptionSubmission($doctor, $_POST['student_user_id'], $_POST['diagnosis'], $_POST['prescription']);
    }

    // Handle request actions (approve/decline)
    handleRequestActions($doctor);
}

// Fetch doctor schedule if requested
$schedule_data = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['view_schedule'])) {
    try {
        $schedule_data = $doctor->view_schedule($user_id);
    } catch (Exception $e) {
        echo "Error fetching schedule: " . $e->getMessage();
    }
}

// Function to handle doctor availability submission
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

// Function to handle prescription submission
function handlePrescriptionSubmission($doctor, $student_user_id, $diagnosis, $prescription) {
    try {
        // Validate required fields
        if (empty($student_user_id) || empty($diagnosis) || empty($prescription)) {
            throw new Exception("All fields are required.");
        }

        // Automatically set the current date for created_at
        $created_at = date('Y-m-d'); // Get the current date

        // Check if the student already has a prescription
        $students_with_prescriptions = $doctor->get_students_with_prescriptions();
        $prescribed_user_ids = array_column($students_with_prescriptions, 'user_id');

        if (in_array($student_user_id, $prescribed_user_ids)) {
            // Student already has a prescription
            echo "Student already has a prescription.";
            header("Location: prescription.php"); // Redirect after error
            exit();
        }

        // Insert the prescription into the database using the stored procedure
        $doctor->insert_prescription($student_user_id, $diagnosis, $prescription, $created_at);

        echo "Prescription saved successfully!";
        header("Location: prescription.php"); // Redirect after saving the prescription
        exit();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Function to handle request actions (approve/decline)
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
