<?php
class Register {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function isDuplicate($email, $username) {
        try {
            $stmt = $this->conn->prepare("CALL check_duplicate(:email, :username)");
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $result;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function create($first_name, $last_name, $email, $username, $password, $role) {
        try {
            $stmt = $this->conn->prepare("CALL CreateUser(:first_name, :last_name, :email, :username, :password, :role)");
            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':role', $role);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>
