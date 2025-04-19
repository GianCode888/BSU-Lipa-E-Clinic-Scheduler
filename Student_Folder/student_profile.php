<?php
require_once '../eclinic_database.php';
require_once 'student_crud.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$database = new DatabaseConnection();
$db = $database->getConnect();
$userId = $_SESSION['user_id'];
$userDetails = new Student($db);
$stmt = $userDetails->getUserDetails($userId);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

if ($userData) {
    echo "<h2>Good day, " . htmlspecialchars($userData['name']) . "!</h2>";
    echo "<p>Welcome to your profile! Here's a quick look at your details:</p>";
    echo "<h3>Your Profile</h3>";
    echo "<p><strong>User ID:</strong> " . $userData['user_id'] . "</p>";
    echo "<p><strong>Username:</strong> " . htmlspecialchars($userData['username']) . "</p>";
    echo "<p><strong>Name:</strong> " . htmlspecialchars($userData['name']) . "</p>";
    echo "<p><strong>Email:</strong> " . htmlspecialchars($userData['email']) . "</p>";
    echo "<p><strong>Joined on:</strong> " . $userData['created_at'] . "</p>";
    echo '<a href="medical_history.php"><button>View Medical History</button></a>';
} else {
    echo "User not found.";
}
?>
