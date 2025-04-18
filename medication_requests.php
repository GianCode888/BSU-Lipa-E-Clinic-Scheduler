<?php
session_start();

// Include required files
include("eclinic_database.php");
include("nurse_dashboard_crud.php");

// Check if user is logged in and is a nurse
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'nurse') {
    header('Location: login.php');
    exit();
}

$nurse_id = $_SESSION['user_id'];
$database = new DatabaseConnection();
$nurseManager = new NurseManager($database->getConnect());

// Get nurse information
$nurse = $nurseManager->getNurseInfo($nurse_id);

// Get pending medication requests
$pendingRequests = $nurseManager->getMedicationRequests('pending');

// Handle form submissions for updating medication status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'update_medication') {
        $request_id = $_POST['request_id'];
        $new_status = $_POST['new_status'];
        $notes = isset($_POST['notes']) ? $_POST['notes'] : '';
        
        $result = $nurseManager->updateMedicationStatus($request_id, $new_status, $nurse_id);
        
        if ($result) {
            // If approved and dispensed, add to patient logs
            if ($new_status == 'approved') {
                $medication = $nurseManager->getMedicationRequestDetails($request_id);
                if ($medication) {
                    $log_details = "Medication dispensed: " . $medication['medication_name'] .
                                  ". Dosage: " . $medication['dosage'] .
                                  ". Notes: " . $notes;
                    
                    $nurseManager->addPatientLog($medication['student_id'], $nurse_id, $log_details);
                }
            }
            
            $_SESSION['message'] = "Medication request updated successfully.";
        } else {
            $_SESSION['error'] = "Failed to update medication request.";
        }
        
        // Redirect to refresh the page
        header('Location: medication_requests.php');
        exit();
    }
}

// Include the HTML template - this will use variables defined above
include("medication_requests_view.php");
?>