<?php
session_start();
require_once 'medical_info.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_SESSION['user_id'])) {
        echo "You must be logged in to submit this form.";
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $blood_type = $_POST['blood_type'];
    $allergies = $_POST['allergies'];
    $med_condition = $_POST['med_condition'];
    $medications_taken = $_POST['medications_taken'];
    $emergency_contact_name = $_POST['emergency_contact_name'];
    $relationship = $_POST['relationship_to_student'];
    $contact_number = $_POST['contact_number'];
    $address = $_POST['address'];

    $medicalInfo = new MedicalInfo();
    $existing_info = $medicalInfo->getMedicalInfo($user_id);

    if ($existing_info) {
        $result = $medicalInfo->updateMedicalInfo(
            $user_id, $blood_type, $allergies, $med_condition,
            $medications_taken, $emergency_contact_name,
            $relationship, $contact_number, $address
        );
        $message = 'Medical information updated successfully!';
    } else {
        $result = $medicalInfo->addMedicalInfo(
            $user_id, $blood_type, $allergies, $med_condition,
            $medications_taken, $emergency_contact_name,
            $relationship, $contact_number, $address
        );
        $message = 'Medical information submitted successfully!';
    }
    

    if ($result) {
        echo "
        <html>
        <head>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
        <script>
        Swal.fire({
            title: 'Success!',
            text: '$message',
            icon: 'success'
        }).then((result) => {
            window.location.href = 'student_profile.php';
        });
        </script>
        </body>
        </html>";
    } else {
        echo "
        <html>
        <head>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
        <script>
        Swal.fire({
            title: 'Error!',
            text: 'There was an error processing your medical information.',
            icon: 'error'
        }).then((result) => {
            window.location.href = 'student_profile.php';
        });
        </script>
        </body>
        </html>";
    }
}
?>
