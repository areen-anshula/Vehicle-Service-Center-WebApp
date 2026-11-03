<?php
session_start();
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name        = trim($_POST['fullname']);
    $email            = trim($_POST['email']);
    $phone            = trim($_POST['phone']);
    $password         = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

   // Check if Password and Confirm Password match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        exit();
}
//Check if an account already exists with this email address
$check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo "<script>alert('An account with this email already exists!'); window.history.back();</script>";
        $check_stmt->close();
        exit();
    }
    $check_stmt->close();

    //Hash the password (for security)
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
}
?>