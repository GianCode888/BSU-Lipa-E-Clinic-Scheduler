<?php
$user_id = $_POST['user_id'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Prescription Form</title>
    <link rel="stylesheet" href="../CSS/prescription_form.css">
</head>
<body>
    <h2>Prescription Form</h2>

    <form action="doctor_serverside.php" method="POST">
        <input type="hidden" name="student_user_id" value="<?php echo htmlspecialchars($_POST['student_user_id']); ?>">

        <label for="diagnosis">Diagnosis:</label>
        <textarea name="diagnosis" required></textarea>

        <label for="prescription">Prescription:</label>
        <textarea name="prescription" required></textarea>

        <button type="submit">Submit Prescription</button>
    </form>

</body>
</html>
