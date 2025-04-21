<?php
session_start();
require_once '../eclinic_database.php';

$database = new DatabaseConnection();
$conn = $database->getConnect();

class Doctor {
  private $conn;

  public function __construct($db) {
      $this->conn = $db;
  }

  public function student_appointment_request() {
          $stmt = $this->conn->prepare("CALL StudentAppointmentRequest()");
          $stmt->execute();
          return $stmt;
  }
}

?>
