


<?php 
session_start();
// Include required files
include("../eclinic_database.php"); 
include("nurse_dashboard_crud.php");

// Check if user is logged in and is a nurse
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'nurse') {
    header('Location: ../login.php');
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
        $request_id = isset($_POST['medication_id']) ? intval($_POST['medication_id']) : 0;
        $new_status = isset($_POST['new_status']) ? $_POST['new_status'] : '';
        $notes = isset($_POST['notes']) ? $_POST['notes'] : '';
        
        if ($request_id && $new_status) {
            // First, update the medication status
            $result = $nurseManager->updateMedicationStatus($request_id, $new_status, $nurse_id);
            
            // Then, if there are notes, add them to the request
            if (!empty($notes)) {
                $nurseManager->addApprovalNotesToMedicationRequest($request_id, $notes);
            }
            
            // If approved status, additionally call dispense medication
            if ($new_status == 'approved') {
                $dispenseResult = $nurseManager->dispenseMedication($request_id, $nurse_id, $notes);
                if ($dispenseResult === true) {
                    $_SESSION['message'] = "Medication request approved and dispensed successfully.";
                } else {
                    // Only show error if dispense specifically failed
                    $_SESSION['error'] = is_string($dispenseResult) ? $dispenseResult : "Failed to dispense medication.";
                }
            } else {
                $_SESSION['message'] = "Medication request " . $new_status . " successfully.";
            }
            
            // Redirect to refresh the page
            header('Location: medication_requests.php');
            exit();
        } else {
            $_SESSION['error'] = "Invalid medication request data received.";
            header('Location: medication_requests.php');
            exit();
        }
    }
}

// Include the view template
include("medication_requests_view.php");
?>
