<?php
class UserDetails {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getUserDetails($userId) {
        try {
            $stmt = $this->conn->prepare("CALL GetUserDetails(:uid)");
            $stmt->bindParam(':uid', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor(); 
            return $user;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    }
}
?>
