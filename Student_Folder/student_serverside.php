<?php
require_once '../eclinic_database.php';
require_once 'student_crud.php';

$database = new DatabaseConnection();
$conn = $database->getConnect();

$student_id = $_SESSION['user_id'];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student = new Student($conn);

    // Handle appointment request
    if (isset($_POST['request_type']) && $_POST['request_type'] === 'appointment') {
        if (isset($_POST['appointment_date'], $_POST['appointment_time'], $_POST['reason'])) {
            $student->request_appointment($student_id, $_POST['appointment_date'], $_POST['appointment_time'], $_POST['reason']);
            $_SESSION['message'] = 'Appointment requested successfully!';
            header("Location: ../student_dashboard.php");
            exit;
        } else {
            $_SESSION['error_message'] = 'Please provide all the appointment details.';
            header("Location: ../student_dashboard.php");
            exit;
        }
    }

    // Handle medication request
    if (isset($_POST['request_type']) && $_POST['request_type'] === 'medication') {
        if (isset($_POST['medication'])) {
            $medication = htmlspecialchars(trim($_POST['medication']));
            $student->request_medication($student_id, $medication);
            $_SESSION['message'] = 'Medication requested successfully!';
            header("Location: ../student_dashboard.php");
            exit;
        } else {
            $_SESSION['error_message'] = 'Please provide the medication details.';
            header("Location: ../student_dashboard.php");
            exit;
        }
    }

    // Handle medical information submission or update
    if (isset($_POST['blood_type'], $_POST['allergies'], $_POST['med_condition'], $_POST['medications_taken'], $_POST['emergency_contact_name'], $_POST['relationship_to_student'], $_POST['contact_number'], $_POST['address'])) {
        $blood_type = $_POST['blood_type'];
        $allergies = $_POST['allergies'];
        $med_condition = $_POST['med_condition'];
        $medications_taken = $_POST['medications_taken'];
        $emergency_contact_name = $_POST['emergency_contact_name'];
        $relationship = $_POST['relationship_to_student'];
        $contact_number = $_POST['contact_number'];
        $address = $_POST['address'];

        $existing_info = $student->getMedicalInfo($student_id);

        if ($existing_info) {
            // Update existing medical info
            $student->updateMedicalInfo(
                $student_id, $blood_type, $allergies, $med_condition,
                $medications_taken, $emergency_contact_name,
                $relationship, $contact_number, $address
            );
            $_SESSION['message'] = 'Medical information updated successfully!';
        } else {
            // Add new medical info
            $student->addMedicalInfo(
                $student_id, $blood_type, $allergies, $med_condition,
                $medications_taken, $emergency_contact_name,
                $relationship, $contact_number, $address
            );
            $_SESSION['message'] = 'Medical information submitted successfully!';
        }
        header("Location: student_profile.php");
        exit;
    }

    // Retrieve prescriptions for the logged-in student
    if (isset($_POST['get_prescription'])) {
        $stmt = $conn->prepare("CALL GetPrescription(?)");
        $stmt->execute([$student_id]);
        $prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        if ($prescriptions) {
            $_SESSION['prescriptions'] = $prescriptions;
            header("Location: doctor_prescriptions.php");
            exit;
        } else {
            $_SESSION['error_message'] = 'No prescriptions found.';
            header("Location: student_dashboard.php");
            exit;
        }
    }

    // Handle deletion of appointments or medications
    if (isset($_POST['delete'])) {
        if (isset($_POST['appointment_id'])) {
            $student->delete_appointment($_POST['appointment_id']);
            $_SESSION['message'] = 'Appointment deleted successfully!';
            header("Location: view_appointment.php");
            exit;
        } elseif (isset($_POST['medication_id'])) {
            $student->delete_medication($_POST['medication_id']);
            $_SESSION['message'] = 'Medication deleted successfully!';
            header("Location: view_medication.php");
            exit;
        }
    }
}
?>
