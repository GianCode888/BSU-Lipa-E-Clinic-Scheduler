<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'doctor') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor Dashboard</title>
</head>
<body>
    <h1>Welcome, Doctor!</h1>
    <p>Dashboard content for the doctor goes here.</p>
</body>
</html>
