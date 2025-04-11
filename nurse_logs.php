<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $log = $_POST['log'];
    $nurse_id = 1; // sample nurse ID

    $stmt = $conn->prepare("INSERT INTO medical_history (student_id, log, doctor_id) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $student_id, $log, $nurse_id);
    $stmt->execute();
}

$result = $conn->query("SELECT user_id, first_name, last_name FROM users WHERE role='student'");
?>

<h2>Add Patient Log</h2>
<form method="post">
    Student:
    <select name="student_id">
        <?php while($row = $result->fetch_assoc()): ?>
            <option value="<?= $row['user_id'] ?>"><?= $row['first_name'] . ' ' . $row['last_name'] ?></option>
        <?php endwhile; ?>
    </select><br><br>
    Notes:<br>
    <textarea name="log" rows="4" cols="50"></textarea><br><br>
    <input type="submit" value="Add Log">
</form>
