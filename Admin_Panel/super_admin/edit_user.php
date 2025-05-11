<?php
session_start();

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'medical_data_admin' && $_SESSION['role'] !== 'super_admin')) {
    header("Location: /login.php");
    exit();
}

require_once '../Admin_Functions/SuperAdminService.php';

$adminService = new SuperAdminService();
$errorHandler = $adminService->getErrorHandler();
$validationErrors = [];

// Check if user ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_users.php");
    exit();
}

$userId = $_GET['id'];
$user = $adminService->getUserById($userId);

if (!$user) {
    header("Location: manage_users.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $userId = $_POST['user_id'];
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    // Removed contact number
    $success = $adminService->updateUser($userId, $firstName, $lastName, $email, $role);

    if ($success) {
        header("Location: manage_users.php?success=update");
        exit();
    } else {
        $validationErrors = $adminService->getErrorHandler()->getErrors();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../admin_styles.css">
    <style>
        .invalid-feedback {
            display: block;
        }
    </style>
</head>
<body>

<?php include '../admin_sidebar.php'; ?>

<div class="main-content">
    <h1>Edit User</h1>
    
    <div class="card">
        <div class="card-body">
            <?php if (!empty($validationErrors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($validationErrors as $field => $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="edit_user.php?id=<?php echo $userId; ?>">
                <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control <?php echo isset($validationErrors['first_name']) ? 'is-invalid' : ''; ?>" 
                               id="first_name" name="first_name" 
                               value="<?php echo htmlspecialchars($_POST['first_name'] ?? $user['first_name']); ?>" required>
                        <?php if (isset($validationErrors['first_name'])): ?>
                            <div class="invalid-feedback"><?php echo htmlspecialchars($validationErrors['first_name']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control <?php echo isset($validationErrors['last_name']) ? 'is-invalid' : ''; ?>" 
                               id="last_name" name="last_name" 
                               value="<?php echo htmlspecialchars($_POST['last_name'] ?? $user['last_name']); ?>" required>
                        <?php if (isset($validationErrors['last_name'])): ?>
                            <div class="invalid-feedback"><?php echo htmlspecialchars($validationErrors['last_name']); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control <?php echo isset($validationErrors['email']) ? 'is-invalid' : ''; ?>" 
                               id="email" name="email" 
                               value="<?php echo htmlspecialchars($_POST['email'] ?? $user['email']); ?>" required>
                        <?php if (isset($validationErrors['email'])): ?>
                            <div class="invalid-feedback"><?php echo htmlspecialchars($validationErrors['email']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select <?php echo isset($validationErrors['role']) ? 'is-invalid' : ''; ?>" 
                                id="role" name="role" required>
                            <option value="student" <?php echo ($_POST['role'] ?? $user['role']) === 'student' ? 'selected' : ''; ?>>Student</option>
                            <option value="doctor" <?php echo ($_POST['role'] ?? $user['role']) === 'doctor' ? 'selected' : ''; ?>>Doctor</option>
                            <option value="nurse" <?php echo ($_POST['role'] ?? $user['role']) === 'nurse' ? 'selected' : ''; ?>>Nurse</option>
                        </select>
                        <?php if (isset($validationErrors['role'])): ?>
                            <div class="invalid-feedback"><?php echo htmlspecialchars($validationErrors['role']); ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Contact number removed -->

                <div class="d-flex justify-content-between">
                    <a href="manage_users.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

