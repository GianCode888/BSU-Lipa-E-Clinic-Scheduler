<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medication Requests - eClinic</title>
    <link rel="stylesheet" href="nurse_dashboard.css">
    <link rel="stylesheet" href="medication_requests.css">
    <script>
        // Simple inline JavaScript for toggling form visibility
        function toggleForm(formId) {
            const form = document.getElementById(formId);
            if (form.style.display === 'none') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }
    </script>
</head>
<body>
    <header>
        <h1>eClinic Scheduler</h1>
        <p>Welcome, Nurse <?php echo htmlspecialchars($nurse['first_name'] . ' ' . $nurse['last_name']); ?></p>
        <nav>
            <ul>
                <li><a href="nurse_dashboard.php">Dashboard</a></li>
                <li><a href="completed_medications.php">Completed Medications</a></li>
                <li><a href="patient_logs.php">Patient Notes</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <!-- Display success/error messages if any -->
        <?php if(isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <?php 
                    echo $_SESSION['message']; 
                    unset($_SESSION['message']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php 
                    echo $_SESSION['error']; 
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
        
        <section>
            <div class="section-header">
                <h2>Pending Medication Requests</h2>
                <a href="nurse_dashboard.php" class="btn">Back to Dashboard</a>
            </div>
            
            <?php if (count($pendingRequests) > 0): ?>
                <?php foreach ($pendingRequests as $request): ?>
                    <div class="medication-request">
                        <div class="request-header">
                            <h3>
                                Request #<?php echo $request['request_id']; ?> - 
                                <?php echo htmlspecialchars($request['first_name'] . ' ' . $request['last_name']); ?>
                            </h3>
                            <span class="status-tag status-<?php echo strtolower($request['status']); ?>">
                                <?php echo ucfirst($request['status']); ?>
                            </span>
                        </div>
                        
                        <div class="request-details">
                            <div class="detail-item">
                                <h4>Medication</h4>
                                <p><?php echo htmlspecialchars($request['medication_name'] ?? 'Not specified'); ?></p>
                            </div>
                            
                            <div class="detail-item">
                                <h4>Dosage</h4>
                                <p><?php echo htmlspecialchars($request['dosage'] ?? 'Not specified'); ?></p>
                            </div>
                            
                            <div class="detail-item">
                                <h4>Requested</h4>
                                <p><?php echo isset($request['created_at']) ? date('M d, Y H:i', strtotime($request['created_at'])) : 'Unknown'; ?></p>
                            </div>
                        </div>
                        
                        <div class="request-actions">
                            <button class="btn btn-small" onclick="toggleForm('form-<?php echo $request['request_id']; ?>')">
                                Process Request
                            </button>
                            
                            <form id="form-<?php echo $request['request_id']; ?>" 
                                  class="medication-form" 
                                  method="POST" 
                                  action="medication_requests.php" 
                                  style="display: none;">
                                
                                <input type="hidden" name="action" value="update_medication">
                                <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                                
                                <div class="form-group">
                                    <label for="notes-<?php echo $request['request_id']; ?>">Notes:</label>
                                    <textarea name="notes" id="notes-<?php echo $request['request_id']; ?>" rows="3"></textarea>
                                </div>
                                
                                <div class="action-buttons">
                                    <button type="submit" name="new_status" value="approved" class="btn btn-small btn-approve">
                                        Approve & Dispense
                                    </button>
                                    
                                    <button type="submit" name="new_status" value="rejected" class="btn btn-small btn-reject" 
                                            onclick="return confirm('Are you sure you want to reject this medication request?')">
                                        Reject Request
                                    </button>
                                </div>
                            </form>
                            
                            <a href="view_student.php?id=<?php echo $request['student_id']; ?>" class="btn-small">
                                View Student Records
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-results">
                    <p>No pending medication requests at this time.</p>
                </div>
            <?php endif; ?>
        </section>
        
        <section class="record-keeping-guidance">
            <h3>Record Keeping Guidelines</h3>
            <ul>
                <li>Keep accurate and up-to-date records of all student interactions and treatments</li>
                <li>Add notes during or after appointments</li>
                <li>Attach relevant documents or prescriptions</li>
                <li>All entries are timestamped with your ID for audit purposes</li>
                <li>Complete history and audit trails are maintained for compliance</li>
            </ul>
        </section>
    </main>
    
    <footer>
        <p>&copy; <?php echo date('Y'); ?> eClinic Scheduler</p>
    </footer>
</body>
</html>