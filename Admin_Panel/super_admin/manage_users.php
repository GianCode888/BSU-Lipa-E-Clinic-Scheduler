<?php
session_start();

// Check if user is logged in and has the correct role
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'medical_data_admin' && $_SESSION['role'] !== 'super_admin')) {
    header("Location: /login.php");
    exit();
}

include '../admin_sidebar.php';
require_once '../Admin_Functions/SuperAdminService.php';

// Initialize the service
$adminService = new SuperAdminService();

// Get all users
$users = $adminService->getAllUsers();

// Display success/error messages from redirects
$successMessage = '';
$errorMessage = '';

if (isset($_GET['success'])) {
    $action = $_GET['success'];
    if ($action === 'update') $successMessage = 'User updated successfully!';
    else if ($action === 'delete') $successMessage = 'User deleted successfully!';
}

if (isset($_GET['error'])) {
    $action = $_GET['error'];
    if ($action === 'update') $errorMessage = 'Error updating user!';
    else if ($action === 'delete') {
        $errorMessage = isset($_GET['message']) ? $_GET['message'] : 'Error deleting user!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../admin_styles.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.3/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.3/js/jquery.dataTables.min.js"></script>
</head>
<body>

<div class="main-content">
    <h1>Manage Users</h1>
    
    <?php if (!empty($successMessage)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $successMessage; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $errorMessage; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="d-flex justify-content-end align-items-center mb-4">
        <div class="d-flex gap-2">
            <select class="form-select" id="userTypeFilter">
                <option value="">All User Types</option>
                <option value="student">Student</option>
                <option value="doctor">Doctor</option>
                <option value="nurse">Nurse</option>
            </select>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <table id="usersTable" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Contact Number</th>
                        <th>Address</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['user_id']; ?></td>
                        <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo ucfirst(htmlspecialchars($user['role'])); ?></td>
                        <td><?php echo htmlspecialchars($user['contact_number'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($user['address'] ?? 'N/A'); ?></td>
                        <td><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                        <td>
                            <a href="update_user.php?id=<?php echo $user['user_id']; ?>" class="btn btn-sm btn-primary">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <button class="btn btn-sm btn-info view-user-btn"
                                    data-user-id="<?php echo $user['user_id']; ?>"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#viewUserModal">
                                <i class="bi bi-eye"></i> View
                            </button>
                            <a href="delete_user.php?id=<?php echo $user['user_id']; ?>" 
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Are you sure you want to delete this user?');">
                                <i class="bi bi-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- View User Modal -->
    <div class="modal fade" id="viewUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">User Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="user-details">
                        <p><strong>ID:</strong> <span id="viewUserId"></span></p>
                        <p><strong>Name:</strong> <span id="viewUserName"></span></p>
                        <p><strong>Email:</strong> <span id="viewUserEmail"></span></p>
                        <p><strong>Role:</strong> <span id="viewUserRole"></span></p>
                        <p><strong>Contact Number:</strong> <span id="viewUserContact"></span></p>
                        <p><strong>Address:</strong> <span id="viewUserAddress"></span></p>
                        <p><strong>Created At:</strong> <span id="viewUserCreatedAt"></span></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        var table = $('#usersTable').DataTable();
        
        $('#userTypeFilter').on('change', function() {
            var userType = $(this).val();
            
            // Custom filtering function
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    var typeMatch = userType === '' || data[3].toLowerCase() === userType.toLowerCase();
                    return typeMatch;
                }
            );
            
            table.draw();
            
            // Clear custom filtering function
            $.fn.dataTable.ext.search.pop();
        });
        
        // Handle view user button click
        $('.view-user-btn').on('click', function() {
            var userId = $(this).data('user-id');
            
            // Get user data from the table row
            var row = $(this).closest('tr');
            var id = row.find('td:eq(0)').text();
            var name = row.find('td:eq(1)').text();
            var email = row.find('td:eq(2)').text();
            var role = row.find('td:eq(3)').text();
            var contactNumber = row.find('td:eq(4)').text();
            var address = row.find('td:eq(5)').text();
            var createdAt = row.find('td:eq(6)').text();
            
            // Populate the view modal
            $('#viewUserId').text(id);
            $('#viewUserName').text(name);
            $('#viewUserEmail').text(email);
            $('#viewUserRole').text(role);
            $('#viewUserContact').text(contactNumber);
            $('#viewUserAddress').text(address);
            $('#viewUserCreatedAt').text(createdAt);
        });
    });
</script>
</body>
</html>
