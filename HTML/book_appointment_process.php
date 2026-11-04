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

    if (empty($registration) || empty($brand) || empty($model) || empty($year) || empty($appointment_date) || empty($appointment_time)) {
        echo "<script>alert('Please fill in all required fields!'); window.history.back();</script>";
        exit();
    }
    $stmt = $conn->prepare("INSERT INTO appointment (user_id, vehicle_registration_number, brand, model, manufacture_year, date, time, additional_note) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssss", $user_id, $registration, $brand, $model, $year, $appointment_date, $appointment_time, $additional_note);

    if ($stmt->execute()) {
        echo "<script>alert('Appointment booked successfully!'); window.location.href='book_appointment.php';</script>";
    } else {
        echo "<script>alert('Something went wrong. Please try again!'); window.history.back();</script>";
    }

    $stmt->close();
}
$conn->close();
?>
