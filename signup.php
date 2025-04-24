<?php
session_start();
require_once 'eclinic_database.php'; 
require_once 'user.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = htmlspecialchars($_POST['first_name']);
    $last_name = htmlspecialchars($_POST['last_name']);
    $email = htmlspecialchars($_POST['email']);
    $username = htmlspecialchars($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = htmlspecialchars($_POST['role']);

    $databaseConnection = new DatabaseConnection();
    $conn = $databaseConnection->getConnect(); 

    $userClass = new User($conn);

    $existingUser = $userClass->isDuplicate($email, $username);

    if ($existingUser) {
        $error_message = "The email or username is already in use.";
    } else {
        if ($userClass->create($first_name, $last_name, $email, $username, $password, $role)) {
            header("Location: login.php");
            exit();
        } else {
            $error_message = "Error occurred while signing up.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<<<<<<< HEAD
    <title>Sign up - eClinic Scheduler</title>
    <link rel="stylesheet" href="CSS/signup.css">
=======
<<<<<<< HEAD
    <title>Sign Up</title>
    <link rel="stylesheet" href="signup.css"> 
=======
    <title>Sign up - eClinic Scheduler</title>
    <link rel="stylesheet" href="CSS/signup.css">
>>>>>>> aac356d49dff9a1dff7c8b4211ddb319cb451d5f
>>>>>>> 03505b4a75b1c764354b7343e0c0426bd04c9f10
</head>
<body>

<div class="wrapper">
    <div class="content">
        <header>
            <img src=".//Images/Spartan.png" alt="eClinic Logo" style="max-width: 120px;">
            <h1>eClinic Scheduler</h1>
            <p>Manage your appointments with ease.</p>
        </header>

        <div class="form-container">
            <form method="POST" action="signup.php">

                <?php if (!empty($error_message)): ?>
                    <div class="error-message"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <label for="first_name">First Name:</label>
                <input type="text" name="first_name" value="<?php echo isset($first_name) ? htmlspecialchars($first_name) : ''; ?>" required>

                <label for="last_name">Last Name:</label>
                <input type="text" name="last_name" value="<?php echo isset($last_name) ? htmlspecialchars($last_name) : ''; ?>" required>

                <label for="email">Email:</label>
                <input type="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>

                <label for="username">Username:</label>
                <input type="text" name="username" value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" required>

                <label for="password">Password:</label>
                <input type="password" name="password" required>

                <label for="role">Role:</label>
                <select name="role" required>
                    <option value="" disabled <?php echo !isset($role) ? 'selected' : ''; ?>>Select role</option>
                    <option value="student" <?php echo (isset($role) && $role == 'student') ? 'selected' : ''; ?>>Student</option>
                    <option value="doctor" <?php echo (isset($role) && $role == 'doctor') ? 'selected' : ''; ?>>Doctor</option>
                    <option value="nurse" <?php echo (isset($role) && $role == 'nurse') ? 'selected' : ''; ?>>Nurse</option>
                </select>

                <button type="submit" name="submit">Signup</button>
            </form>

            <div class="links">
                <p>Already have an account?</p><a href="login.php">Log in now!</a>
            </div>
        </div>
    </div>
</div>

<footer>
    <p>&copy; <?php echo date("Y"); ?> Spartan eClinic Scheduler</p>
</footer>

</body>
</html>
