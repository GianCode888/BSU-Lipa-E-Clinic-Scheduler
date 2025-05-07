<?php
include '../eclinic_database.php';
include 'nurse_dashboard_crud.php';

$database = new DatabaseConnection();
$db = $database->getConnect();
$nurseManager = new NurseManager($db);

// Handle delete request
if (isset($_GET['id'])) {
    $log_id = $_GET['id'];

    if ($nurseManager->deleteCompletedLogs($log_id)) {
        echo "<script>alert('Request deleted successfully!'); window.location.href='view_completed_logs.php';</script>";
    } else {
        echo "<script>alert('Failed to delete request.');</script>";
    }
} else {
    echo "<script>alert('No log ID provided.'); window.location.href='view_completed_logs.php';</script>";
}
?>