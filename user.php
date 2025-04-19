<?php
class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function isDuplicate($email, $username) {
        try {
            $stmt = $this->conn->prepare("CALL CheckDuplicate(:email, :username)");
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

    public function authenticate($username, $password) {
        try {
            $stmt = $this->conn->prepare("CALL LoginUser(:username, :password)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                return $user;
            } else {
                return false;
            }

        } catch (PDOException $e) {
            die("Error authenticating: " . $e->getMessage());
        }
    }
}
?>
