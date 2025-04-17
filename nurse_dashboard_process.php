<?php
session_start();
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

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle appointment status updates
    if (isset($_POST['action']) && $_POST['action'] === 'update_appointment') {
        if (isset($_POST['appointment_id']) && isset($_POST['new_status'])) {
            $appointment_id = $_POST['appointment_id'];
            $new_status = $_POST['new_status'];
            
            if ($nurseManager->updateAppointmentStatus($appointment_id, $new_status, $nurse_id)) {
                $_SESSION['message'] = "Appointment status updated successfully";
            } else {
                $_SESSION['error'] = "Failed to update appointment status";
            }
        } else {
            $_SESSION['error'] = "Missing required information";
        }
    }
    
    // Redirect back to dashboard
    header('Location: nurse_dashboard.php');
    exit();
}<?php
session_start();
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

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle appointment status updates
    if (isset($_POST['action']) && $_POST['action'] === 'update_appointment') {
        if (isset($_POST['appointment_id']) && isset($_POST['new_status'])) {
            $appointment_id = $_POST['appointment_id'];
            $new_status = $_POST['new_status'];
            
            if ($nurseManager->updateAppointmentStatus($appointment_id, $new_status, $nurse_id)) {
                $_SESSION['message'] = "Appointment status updated successfully";
            } else {
                $_SESSION['error'] = "Failed to update appointment status";
            }
        } else {
            $_SESSION['error'] = "Missing required information";
        }
    }
    
    // Redirect back to dashboard
    header('Location: nurse_dashboard.php');
    exit();
}