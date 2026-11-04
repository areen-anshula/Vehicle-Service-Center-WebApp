<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include '../PHP/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id            = 1;
    $registration       = trim($_POST['registration']);
    $brand              = trim($_POST['brand']);
    $model              = trim($_POST['model']);
    $year               = trim($_POST['year']);
    $services           = isset($_POST['services']) ? implode(', ', $_POST['services']) : '';
    $appointment_date   = $_POST['appointment_date'];
    
    $raw_time           = $_POST['appointment_time'];
    $appointment_time   = date("H:i:s", strtotime($raw_time)); 

    $additional_note    = trim($_POST['additional_note']);
}
?>