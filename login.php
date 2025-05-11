<?php
session_start();
require_once 'eclinic_database.php';
require_once 'user.php';
include_once 'Admin_Panel/Admin_Functions/admin_service.php';
include_once 'Admin_Panel/Admin_Functions/notification_service.php';

$notificationService = new NotificationService();
$notification = '';
$error_message = '';
$login_type = isset($_GET['type']) ? $_GET['type'] : 'user';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Admin login
    if (isset($_POST['admin_login'])) {
        $email = htmlspecialchars($_POST['email']);
        $password = htmlspecialchars($_POST['password']);
        
        $adminService = new AdminService();    //instantiationnnnn
        $role = $adminService->getAdminRole($email, $password);

        if ($role !== null) {
            $_SESSION['role'] = $role;
            $_SESSION['email'] = $email;
            $_SESSION['login_notification'] = $notificationService->loginSuccess($role);
            
            if ($role === 'super_admin') {
                header("Location: Admin_Panel/super_admin/dashboard.php");
            } elseif ($role === 'care_admin') {
                header("Location: Admin_Panel/care_admin/dashboard.php");
            }
            exit();
        } else {
            $error_message = "Invalid admin credentials!";
            $notification = $notificationService->error('Login Failed', 'Invalid email or password. Please try again.');
        }
    } 
    // Regular user login
    elseif (isset($_POST['login'])) {
        $username = htmlspecialchars($_POST['username']);
        $password = htmlspecialchars($_POST['password']);

        $database = new DatabaseConnection();
        $conn = $database->getConnect();
        $userClass = new User($conn);
        $user = $userClass->authenticate($username, $password);

        if ($user) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role']; 
        
            if ($user['role'] == 'doctor') {
                header("Location: doctor_dashboard.php");
            } elseif ($user['role'] == 'nurse') {
                header("Location: nurse_dashboard.php");
            } elseif ($user['role'] == 'student') {
                header("Location: student_dashboard.php");
            }
            exit();
        } else {
            $error_message = "Invalid username or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ($login_type == 'admin') ? 'Admin Login' : 'Login'; ?> - eClinic Scheduler</title>
    <link rel="stylesheet" href="CSS/login.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<header>
    <img src="./Images/Spartan.png" alt="eClinic Logo">
    <h1>eClinic Scheduler</h1>
    <p>Manage your appointments with ease.</p>
</header>

<div class="form-container">
    <div class="login-toggle" style="text-align: center; margin-bottom: 15px;">
        <a href="login.php"
           style="margin-right: 10px; text-decoration: none; font-family: Arial, sans-serif; font-size: 16px; color: #333; <?php echo ($login_type != 'admin') ? 'font-weight: bold; color:rgb(255, 0, 0);' : ''; ?>">
            User Login
        </a> |
        <a href="login.php?type=admin"
           style="margin-left: 10px; text-decoration: none; font-family: Arial, sans-serif; font-size: 16px; color: #333; <?php echo ($login_type == 'admin') ? 'font-weight: bold; color:rgb(255, 0, 0);' : ''; ?>">
            Admin Login
        </a>
    </div>

    <?php if ($login_type == 'admin'): ?>
        <!-- Admin Login Form -->
        <form method="POST" action="login.php?type=admin">
            <?php if (!empty($error_message) && isset($_POST['admin_login'])): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <label for="email">Email:</label>
            <input type="text" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" name="admin_login">Login</button>
        </form>
    <?php else: ?>
        <!-- User Login Form -->
        <form method="POST" action="login.php">
            <?php if (!empty($error_message) && isset($_POST['login'])): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" name="login">Login</button>

            <div class="links">
                <p>Don't have an account? <a href="signup.php">Sign up here!</a></p>
            </div>
        </form>
    <?php endif; ?>
</div>

<footer>
    &copy; <?php echo date('Y'); ?> Spartan eClinic Scheduler
</footer>

<?php echo $notification; ?>

</body>
</html>
