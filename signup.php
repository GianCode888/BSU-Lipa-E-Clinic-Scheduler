<?php
include('eclinic_database.php');

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = htmlspecialchars($_POST['first_name']);
    $last_name = htmlspecialchars($_POST['last_name']);
    $email = htmlspecialchars($_POST['email']);
    $username = htmlspecialchars($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = htmlspecialchars($_POST['role']);

    $database = new DatabaseConnection();
    $conn = $database->getConnect();

    // Check if email or username already exists
    $query = "SELECT * FROM users WHERE email = :email OR username = :username";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingUser) {
        if ($existingUser['email'] == $email) {
            $error_message = "The email address is already in use.";
        } elseif ($existingUser['username'] == $username) {
            $error_message = "The username is already in use.";
        }
    } else {
        $query = "INSERT INTO users (first_name, last_name, email, username, password, role) 
                  VALUES (:first_name, :last_name, :email, :username, :password, :role)";
        $stmt = $conn->prepare($query);

        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':role', $role);

        if ($stmt->execute()) {
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
    <title>Sign Up</title>
    <link rel="stylesheet" href="CSS/signup.css"> 
</head>
<body>
    <header>
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
            <p>Already have an account?</p><a href="login.php">Login</a>
        </div>
    </div>

</body>
</html>
