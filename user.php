<?php
class Login {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
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
