<?php
session_start();
include '../eclinic_database.php';

$database = new DatabaseConnection();
$conn = $database->getConnect();

$student_id = $_SESSION['user_id'];

$sql = "SELECT mh.medhistory_id, mh.log, mh.created_at, u.first_name, u.last_name 
        FROM medical_history mh
        JOIN users u ON mh.doctor_id = u.user_id
        WHERE mh.student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bindParam(1, $student_id);
$stmt->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical History</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<h3>Your Medical History</h3>

<table border="1">
    <tr>
        <th>Date</th>
        <th>Doctor Name</th>
        <th>Log</th>
    </tr>

    <?php
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $created_at = date('Y-m-d H:i:s', strtotime($row['created_at']));
        echo "<tr>
            <td>{$created_at}</td>
            <td>{$row['first_name']} {$row['last_name']}</td>
            <td>{$row['log']}</td>
        </tr>";
    }
    ?>
</table>

</body>
</html>
