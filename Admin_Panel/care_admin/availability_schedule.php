<?php
session_start();

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'care_admin' && $_SESSION['role'] !== 'super_admin')) {
    header("Location: /login.php");
    exit();
}

include '../admin_sidebar.php';
require_once '../Admin_Functions/MedDataService.php';
require_once '../Admin_Functions/AvailabilityService.php';

$medDataService = new MedDataService();
$availabilityService = new AvailabilityService($medDataService);

$totalDoctors = $medDataService->getTotalDoctors();
$availableDoctors = $medDataService->getAvailableDoctors();
$doctors = $medDataService->getAllDoctors();

// Process filter form submission
$selectedUserId = isset($_GET['user_id']) ? $_GET['user_id'] : null;
$selectedWeek = isset($_GET['week']) ? $_GET['week'] : date('Y-\WW');

// Parse the week string to get start and end dates
list($year, $week) = explode('-W', $selectedWeek);
$weekStart = date('Y-m-d', strtotime($year . 'W' . $week . '1')); // Monday
$weekEnd = date('Y-m-d', strtotime($year . 'W' . $week . '5'));   // Friday

$scheduleData = $availabilityService->getScheduleData($selectedUserId, $weekStart, $weekEnd);
$schedule = $scheduleData['schedule'];
$doctorAvailability = $scheduleData['doctorAvailability'];

$prevWeek = $availabilityService->getPreviousWeek($selectedWeek);
$nextWeek = $availabilityService->getNextWeek($selectedWeek);

$doctorName = '';
if ($selectedUserId) {
    foreach ($doctors as $doctor) {
        if ($doctor['user_id'] == $selectedUserId) {
            $doctorName = $doctor['first_name'] . ' ' . $doctor['last_name'];
            break;
        }
    }
}

// Get the day names for the selected week
$dayNames = [];
$dayDates = [];
for ($i = 0; $i < 5; $i++) {
    $date = date('Y-m-d', strtotime($weekStart . ' +' . $i . ' days'));
    $dayNames[] = date('l', strtotime($date));
    $dayDates[] = date('M d', strtotime($date));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Availability</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../admin_styles.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.3/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.3/js/jquery.dataTables.min.js"></script>

</head>
<body>

<div class="main-content">
    <h1>Doctor Availability Schedule</h1>
    
    <div class="row mb-5">
        <div class="col-md-4">
            <!-- Filter Schedule Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Filter Schedule</h5>
                    <form method="GET" action="">
                        <div class="mb-3">
                            <label for="doctorSelect" class="form-label">Select Doctor</label>
                            <select class="form-select" id="doctorSelect" name="user_id">
                                <option value="">All Doctors</option>
                                <?php foreach ($doctors as $doctor): ?>
                                <option value="<?php echo $doctor['user_id']; ?>" <?php echo ($selectedUserId == $doctor['user_id']) ? 'selected' : ''; ?>>
                                    <?php echo $doctor['first_name'] . ' ' . $doctor['last_name']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="weekSelect" class="form-label">Week</label>
                            <div class="input-group">
                                <input type="week" class="form-control" id="weekSelect" name="week" value="<?php echo $selectedWeek; ?>">
                                <a href="?user_id=<?php echo $selectedUserId; ?>&week=<?php echo $prevWeek; ?>" class="btn btn-outline-secondary">&lt;</a>
                                <a href="?user_id=<?php echo $selectedUserId; ?>&week=<?php echo $nextWeek; ?>" class="btn btn-outline-secondary">&gt;</a>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">View Schedule</button>
                    </form>
                </div>
            </div>
            
            <!-- Stats Cards -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card bg-primary text-white mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Total Doctors</h5>
                            <div class="card-stats"><h4><?php echo $totalDoctors; ?></h4></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card bg-success text-white mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Available Doctors</h5>
                            <div class="card-stats"><h4><?php echo $availableDoctors; ?></h4></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <!-- Availability Table Card -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title">
                            Availability for Week: <?php echo date('M d', strtotime($weekStart)); ?> - <?php echo date('M d, Y', strtotime($weekEnd)); ?>
                            <?php if ($doctorName): ?>
                                <small class="text-muted">(<?php echo $doctorName; ?>)</small>
                            <?php endif; ?>
                        </h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="availabilityTable">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                    <th><?php echo $dayNames[$i]; ?><br><small><?php echo $dayDates[$i]; ?></small></th>
                                    <?php endfor; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($schedule as $timeSlot): ?>
                                <tr>
                                    <td><?php echo $timeSlot['display']; ?></td>
                                    <td class="<?php echo $availabilityService->getStatusClass($timeSlot['Monday']); ?>" 
                                        <?php if (!$selectedUserId && isset($timeSlot['Monday']['doctors']) && !empty($timeSlot['Monday']['doctors'])): ?>
                                        title="<?php echo htmlspecialchars($timeSlot['Monday']['doctors']); ?>"
                                        data-bs-toggle="tooltip"
                                        <?php endif; ?>>
                                        <?php echo $availabilityService->formatStatusCell($timeSlot['Monday']); ?>
                                    </td>
                                    <td class="<?php echo $availabilityService->getStatusClass($timeSlot['Tuesday']); ?>"
                                        <?php if (!$selectedUserId && isset($timeSlot['Tuesday']['doctors']) && !empty($timeSlot['Tuesday']['doctors'])): ?>
                                        title="<?php echo htmlspecialchars($timeSlot['Tuesday']['doctors']); ?>"
                                        data-bs-toggle="tooltip"
                                        <?php endif; ?>>
                                        <?php echo $availabilityService->formatStatusCell($timeSlot['Tuesday']); ?>
                                    </td>
                                    <td class="<?php echo $availabilityService->getStatusClass($timeSlot['Wednesday']); ?>"
                                        <?php if (!$selectedUserId && isset($timeSlot['Wednesday']['doctors']) && !empty($timeSlot['Wednesday']['doctors'])): ?>
                                        title="<?php echo htmlspecialchars($timeSlot['Wednesday']['doctors']); ?>"
                                        data-bs-toggle="tooltip"
                                        <?php endif; ?>>
                                        <?php echo $availabilityService->formatStatusCell($timeSlot['Wednesday']); ?>
                                    </td>
                                    <td class="<?php echo $availabilityService->getStatusClass($timeSlot['Thursday']); ?>"
                                        <?php if (!$selectedUserId && isset($timeSlot['Thursday']['doctors']) && !empty($timeSlot['Thursday']['doctors'])): ?>
                                        title="<?php echo htmlspecialchars($timeSlot['Thursday']['doctors']); ?>"
                                        data-bs-toggle="tooltip"
                                        <?php endif; ?>>
                                        <?php echo $availabilityService->formatStatusCell($timeSlot['Thursday']); ?>
                                    </td>
                                    <td class="<?php echo $availabilityService->getStatusClass($timeSlot['Friday']); ?>"
                                        <?php if (!$selectedUserId && isset($timeSlot['Friday']['doctors']) && !empty($timeSlot['Friday']['doctors'])): ?>
                                        title="<?php echo htmlspecialchars($timeSlot['Friday']['doctors']); ?>"
                                        data-bs-toggle="tooltip"
                                        <?php endif; ?>>
                                        <?php echo $availabilityService->formatStatusCell($timeSlot['Friday']); ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        <h6>Status:</h6>
                        <div class="d-flex flex-wrap gap-3">
                            <div>
                                <span class="badge bg-success">Available</span> - Doctor is available for appointments
                            </div>
                            <div>
                                <span class="badge bg-info">Completed</span> - Past appointment or availability
                            </div>
                            <div>
                                <span class="badge bg-danger">Unavailable</span> - Doctor is not available
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Detailed Doctor Availability Section - Centered and Compact -->
    <?php if (!$selectedUserId && count($doctorAvailability) > 0): ?>
    <div class="doctor-detail-containers">
        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title text-center mb-4">Detailed Doctor Availability</h5>
                <div class="row">
                    <?php foreach ($doctorAvailability as $doctorId => $doctor): ?>
                    <div class="col-md-6">
                        <div class="card doctor-availability-cards">
                            <div class="card-body">
                                <h4 class="card-title"><?php echo $doctor['name']; ?></h4>
                                <?php foreach ($dayNames as $index => $day): ?>
                                    <div class="day-schedule">
                                        <h6><?php echo $day; ?> (<?php echo $dayDates[$index]; ?>):</h6>
                                        <?php if (isset($doctor['days'][$day]) && !empty($doctor['days'][$day])): ?>
                                            <?php foreach ($doctor['days'][$day] as $timeSlot): ?>
                                                <div class="time-slot">
                                                    <span class="status-badge <?php echo $timeSlot['status'] == 'Available' ? 'status-available' : 'status-completed'; ?>"></span>
                                                    <?php echo substr($timeSlot['start_time'], 0, 5); ?> - <?php echo substr($timeSlot['end_time'], 0, 5); ?>
                                                    <small class="<?php echo $availabilityService->getDayStatusClass($timeSlot['status']); ?>">
                                                        (<?php echo $timeSlot['status']; ?>)
                                                    </small>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="time-slot">
                                                <span class="status-badge status-unavailable"></span>
                                                <span class="text-danger">Not Available</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            html: true
        })
    })
    
    function exportToCSV() {
        // Get the table
        const table = document.getElementById('availabilityTable');
        let csv = [];
        
        // Get all rows
        const rows = table.querySelectorAll('tr');
        
        // Loop through rows
        for (let i = 0; i < rows.length; i++) {
            const row = [], cols = rows[i].querySelectorAll('td, th');
            
            // Loop through columns
            for (let j = 0; j < cols.length; j++) {
                // Get the text content and replace any commas with spaces
                let data = cols[j].textContent.replace(/,/g, ' ');
                // Wrap with quotes
                row.push('"' + data + '"');
            }
            
            csv.push(row.join(','));
        }
        
        // Combine into a single string with line breaks
        const csvString = csv.join('\n');
        
    }
</script>
</body>
</html>
