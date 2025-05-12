<?php

require_once '../../eclinic_database.php'; 
require_once 'ErrorHandler.php';

class SuperAdminService {
    private $conn;
    private $errorHandler;

    public function __construct() {
        $db = new DatabaseConnection();
        $this->conn = $db->getConnect();
        $this->errorHandler = new ErrorHandler();
    }

    public function getDashboardStats() {
        $stmt = $this->conn->prepare("CALL GetDashboardStats()");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getRecentAppointments($limit) {
        $stmt = $this->conn->prepare("CALL GetRecentAppointments(:limit)");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getRecentMedicalRecords($limit) {
        $stmt = $this->conn->prepare("CALL GetRecentMedicalRecords(:limit)");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    


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

   public function updateUser($userId, $firstName, $lastName, $email, $userType) {
    $originalUser = $this->getUserById($userId); 
    $this->errorHandler->clearErrors();

    // Validate that the user exists
    if (!$this->errorHandler->validateUserExists($originalUser)) {
        return false;
    }

    // for validation
    $newUserData = [
        'first_name' => $firstName,
        'last_name' => $lastName,
        'email' => $email,
        'role' => $userType
    ];

    if (!$this->errorHandler->validateUserData($newUserData)) {
        return false;
    }

    if (!$this->errorHandler->validateChanges($originalUser, $newUserData)) {
        return false;
    }

    $this->conn->query("SET @success = 0");

    $stmt = $this->conn->prepare("CALL UpdateUser(:id, :fname, :lname, :email, :type, @success)");
    $stmt->bindParam(':id', $userId);
    $stmt->bindParam(':fname', $firstName);
    $stmt->bindParam(':lname', $lastName);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':type', $userType);
    $stmt->execute();

    // success status
    $result = $this->conn->query("SELECT @success AS success");
    $success = $result->fetch(PDO::FETCH_ASSOC)['success'];

    return $success;
}


    public function deleteUser($userId) {
    $this->conn->query("SET @success = 0");
    $stmt = $this->conn->prepare("CALL DeleteUser(:id, @success)");
    $stmt->bindParam(':id', $userId);
    $stmt->execute();
    
    $result = $this->conn->query("SELECT @success AS success");
    return $result->fetch(PDO::FETCH_ASSOC)['success'];
}
    
 
    
    // Get the error handler instance
    public function getErrorHandler() {
        return $this->errorHandler;
    }
}
?>
