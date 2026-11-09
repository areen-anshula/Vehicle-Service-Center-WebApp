
<?php
// login_process.php

session_start();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Connect to the AutoPulse database
    $conn = new mysqli("localhost", "root", "", "autopulse");
    $conn->set_charset("utf8mb4");

    // Get submitted login details
    $email = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
        header("Location: login.php?error=empty");
        exit();
    }

    // Fetch the matching user from the users table
    $stmt = $conn->prepare(
        "SELECT id, full_name, email, password, role
         FROM users
         WHERE email = ?
         LIMIT 1"
    );

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Check whether the user exists and the password is correct
    if (!$user || !password_verify($password, $user["password"])) {
        header("Location: login.php?error=invalid");
        exit();
    }

    // Validate the user's role
    $role = strtolower(trim($user["role"]));

    $redirect_pages = [
    "admin"        => "admin/dashboard.php",
    "mechanic"     => "mechanic/dashboard.php",
    "receptionist" => "receptionist/dashboard.php",
    "client"       => "homepage.php"
];

    if (!isset($redirect_pages[$role])) {
        header("Location: login.php?error=invalid");
        exit();
    }

    // Create a fresh session after successful login
    session_regenerate_id(true);

    $_SESSION["user_id"] = $user["id"];
    $_SESSION["full_name"] = $user["full_name"];
    $_SESSION["email"] = $user["email"];
    $_SESSION["role"] = $role;

    // Close database resources
    $stmt->close();
    $conn->close();

    // Redirect according to the user's role
    header("Location: " . $redirect_pages[$role]);
    exit();

} catch (mysqli_sql_exception $e) {
    error_log("Login database error: " . $e->getMessage());
    http_response_code(500);
    exit("A database error occurred. Please check your database connection and users table.");
}
