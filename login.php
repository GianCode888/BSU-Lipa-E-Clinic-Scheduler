<?php
session_start();
include('eclinic_database.php');


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST['username']);  
    $password = htmlspecialchars($_POST['password']);

    if ($username == 'admin' && $password == 'admin123') {
        $_SESSION['user_id'] = 'admin'; 
        $_SESSION['role'] = 'admin';  
        
        header("Location: admin_dashboard.php");
        exit();
    }

    $database = new DatabaseConnection();
    $conn = $database->getConnect();

    // Check if user exists by username
    $query = "SELECT * FROM users WHERE username = :username";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
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
        $error_message = "Invalid username or password!";
    }  
}  
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/login.css">
    <title>Login</title>

</head>
<body>
    <header>
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
                <p>Don't Have An Account?</p>
                <a href="signup.php" id="signUpLink">Sign Up</a>
            </div>
        </form>
    </div>

</body>
</html>
