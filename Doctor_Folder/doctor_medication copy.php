<?php
// Include your database connection
require_once '../eclinic_database.php';

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if doctor is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Get doctor ID
$doctor_id = $_SESSION['user_id'];

// Message variable
$medicationMessage = '';

$medications = [];

try {
    $db = new DatabaseConnection();
    $conn = $db->getConnect();

    // ✅ Using the routine call format you wanted
    $stmt = $conn->prepare("CALL ViewDoctorMedicationsByDoctor(:doctor_id)");
    $stmt->execute([':doctor_id' => $doctor_id]);
    $medications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor(); // ✅ Always after CALL

} catch (PDOException $e) {
    die("Error fetching medications: " . $e->getMessage());
}

// Function to display medications
function showMedications($medications)
{
    echo "<div id='medications' class='section'>";
    echo "<h2>💊 Your Prescribed Medications</h2>";

    if (empty($medications)) {
        echo "<p>No medications found.</p>";
    } else {
        echo "<table border='1' cellpadding='8'>";
        echo "<tr>
                <th>Student Name</th>
                <th>Medication</th>
                <th>Dosage</th>
                <th>Date Prescribed</th>
              </tr>";
        foreach ($medications as $med) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($med['student_name']) . "</td>";
            echo "<td>" . htmlspecialchars($med['medication']) . "</td>";
            echo "<td>" . htmlspecialchars($med['dosage']) . "</td>";
            echo "<td>" . htmlspecialchars($med['date_prescribed']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    echo "</div>";
}

// Display the medications
showMedications($medications);
?>
    