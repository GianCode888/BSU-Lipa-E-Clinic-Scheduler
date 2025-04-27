<?php
include("../eclinic_database.php");
include("nurse_dashboard_crud.php");

$database = new DatabaseConnection();
$conn = $database->getConnect();
$nurseManager = new NurseManager($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student = $_POST['student'];
    $nurse = $_POST['nurse'];
    $details = $_POST['details'];
    $date = $_POST['date'];

    if ($nurseManager->addPatientLog($student, $nurse, $details, $date)) {
        echo "Log submitted successfully!";
    } else {
        echo "Error: Could not submit the log.";
    }
}
?>

<?php include('patient_log.html'); ?>
