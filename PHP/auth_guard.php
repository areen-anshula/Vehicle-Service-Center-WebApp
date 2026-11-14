
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$required_role = strtolower(trim($required_role ?? ''));
$login_pages = [
    "admin" => "admin/admin_login.php",
    "mechanic" => "mechanic_login.php",
    "receptionist" => "receptionist_login.php",
    "client" => "customer_login.php"
];
$script_name = str_replace("\\", "/", $_SERVER["SCRIPT_NAME"] ?? "");
$html_directory = dirname(dirname($script_name));
if (strtolower(basename(dirname($script_name))) === "php") {
    $html_directory .= "/HTML";
}
$login_url = rtrim($html_directory, "/") . "/" . ($login_pages[$required_role] ?? "login.php");

// Check login session
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["role"])) {
    header("Location: " . $login_url . "?error=unauthorized");
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

    header("Location: " . $login_url . "?error=invalid");
    exit();
}

// Check dashboard permission
if (!isset($required_role) ||
    $user_role !== $required_role) {
    http_response_code(403);
    exit("Access denied. You are not authorized to access this page.");
}
