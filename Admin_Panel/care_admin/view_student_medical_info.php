<?php
session_start();

require_once '../Admin_Functions/MedDataService.php';

// Get user_id from query parameter
$userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($userId > 0) {
    $medDataService = new MedDataService();
    $medicalInfo = $medDataService->getStudentMedicalInfo($userId);
    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Medical Information</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container p-3">
        <?php if (isset($medicalInfo) && $medicalInfo): ?>
            <div class="mb-3">
                <label class="form-label"><strong>User ID:</strong></label>
                <p><?php echo htmlspecialchars($medicalInfo['user_id'] ?? 'N/A'); ?></p>
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Blood Type:</strong></label>
                <p><?php echo htmlspecialchars($medicalInfo['blood_type'] ?? 'N/A'); ?></p>
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Allergies:</strong></label>
                <p><?php echo htmlspecialchars($medicalInfo['allergies'] ?? 'N/A'); ?></p>
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Medical Condition:</strong></label>
                <p><?php echo htmlspecialchars($medicalInfo['med_condition'] ?? 'N/A'); ?></p>
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Medications Currently Taken:</strong></label>
                <p><?php echo htmlspecialchars($medicalInfo['medications_taken'] ?? 'N/A'); ?></p>
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Emergency Contact Name:</strong></label>
                <p><?php echo htmlspecialchars($medicalInfo['emergency_contact_name'] ?? 'N/A'); ?></p>
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Relationship to Student:</strong></label>
                <p><?php echo htmlspecialchars($medicalInfo['relationship_to_student'] ?? 'N/A'); ?></p>
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Contact Number:</strong></label>
                <p><?php echo htmlspecialchars($medicalInfo['contact_number'] ?? 'N/A'); ?></p>
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Address:</strong></label>
                <p><?php echo htmlspecialchars($medicalInfo['address'] ?? 'N/A'); ?></p>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                No medical information found for this student (User ID: <?php echo $userId; ?>).
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
