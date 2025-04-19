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
    <?php elseif ($_SESSION['role'] === 'medical_data_admin'): ?>
        <p>Welcome!<br>Medical Data Admin</p>
    <?php elseif ($_SESSION['role'] === 'operation_admin'): ?>
        <p>Welcome!<br>Operation Admin</p>
    <?php endif; ?>

    <!-- Super Admin  -->
    <?php if ($_SESSION['role'] === 'super_admin'): ?>

        <button onclick="location.href='../super_admin/dashboard.php'"><i class="bi bi-house-door"></i> Dashboard</button>
        <button onclick="location.href='../super_admin/manage_users.php'"><i class="bi bi-person-check"></i> Manage Users</button>

        <button id="medicalRecordsBtn"><i class="bi bi-file-earmark-medical"></i> Medical Records</button>
        
        <!-- Medical Records Sub-buttons -->
        <div id="medicalRecordsSubMenu" class="submenu">
            <button onclick="location.href='../med_data_admin/dashboard.php'" class="submenu-button"><i class="bi bi-house-door"></i> Med Dashboard</button>
            <button onclick="location.href='../med_data_admin/view_medical_history.php'" class="submenu-button"><i class="bi bi-file-earmark-text"></i> Medical History</button>
            <button onclick="location.href='../med_data_admin/handle_medication_requests.php'" class="submenu-button"><i class="bi bi-pills"></i> Medication Requests</button>
        </div>

        <button id="appointmentManagementBtn"><i class="bi bi-calendar-check"></i> Appointment Management</button>
        
        <!-- Appointment Management Sub-buttons  -->
        <div id="appointmentManagementSubMenu" class="submenu">
            <button onclick="location.href='../operation_admin/dashboard.php'" class="submenu-button"><i class="bi bi-house-door"></i> Appointment Dashboard</button>
            <button onclick="location.href='../operation_admin/view_appointments.php'" class="submenu-button"><i class="bi bi-calendar"></i> Appointments</button>
            <button onclick="location.href='../operation_admin/availability_schedule.php'" class="submenu-button"><i class="bi bi-person-lines-fill"></i> Doctor Availability</button>
        </div>
    <?php endif; ?>

    <!-- Medical Data Admin -->
    <?php if ($_SESSION['role'] === 'medical_data_admin'): ?>
        <button onclick="location.href='../med_data_admin/dashboard.php'"><i class="bi bi-house-door"></i> Dashboard</button>
        <button onclick="location.href='../med_data_admin/view_medical_history.php'"><i class="bi bi-file-earmark-text"></i> Medical History</button>
        <button onclick="location.href='../med_data_admin/handle_medication_requests.php'"><i class="bi bi-pills"></i> Medication Requests</button>
        <button onclick="location.href='../med_data_admin/reports_generator.php'"><i class="bi bi-file-earmark-bar-graph"></i> Health Reports</button>
    <?php endif; ?>

    <!-- Operation Admin -->
    <?php if ($_SESSION['role'] === 'operation_admin'): ?>
        <button onclick="location.href='../operation_admin/dashboard.php'"><i class="bi bi-house-door"></i> Dashboard</button>
        <button onclick="location.href='../operation_admin/view_appointments.php'"><i class="bi bi-calendar"></i> Appointments</button>
        <button onclick="location.href='../operation_admin/availability_schedule.php'"><i class="bi bi-person-lines-fill"></i> Doctor Availability</button>
    <?php endif; ?>

    <button class="logout" onclick="location.href='../logout.php'"><i class="bi bi-box-arrow-right"></i> LOGOUT</button>
</div>

<?php if ($_SESSION['role'] === 'super_admin'): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if we're in the medical records section
        const currentPath = window.location.pathname;
        const inMedicalRecords = currentPath.includes('/med_data_admin/');
        const inAppointmentManagement = currentPath.includes('/operation_admin/');
        
        // Show the appropriate submenu if we're already in that section
        if (inMedicalRecords) {
            document.getElementById('medicalRecordsSubMenu').style.display = 'block';
        }
        
        if (inAppointmentManagement) {
            document.getElementById('appointmentManagementSubMenu').style.display = 'block';
        }
        
        // Medical Records button click handler
        document.getElementById('medicalRecordsBtn').addEventListener('click', function(e) {
            e.preventDefault(); // Prevent default action
            
            // If we're already in the medical records section, just toggle the submenu
            if (inMedicalRecords) {
                const subMenu = document.getElementById('medicalRecordsSubMenu');
                subMenu.style.display = subMenu.style.display === 'none' ? 'block' : 'none';
                return;
            }
            
            Swal.fire({
                title: 'Access Medical Records',
                text: "You are about to access the Medical Records section. This area contains sensitive patient information.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745', 
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, proceed',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Navigate to the medical records dashboard
                    window.location.href = '../med_data_admin/dashboard.php';
                }
            });
        });
        
        // Appointment Management button click handler
        document.getElementById('appointmentManagementBtn').addEventListener('click', function(e) {
            e.preventDefault(); // Prevent default action
            
            // If we're already in the appointment management section, just toggle the submenu
            if (inAppointmentManagement) {
                const subMenu = document.getElementById('appointmentManagementSubMenu');
                subMenu.style.display = subMenu.style.display === 'none' ? 'block' : 'none';
                return;
            }
            
            Swal.fire({
                title: 'Access Appointment Management',
                text: "You are about to access the Appointment Management section. This area contains scheduling and patient appointment information.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745', 
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, proceed',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Navigate to the appointment management dashboard
                    window.location.href = '../operation_admin/dashboard.php';
                }
            });
        });
    });
</script>
<?php endif; ?>
<?php endif; ?>
