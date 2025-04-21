<?php
session_start();
require_once '../eclinic_database.php';

$database = new DatabaseConnection();
$conn = $database->getConnect();
$doctor_id = $_SESSION['user_id'];

class Doctor{
      private $conn;

      public function __construct($db) {
        $this->conn = $db;
      }

      public function student_appointment_request($doctor_id){
        $stmt = $this->conn->prepare("CALL StudentAppointmentRequest(:doctor_id)");
        $stmt->execute([':doctor_id'=>$doctor_id]);
        return $stmt;
      }
}
?>