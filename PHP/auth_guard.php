
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check login session
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["role"])) {
    header("Location: ../HTML/login.php?error=unauthorized");
    exit();
}

// Validate role
$allowed_roles = [
    "admin",
    "mechanic",
    "receptionist",
    "client"
];

$user_role = strtolower(trim($_SESSION["role"]));

if (!in_array($user_role, $allowed_roles, true)) {
    session_unset();
    session_destroy();

    header("Location: ../HTML/login.php?error=invalid");
    exit();
}

// Check dashboard permission
if (!isset($required_role) ||
    $user_role !== strtolower($required_role)) {
    http_response_code(403);
    exit("Access denied. You are not authorized to access this page.");
}
