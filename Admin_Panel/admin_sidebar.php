<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['role'])):
?>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../admin_styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<div class="sidebar">
    <div class="logo-container">
        <img src="../Batangas_State_Logo.png" alt="BSU Logo" class="sidebar-logo">
    </div>
    <h2>E-CLINIC</h2>

    <?php if ($_SESSION['role'] === 'super_admin'): ?>
        <p>Welcome!<br>Super Admin</p>
    <?php elseif ($_SESSION['role'] === 'care_admin'): ?>
        <p>Welcome!<br>Care Admin</p>
    <?php endif; ?>

    <!-- Super Admin  -->
    <?php if ($_SESSION['role'] === 'super_admin'): ?>

        <button onclick="location.href='../super_admin/dashboard.php'"><i class="bi bi-house-door"></i> Dashboard</button>
        <button onclick="location.href='../super_admin/manage_users.php'"><i class="bi bi-person-check"></i> Manage Users</button>

        <button id="careAdminBtn"><i class="bi bi-heart-pulse"></i> Care Administration</button>
        
        <!-- Care Admin Sub-buttons -->
        <div id="careAdminSubMenu" class="submenu">
            <button onclick="location.href='../care_admin/dashboard.php'" class="submenu-button"><i class="bi bi-house-door"></i> Care Dashboard</button>
            <button onclick="location.href='../care_admin/view_medical_history.php'" class="submenu-button"><i class="bi bi-file-earmark-text"></i> Medical History</button>
            <button onclick="location.href='../care_admin/view_appointments.php'" class="submenu-button"><i class="bi bi-calendar"></i> Appointments</button>
            <button onclick="location.href='../care_admin/availability_schedule.php'" class="submenu-button"><i class="bi bi-person-lines-fill"></i> Doctor Availability</button>
        </div>
    <?php endif; ?>

    <!-- Care Admin -->
    <?php if ($_SESSION['role'] === 'care_admin'): ?>
        <button onclick="location.href='../care_admin/dashboard.php'"><i class="bi bi-house-door"></i> Dashboard</button>
        <button onclick="location.href='../care_admin/view_medical_history.php'"><i class="bi bi-file-earmark-text"></i> Medical History</button>
        <button onclick="location.href='../care_admin/view_appointments.php'"><i class="bi bi-calendar"></i> Appointments</button>
        <button onclick="location.href='../care_admin/availability_schedule.php'"><i class="bi bi-person-lines-fill"></i> Doctor Availability</button>
    <?php endif; ?>

    <button class="logout" onclick="location.href='../logout.php'"><i class="bi bi-box-arrow-right"></i> LOGOUT</button>
</div>

<?php if ($_SESSION['role'] === 'super_admin'): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if we're in the care admin section
        const currentPath = window.location.pathname;
        const inCareAdmin = currentPath.includes('/care_admin/');
        
        // Show the appropriate submenu if we're already in that section
        if (inCareAdmin) {
            document.getElementById('careAdminSubMenu').style.display = 'block';
        }
        
        // Care Admin button click handler
        document.getElementById('careAdminBtn').addEventListener('click', function(e) {
            e.preventDefault(); // Prevent default action
            
            // If we're already in the care admin section, just toggle the submenu
            if (inCareAdmin) {
                const subMenu = document.getElementById('careAdminSubMenu');
                subMenu.style.display = subMenu.style.display === 'none' ? 'block' : 'none';
                return;
            }
            
            Swal.fire({
                title: 'Access Care Administration',
                text: "You are about to access the Care Administration section. This area contains sensitive patient information and appointment scheduling.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745', 
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, proceed',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Navigate to the care admin dashboard
                    window.location.href = '../care_admin/dashboard.php';
                }
            });
        });
    });
</script>
<?php endif; ?>
<?php endif; ?>