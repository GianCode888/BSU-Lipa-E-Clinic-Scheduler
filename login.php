<?php
session_start();
include_once 'Admin_Panel/Admin_Functions/admin_service.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $adminService = new AdminService();
    $role = $adminService->getAdminRole($email, $password);

    if ($role !== null) {
        $_SESSION['role'] = $role;
        $_SESSION['email'] = $email;
        
        // Redirect based on role
        if ($role === 'super_admin') {
            header("Location: Admin_Panel/super_admin/dashboard.php");
        } elseif ($role === 'medical_data_admin') {
            header("Location: Admin_Panel/med_data_admin/dashboard.php");
        } elseif ($role === 'operation_admin') {
            header("Location: Admin_Panel/operation_admin/dashboard.php");
        }
        exit();
    } else {
        $error = "Invalid credentials!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BSU-LIPA E-Clinic Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 30px;
            width: 100%;
            max-width: 400px;
        }
        .login-logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #28a745;
            border-color: #28a745;
            width: 100%;
        }
        .btn-primary:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-logo">
            <h2>BSU-LIPA E-Clinic</h2>
            <h4>Admin Login</h4>
        </div>
        
        <form method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Login</button>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger mt-3" role="alert">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
        </form>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
