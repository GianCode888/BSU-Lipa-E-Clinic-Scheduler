<?php
class Doctor {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // 1. View Appointments
    public function getAppointments($filter = '', $sort = '') {
        $query = "SELECT * FROM appointments";

        if (!empty($filter)) {
            $query .= " WHERE student_name LIKE :filter OR status LIKE :filter";
        }

        if (!empty($sort)) {
            $query .= " ORDER BY $sort";
        } else {
            $query .= " ORDER BY appointment_time ASC";
        }

        $stmt = $this->conn->prepare($query);
        if (!empty($filter)) {
            $filter = "%$filter%";
            $stmt->bindParam(':filter', $filter);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Approve/Decline Appointment
    public function updateAppointmentStatus($id, $status, $comment = '') {
        $query = "UPDATE appointments SET status = :status, comment = :comment WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':comment', $comment);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // 3. View Medication Requests
    public function getMedicationRequests() {
        $query = "SELECT * FROM medication_requests ORDER BY request_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 4. Approve/Decline Medication Request
    public function updateMedicationStatus($id, $status, $signature = null) {
        $query = "UPDATE medication_requests SET status = :status, digital_signature = :signature WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':signature', $signature);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // 5. Set Doctor Availability
    public function setAvailability($doctor_id, $day, $start_time, $end_time) {
        $query = "INSERT INTO doctor_schedule (doctor_id, day, start_time, end_time) 
                  VALUES (:doctor_id, :day, :start_time, :end_time)
                  ON DUPLICATE KEY UPDATE start_time = :start_time_update, end_time = :end_time_update";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':doctor_id', $doctor_id);
        $stmt->bindParam(':day', $day);
        $stmt->bindParam(':start_time', $start_time);
        $stmt->bindParam(':end_time', $end_time);
        $stmt->bindParam(':start_time_update', $start_time);
        $stmt->bindParam(':end_time_update', $end_time);
        return $stmt->execute();
    }

    // 6. View Student Medical History
    public function getStudentHistory($student_id) {
        $query = "SELECT * FROM student_medical_history WHERE student_id = :student_id ORDER BY visit_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':student_id', $student_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
