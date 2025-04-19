<?php

require_once '../../eclinic_database.php'; 

class SuperAdminService {
    private $conn;

    public function __construct() {
        $db = new DatabaseConnection();
        $this->conn = $db->getConnect();
    }

    // Dashboard methods
    public function getDashboardStats() {
        $stmt = $this->conn->prepare("CALL GetDashboardStats()");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getRecentActivity($limit) {
        $stmt = $this->conn->prepare("CALL GetRecentActivity(:limit)");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // User management methods
    public function getAllUsers() {
        $stmt = $this->conn->prepare("CALL GetAllUsers()");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById($userId) {
        $stmt = $this->conn->prepare("CALL GetUserById(:id)");
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateUser($userId, $firstName, $lastName, $email, $userType, $address, $contactNumber) {
        $stmt = $this->conn->prepare("CALL UpdateUser(:id, :fname, :lname, :email, :type, :address, :contact, @success)");
        $stmt->bindParam(':id', $userId);
        $stmt->bindParam(':fname', $firstName);
        $stmt->bindParam(':lname', $lastName);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':type', $userType);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':contact', $contactNumber);
        $stmt->execute();

        $result = $this->conn->query("SELECT @success AS success");
        return $result->fetch(PDO::FETCH_ASSOC)['success'];
    }

    public function deleteUser($userId) {
        $stmt = $this->conn->prepare("CALL DeleteUser(:id, @success)");
        $stmt->bindParam(':id', $userId);
        $stmt->execute();

        $result = $this->conn->query("SELECT @success AS success");
        return $result->fetch(PDO::FETCH_ASSOC)['success'];
    }
    
    // Add these new methods
    public function getAppointmentsByStatus($status) {
        $stmt = $this->conn->prepare("CALL GetAppointmentsByStatus(:status)");
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMedicationRequestsByStatus($status) {
        $stmt = $this->conn->prepare("CALL GetMedicationRequestsByStatus(:status)");
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>