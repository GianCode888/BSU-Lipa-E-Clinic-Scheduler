<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'nurse') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Nurse Dashboard</title>
</head>
<body>
    <h1>Welcome, Nurse!</h1>
    <p>Dashboard content for the nurse goes here.</p>
</body>
</html>
