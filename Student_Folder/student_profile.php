<?php
require_once '../eclinic_database.php';
require_once 'student_serverside.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$database = new DatabaseConnection();
$db = $database->getConnect();
$userId = $_SESSION['user_id'];

$student = new Student($db);
$userData = $student->getUserDetails($userId);
echo '<link rel="stylesheet" href="../CSS/student_profile.css">';

if ($userData) {
    echo '<button type="button" onclick="window.location.href=\'../student_dashboard.php\'">Home</button>';
} else {
    echo "User not found.";
    exit();
}

$medical_info = $student->getMedicalInfo($userId);

echo "<hr>";

if ($medical_info) {
    echo "<h2>Basic Health Information</h2>";
} else {
    $medical_info = [
        'blood_type' => '',
        'allergies' => '',
        'med_condition' => '',
        'medications_taken' => '',
        'emergency_contact_name' => '',
        'relationship_to_student' => '',
        'contact_number' => '',
        'address' => ''
    ];
    echo "<h2>Please complete your Basic Health Record</h2>";
}
?>

<div class="main-container">
    <!-- Left side: Profile Information -->
    <div class="left-side card">
        <h3>Your Profile</h3>
        <p><strong>User ID:</strong> <?= htmlspecialchars($userData['user_id']) ?></p>
        <p><strong>Username:</strong> <?= htmlspecialchars($userData['username']) ?></p>
        <p><strong>Name:</strong> <?= htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($userData['email']) ?></p>
        <p><strong>Joined on:</strong> <?= $userData['created_at'] ?></p>
        <a href="doctor_prescription.php"><button class="btn-primary">View Doctor Prescription</button></a>
    </div>

    <!-- Right side: Health Record Form -->
    <div class="right-side card">
        <form action="student_serverside.php" method="POST">
            <label for="blood_type">Blood Type:</label>
            <input type="text" name="blood_type" value="<?= htmlspecialchars($medical_info['blood_type']) ?>" required>
            <br>

            <label for="allergies">Allergies (medications, food, etc.):</label>
            <textarea name="allergies" required><?= htmlspecialchars($medical_info['allergies']) ?></textarea>
            <br>

            <label for="med_condition">Medical Conditions (e.g., asthma, diabetes):</label>
            <textarea name="med_condition" required><?= htmlspecialchars($medical_info['med_condition']) ?></textarea>
            <br>

            <label for="medications_taken">Medications Currently Taken:</label>
            <textarea name="medications_taken" required><?= htmlspecialchars($medical_info['medications_taken']) ?></textarea>
            <br>

            <label for="emergency_contact_name">Emergency Contact Name:</label>
            <input type="text" name="emergency_contact_name" value="<?= htmlspecialchars($medical_info['emergency_contact_name']) ?>" required>
            <br>

            <label for="relationship_to_student">Relationship to Student:</label>
            <input type="text" name="relationship_to_student" value="<?= htmlspecialchars($medical_info['relationship_to_student']) ?>" required>
            <br>

            <label for="contact_number">Contact Number:</label>
            <input type="tel" name="contact_number" pattern="[0-9]{11}" value="<?= htmlspecialchars($medical_info['contact_number']) ?>" required>
            <br>

            <label for="address">Address:</label>
            <textarea name="address" required><?= htmlspecialchars($medical_info['address']) ?></textarea>
            <br>

            <input type="submit" value="Submit Health Record" class="btn-primary">
        </form>
    </div>
</div>
