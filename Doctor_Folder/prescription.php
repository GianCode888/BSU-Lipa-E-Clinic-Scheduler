<?php
include '../eclinic_database.php';
include '../Doctor_Folder/doctor_crud.php';

$database = new DatabaseConnection();
$conn = $database->getConnect();
$doctor = new Doctor($conn);

$approved_students = $doctor->get_approved_students();
$students_with_prescriptions = $doctor->get_students_with_prescriptions();
$prescribed_user_ids = array_column($students_with_prescriptions, 'user_id');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Prescription Orders</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="../CSS/prescription.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#approvedStudentsTable').DataTable();
        });
    </script>
</head>
<body>
<button type="button" onclick="window.location.href='../doctor_dashboard.php'">Home</button>

    <h2>Approved Students for Prescription Orders</h2>

    <?php if (!empty($approved_students)): ?>
        <table id="approvedStudentsTable" class="display">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($approved_students as $student): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['student_name']); ?></td>
                        <td>
                            <?php if (in_array($student['user_id'], $prescribed_user_ids)): ?>
                                <span>Provided Prescription</span>
                            <?php else: ?>
                                <form action="prescription_form.php" method="POST">
                                    <input type="hidden" name="student_user_id" value="<?php echo htmlspecialchars($student['user_id']); ?>">
                                    <button type="submit">Give Prescription</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No approved students found for prescription.</p>
    <?php endif; ?>

</body>
</html>
