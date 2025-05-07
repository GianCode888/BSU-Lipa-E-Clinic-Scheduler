<?php
include('../eclinic_database.php');
include("Nurse_Folder/nurse_dashboard_crud.php");

$nurse_id = $_SESSION['user_id'];
$database = new DatabaseConnection();
$nurseManager = new NurseManager($database->getConnect());

// Get nurse information

$nurseManager = new NurseManager($pdo);
$allRequests = $nurseManager->student_appointment_request();

foreach ($allRequests as $request) {
    echo $request['request_type'] . ' - ' . $request['student_name'] . ' - ' . $request['status'] . '<br>';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medication Requests - eClinic</title>
    <link rel="stylesheet" href="../Nurse_Folder/nurse_dashboard.css">
    <link rel="stylesheet" href="../Nurse_Folder/medication_requests.css">
    <script>
        function toggleForm(formId) {
            const form = document.getElementById(formId);
            if (form.style.display === 'none') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }
    </script>
</head>
<body>
    <header>
        <h1>eClinic Scheduler</h1>
        <p>Welcome, Nurse <?php echo htmlspecialchars($nurse['first_name'] . ' ' . $nurse['last_name']); ?></p>
        <nav>
            <ul>
                <li><a href="Nurse_Folder/approved_request_list">Approved List</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    
