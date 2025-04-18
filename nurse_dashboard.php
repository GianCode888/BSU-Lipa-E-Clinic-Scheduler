<?php
session_start();
include("eclinic_database.php");

// Check if user is logged in and is a nurse
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'nurse') {
    header('Location: login.php');
    exit();
}

$database = new DatabaseConnection();
$conn = $database->getConnect();

// Get the nurse's information
$query = "SELECT first_name, last_name FROM users WHERE user_id = :user_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']); // Fix parameter name and use session variable
$stmt->execute();
$nurse = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="nurse_dashboard.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nurse Dashboard - eClinic</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>eClinic Scheduler</h1>
        <p>Welcome, Nurse <?php echo $nurse['first_name'] . ' ' . $nurse['last_name']; ?></p>
        <nav>
            <ul>
                <li><a href="nurse_dashboard.php">Dashboard</a></li>
                <li><a href="medication_requests.php">Medication Requests</a></li>
                <li><a href="completed_medications.php">Completed Medications</a></li>
                <li> <a href="patient_log.php">Patient Logs</a></li>

                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <section class="dashboard-summary">
            <h2>Dashboard Summary</h2>
            <div class="dashboard-cards">
                <div class="card">
                    <h3>Pending Dispensing</h3>
                    <?php
                    // Count medication requests pending dispensing
                    $query = "SELECT COUNT(*) FROM medication_requests WHERE status = 'approved'";
                    $stmt = $conn->query($query);
                    $pendingDispensing = $stmt->fetchColumn();
                    ?>
                    <p class="count"><?php echo $pendingDispensing; ?></p>
                    <a href="medication_requests.php" class="btn">View Requests</a>
                </div>
                
                <div class="card">
                    <h3>Today's Appointments</h3>
                    <?php
                    // Count today's appointments for this nurse
                    $today = date('Y-m-d');
                    $query = "SELECT COUNT(*) FROM appointments 
                              WHERE nurse_id = :nurse_id AND 
                              DATE(appointment_date) = :today";
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':nurse_id', $_SESSION['user_id']);
                    $stmt->bindParam(':today', $today);
                    $stmt->execute();
                    $todayAppointments = $stmt->fetchColumn();
                    ?>
                    <p class="count"><?php echo $todayAppointments; ?></p>
                    <a href="appointments.php" class="btn">View Appointments</a>
                </div>
            </div>
        </section>
        
        <section class="recent-activity">
            <h2>Recent Activity</h2>
            <table>
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Get recent appointments for this nurse
                    $query = "SELECT a.appointment_id, a.reason, a.status, a.appointment_date, 
                              u.first_name, u.last_name 
                              FROM appointments a
                              JOIN users u ON a.student_id = u.user_id
                              WHERE a.nurse_id = :nurse_id
                              ORDER BY a.appointment_date DESC LIMIT 10";
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':nurse_id', $_SESSION['user_id']);
                    $stmt->execute();
                    $recentActivity = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (count($recentActivity) > 0) {
                        foreach ($recentActivity as $activity) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($activity['first_name']) . " " . htmlspecialchars($activity['last_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($activity['reason']) . "</td>";
                            echo "<td>" . ucfirst(htmlspecialchars($activity['status'])) . "</td>";
                            echo "<td>" . date('M d, Y H:i', strtotime($activity['appointment_date'])) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No recent appointments found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </main>
    
    <footer>
        <p>&copy; <?php echo date('Y'); ?> eClinic Scheduler</p>
    </footer>
</body>
</html>