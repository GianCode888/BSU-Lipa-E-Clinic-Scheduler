<?php
require_once 'eclinic_database.php';
require_once 'user.php';

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    $database = new DatabaseConnection();
    $conn = $database->getConnect();
    $userClass = new User($conn);
    $user = $userClass->authenticate($username, $password);

    if ($user) {
        session_start();
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - eClinic Scheduler</title>
    <link rel="stylesheet" href="CSS/login.css">
</head>
<body>

    <header>
        <img src=".//Images/Spartan.png" alt="eClinic Logo">
        <h1>eClinic Scheduler</h1>
        <p>Manage your appointments with ease.</p>
    </header>

    <div class="form-container">
        <form method="POST" action="login.php">
            <?php if (!empty($error_message)): ?>
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
    </div>

    <footer>
        &copy; <?php echo date('Y'); ?> Spartan eClinic Scheduler
    </footer>

</body>
</html>

