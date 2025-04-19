<?php
// Initialize the session
session_start();

// Unset all the session variables
$_SESSION = array();

// End the session
session_destroy();

// Redirect to login page
echo "<script>window.location.href = '../login.php';</script>";
exit();
?>
