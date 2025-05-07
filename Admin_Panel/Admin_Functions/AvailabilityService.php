<?php
// This file contains the logic for handling doctor availability schedules

class AvailabilityService {
    private $medDataService;
    
    public function __construct($medDataService) {
        $this->medDataService = $medDataService;
    }
    
    public function getScheduleData($selectedUserId, $weekStart, $weekEnd) {
        if ($selectedUserId === null || $selectedUserId === '' || $selectedUserId === 0) {
            // For "All Doctors" view, use the combined availability
            $schedule = $this->medDataService->getCombinedDoctorAvailability($weekStart, $weekEnd);
            $doctorAvailability = $this->getDoctorAvailabilityDetails($weekStart, $weekEnd);
            
            return [
                'schedule' => $schedule,
                'doctorAvailability' => $doctorAvailability
            ];
        } else {
            // For specific doctor view, use the existing method
            $availabilityData = $this->medDataService->getDoctorAvailabilityByWeek($selectedUserId, $weekStart, $weekEnd);
            
            // Define time slots
            $timeSlots = [
                '08:00:00' => '08:00 - 09:00',
                '09:00:00' => '09:00 - 10:00',
                '10:00:00' => '10:00 - 11:00',
                '11:00:00' => '11:00 - 12:00',
                '13:00:00' => '13:00 - 14:00',
                '14:00:00' => '14:00 - 15:00',
                '15:00:00' => '15:00 - 16:00'
            ];
            
            // Initialize schedule array with all time slots and days
            $schedule = [];
            foreach ($timeSlots as $startTime => $displayTime) {
                $schedule[$startTime] = [
                    'display' => $displayTime,
                    'Monday' => 'Unavailable',
                    'Tuesday' => 'Unavailable',
                    'Wednesday' => 'Unavailable',
                    'Thursday' => 'Unavailable',
                    'Friday' => 'Unavailable'
                ];
            }
            
            // Process availability data to handle time ranges
            foreach ($availabilityData as $slot) {
                $day = $slot['day_name'];
                $startTime = $slot['start_time'];
                $endTime = $slot['end_time'];
                $availableDate = $slot['available_date'];
            
                // Check if the date is in the past
                $isPastDate = strtotime($availableDate) < strtotime(date('Y-m-d'));
            
                // Convert start and end times to hours for comparison
                $startHour = (int)substr($startTime, 0, 2);
                $endHour = (int)substr($endTime, 0, 2);
            
                // Mark all time slots between start and end time as available
                foreach ($timeSlots as $slotTime => $slotDisplay) {
                    $slotHour = (int)substr($slotTime, 0, 2);
                
                    // Check if this slot is within the availability range
                    if ($slotHour >= $startHour && $slotHour < $endHour) {
                        if ($isPastDate) {
                            $schedule[$slotTime][$day] = 'Completed';
                        } else {
                            $schedule[$slotTime][$day] = 'Available';
                        }
                    }
                }
            }
            
            return [
                'schedule' => $schedule,
                'doctorAvailability' => []
            ];
        }
    }
    
    private function getDoctorAvailabilityDetails($weekStart, $weekEnd) {
        $availabilityData = $this->medDataService->getDoctorAvailabilityByWeek(null, $weekStart, $weekEnd);
        
        // Store doctor availability details for the summary section
        $doctorAvailability = [];
        
        foreach ($availabilityData as $slot) {
            $day = $slot['day_name'];
            $startTime = $slot['start_time'];
            $endTime = $slot['end_time'];
            $availableDate = $slot['available_date'];
            $doctorName = $slot['doctor_name'];
            $doctorId = $slot['user_id'];
            $doctorRole = $slot['role'];
        
            // Check if the date is in the past
            $isPastDate = strtotime($availableDate) < strtotime(date('Y-m-d'));
            
            // Store doctor availability for summary
            if (!isset($doctorAvailability[$doctorId])) {
                $doctorAvailability[$doctorId] = [
                    'name' => $doctorName,
                    'role' => $doctorRole,
                    'days' => []
                ];
            }
            
            if (!isset($doctorAvailability[$doctorId]['days'][$day])) {
                $doctorAvailability[$doctorId]['days'][$day] = [];
            }
            
            $doctorAvailability[$doctorId]['days'][$day][] = [
                'date' => $availableDate,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => $isPastDate ? 'Completed' : 'Available'
            ];
        }
        
        return $doctorAvailability;
    }
    
    public function getStatusClass($status) {
        if (is_array($status)) {
            switch ($status['status']) {
                case 'Available':
                    return 'bg-success text-white';
                case 'Completed':
                    return 'bg-info text-white';
                case 'Unavailable':
                default:
                    return 'bg-danger text-white';
            }
        } else {
            switch ($status) {
                case 'Available':
                    return 'bg-success text-white';
                case 'Completed':
                    return 'bg-info text-white';
                case 'Unavailable':
                default:
                    return 'bg-danger text-white';
            }
        }
    }
    
    public function formatStatusCell($statusData) {
        if (is_array($statusData)) {
            $status = $statusData['status'];
            $count = isset($statusData['count']) ? $statusData['count'] : 0;
            
            if ($count > 0) {
                return $status ;
            } else {
                return $status;
            }
        } else {
            return $statusData;
        }
    }
    
    public function getDayStatusClass($status) {
        switch ($status) {
            case 'Available':
                return 'text-success';
            case 'Completed':
                return 'text-info';
            default:
                return 'text-danger';
        }
    }

    public function getPreviousWeek($currentWeek) {
        list($year, $week) = explode('-W', $currentWeek);
        $week = intval($week);
        $week--;
        
        if ($week < 1) {
            $year--;
            $week = 52; // Assuming 52 weeks in a year
        }
        
        return sprintf('%d-W%02d', $year, $week);
    }
    
    public function getNextWeek($currentWeek) {
        list($year, $week) = explode('-W', $currentWeek);
        $week = intval($week);
        $week++;
        
        if ($week > 52) {
            $year++;
            $week = 1;
        }
        
        return sprintf('%d-W%02d', $year, $week);
    }
}
?>