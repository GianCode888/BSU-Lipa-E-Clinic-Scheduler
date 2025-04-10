<?php
include('eclinic_database.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = htmlspecialchars($_POST['first_name']);
    $last_name = htmlspecialchars($_POST['last_name']);
    $email = htmlspecialchars($_POST['email']);
    $username = htmlspecialchars($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = htmlspecialchars($_POST['role']);

    // Create a new instance of DatabaseConnection
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
            echo "The email address is already in use.";
        } elseif ($existingUser['username'] == $username) {
            echo "The username is already in use.";
        }
    } else {
        // Proceed to insert the user into the database
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
            header("Location: login.php"); // Redirect to login page after successful signup
            exit();
        } else {
            echo "Error occurred while signing up.";
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
    <link rel="stylesheet" href="signup.css"> 
</head>
<body>
    <header>
        <h1>eClinic Scheduler</h1>
        <p>Manage your appointments with ease.</p>
    </header>
    
    <div class="form-container">
        <form method="POST" action="signup.php">
            <label for="first_name">First Name:</label>
            <input type="text" name="first_name" required>

            <label for="last_name">Last Name:</label>
            <input type="text" name="last_name" required>

            <label for="email">Email:</label>
            <input type="email" name="email" required>

            <label for="username">Username:</label>
            <input type="text" name="username" required>

            <label for="password">Password:</label>
            <input type="password" name="password" required>

            <label for="role">Role:</label>
            <select name="role" required>
                <option value="" disabled selected>Select role</option>
                <option value="student">Student</option>
                <option value="doctor">Doctor</option>
                <option value="nurse">Nurse</option>
            </select>

            <button type="submit" name="submit">Signup</button>
        </form>

        <div class="links">
            <p>Already have an account?</p><a href="login.php">Login</a>
        </div>
    </div>

</body>
</html>




