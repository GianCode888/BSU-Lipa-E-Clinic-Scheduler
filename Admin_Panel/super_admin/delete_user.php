<?php
session_start();

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'medical_data_admin' && $_SESSION['role'] !== 'super_admin')) {
    header("Location: /login.php");
    exit();
}

require_once '../Admin_Functions/SuperAdminService.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_users.php");
    exit();
}

$userId = $_GET['id'];
$adminService = new SuperAdminService();
$success = $adminService->deleteUser($userId);

if ($success) {
    header("Location: manage_users.php?success=delete");
    exit();
} else {
    header("Location: manage_users.php?error=delete&message=User cannot be deleted because they have associated records (appointments, medication requests, or availability schedules).");
    exit();
}
?>