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

// Profile info
$userData = $student->getUserDetails($userId);

if ($userData) {
    echo '<button type="button" onclick="history.back()">Home</button>';
    echo "<h2>Good day, " . htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']) . "!</h2>";
    echo "<p>This section provides a comprehensive view of your personal details, basic health information, and medical history within the school. You can review and update any necessary details to ensure your health records are accurate and up to date.</p>";
    echo "<h3>Your Profile</h3>";
    echo "<p><strong>User ID:</strong> " . $userData['user_id'] . "</p>";
    echo "<p><strong>Username:</strong> " . htmlspecialchars($userData['username']) . "</p>";
    echo "<p><strong>Name:</strong> " . htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']) . "</p>";
    echo "<p><strong>Email:</strong> " . htmlspecialchars($userData['email']) . "</p>";
    echo "<p><strong>Joined on:</strong> " . $userData['created_at'] . "</p>";
    echo '<a href="medical_history.php"><button>View Medical History</button></a>';
} else {
    echo "User not found.";
    exit();
}

// Medical info
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

    <input type="submit" value="Submit Health Record">
</form>
