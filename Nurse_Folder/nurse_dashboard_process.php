<<<<<<< HEAD
<?php
session_start();
include("eclinic_database.php");
include("nurse_dashboard_crud.php");

// Check if user is logged in and is a nurse
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'nurse') {
    $_SESSION['error'] = "You must be logged in as a nurse to perform this action.";
    header('Location: login.php');
    exit();
}

$nurse_id = $_SESSION['user_id'];
$database = new DatabaseConnection();
$nurseManager = new NurseManager($database->getConnect());

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle appointment status updates
    if (isset($_POST['action']) && $_POST['action'] == 'update_appointment') {
        if (isset($_POST['appointment_id']) && isset($_POST['new_status'])) {
            $appointment_id = $_POST['appointment_id'];
            $new_status = $_POST['new_status'];
            
            $result = $nurseManager->updateAppointmentStatus($appointment_id, $new_status, $nurse_id);
            
            if ($result === true) {
                $_SESSION['message'] = "Appointment status updated successfully.";
            } else {
                $_SESSION['error'] = $result; // Error message from the method
            }
        } else {
            $_SESSION['error'] = "Missing required information for updating appointment.";
        }
    }
    
    // Handle medication dispensing
    elseif (isset($_POST['action']) && $_POST['action'] == 'dispense_medication') {
        if (isset($_POST['request_id'])) {
            $request_id = $_POST['request_id'];
            $notes = isset($_POST['notes']) ? $_POST['notes'] : '';
            
            $result = $nurseManager->dispenseMedication($request_id, $nurse_id, $notes);
            
            if ($result === true) {
                $_SESSION['message'] = "Medication dispensed successfully.";
            } else {
                $_SESSION['error'] = $result; // Error message from the method
            }
        } else {
            $_SESSION['error'] = "Missing required information for dispensing medication.";
        }
    }
    
    // Handle patient log entries
    elseif (isset($_POST['action']) && $_POST['action'] == 'add_patient_log') {
        if (isset($_POST['student_id']) && isset($_POST['log_entry'])) {
            $student_id = $_POST['student_id'];
            $log_entry = $_POST['log_entry'];
            
            $result = $nurseManager->addPatientLog($student_id, $nurse_id, $log_entry);
            
            if ($result === true) {
                $_SESSION['message'] = "Patient log entry added successfully.";
            } else {
                $_SESSION['error'] = $result; // Error message from the method
            }
        } else {
            $_SESSION['error'] = "Missing required information for adding patient log.";
        }
    }
    
    // Unknown action
    else {
        $_SESSION['error'] = "Invalid action requested.";
    }
    
    // Redirect back to the appropriate page
    if (isset($_POST['redirect']) && !empty($_POST['redirect'])) {
        header('Location: ' . $_POST['redirect']);
    } else {
        header('Location: nurse_dashboard.php');
    }
    exit();
} else {
    // If not POST request, redirect to dashboard
    $_SESSION['error'] = "Invalid request method.";
    header('Location: nurse_dashboard.php');
    exit();
=======
<?php 
session_start(); 
include("eclinic_database.php"); 
include("Nurse_Folder/nurse_dashboard_crud.php");  

// Check if user is logged in and is a nurse 
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'nurse') {     
    $_SESSION['error'] = "You must be logged in as a nurse to perform this action.";     
    header('Location: login.php');     
    exit(); 
}  

$nurse_id = $_SESSION['user_id']; 
$database = new DatabaseConnection(); 
$nurseManager = new NurseManager($database->getConnect());  

if ($_SERVER['REQUEST_METHOD'] == 'POST') {     
    // Handle appointment status updates     
    if (isset($_POST['action']) && $_POST['action'] == 'update_appointment') {         
        if (isset($_POST['appointment_id']) && isset($_POST['new_status'])) {             
            $appointment_id = $_POST['appointment_id'];             
            $new_status = $_POST['new_status'];             
            
            $result = $nurseManager->updateAppointmentStatus($appointment_id, $new_status, $nurse_id);             
            
            if ($result === true) {                 
                $_SESSION['message'] = "Appointment status updated successfully.";             
            } else {                 
                $_SESSION['error'] = $result; // Error message from the method             
            }         
        } else {             
            $_SESSION['error'] = "Missing required information for updating appointment.";         
        }     
    }     
    
    // Handle medication dispensing     
    elseif (isset($_POST['action']) && $_POST['action'] == 'dispense_medication') {         
        if (isset($_POST['request_id'])) {             
            $request_id = intval($_POST['request_id']);             
            $notes = isset($_POST['notes']) ? $_POST['notes'] : '';             
            
            try {
                $result = $nurseManager->dispenseMedication($request_id, $nurse_id, $notes);             
                
                if ($result === true) {                 
                    $_SESSION['message'] = "Medication dispensed successfully.";             
                } else {                 
                    $_SESSION['error'] = "Failed to dispense medication: " . $result;             
                }
            } catch (Exception $e) {
                $_SESSION['error'] = "Error: " . $e->getMessage();
            }
        } else {             
            $_SESSION['error'] = "Missing medication request ID.";         
        }     
    }     
    
    // Handle patient log entries     
    elseif (isset($_POST['action']) && $_POST['action'] == 'add_patient_log') {         
        if (isset($_POST['student_id']) && isset($_POST['log_entry'])) {             
            $student_id = $_POST['student_id'];             
            $log_entry = $_POST['log_entry'];             
            
            $result = $nurseManager->addPatientLog($student_id, $nurse_id, $log_entry);             
            
            if ($result === true) {                 
                $_SESSION['message'] = "Patient log entry added successfully.";             
            } else {                 
                $_SESSION['error'] = $result; // Error message from the method             
            }         
        } else {             
            $_SESSION['error'] = "Missing required information for adding patient log.";         
        }     
    }     
    
    // Unknown action     
    else {         
        $_SESSION['error'] = "Invalid action requested.";     
    }     
    
    // Redirect back to the appropriate page     
    if (isset($_POST['redirect']) && !empty($_POST['redirect'])) {         
        header('Location: ' . $_POST['redirect']);     
    } else {         
        header('Location: nurse_dashboard.php');     
    }     
    exit(); 
} else {     
    // If not POST request, redirect to dashboard     
    $_SESSION['error'] = "Invalid request method.";     
    header('Location: nurse_dashboard.php');     
    exit(); 
>>>>>>> 03505b4a75b1c764354b7343e0c0426bd04c9f10
}
?>