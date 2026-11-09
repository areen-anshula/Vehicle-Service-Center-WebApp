<?php
/**
 * Shared authentication helpers for all four login pages.
 *
 * Every role logs in through its own page, but the checking logic lives
 * here so it is written (and fixed) only once.
 */

require_once __DIR__ . '/../admin/db_connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

/**
 * Where each role lands after a successful login.
 * Paths are relative to the HTML/ folder.
 */
const ROLE_HOME = [
    'customer'     => 'customer_dashboard.php',
    'receptionist' => 'receptionist_dashboard.php',
    'mechanic'     => 'mechanic.php',
    'admin'        => 'admin/dashboard.php',
];

/** Login page of each role, relative to the HTML/ folder. */
const ROLE_LOGIN = [
    'customer'     => 'customer_login.php',
    'receptionist' => 'receptionist_login.php',
    'mechanic'     => 'mechanic_login.php',
    'admin'        => 'admin/admin_login.php',
];

/** Builds a URL to a page in HTML/ from wherever the current script is. */
function app_url(string $path): string
{
    $inAdmin = basename(dirname($_SERVER['SCRIPT_FILENAME'])) === 'admin';
    return ($inAdmin ? '../' : '') . $path;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Handles a POSTed login form for one role.
 * Redirects on success; returns an error message on failure (or null on GET).
 */
function handle_login(PDO $pdo, string $role): ?string
{
    // Already logged in with this role? Go straight to the dashboard.
    if (($_SESSION['role'] ?? null) === $role) {
        header('Location: ' . app_url(ROLE_HOME[$role]));
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return null;
    }

    if (!hash_equals(csrf_token(), $_POST['csrf_token'] ?? '')) {
        return 'Your session expired. Please try again.';
    }

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        return 'Please enter your email and password.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Please enter a valid email address.';
    }

    try {
        $stmt = $pdo->prepare(
            'SELECT user_id, full_name, email, password, role, status
               FROM users
              WHERE email = ? AND role = ?
              LIMIT 1'
        );
        $stmt->execute([$email, $role]);
        $user = $stmt->fetch();
    } catch (PDOException $e) {
        error_log($e->getMessage());
        return 'Something went wrong. Please try again later.';
    }

    // Same message for "no such user" and "wrong password" so attackers
    // cannot find out which emails are registered.
    if (!$user || !password_verify($password, $user['password'])) {
        return 'Incorrect email or password.';
    }

    if ($user['status'] !== 'active') {
        return 'Your account is inactive. Please contact the service center.';
    }

    // Prevent session fixation, then store the logged-in user.
    session_regenerate_id(true);
    $_SESSION['user_id']   = (int) $user['user_id'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['email']     = $user['email'];
    $_SESSION['role']      = $user['role'];

    header('Location: ' . app_url(ROLE_HOME[$role]));
    exit;
}

/**
 * Put at the top of every protected page, e.g. require_role('mechanic');
 * Sends anyone who is not logged in with that role to its login page.
 */
function require_role(string $role): void
{
    if (($_SESSION['role'] ?? null) !== $role) {
        header('Location: ' . app_url(ROLE_LOGIN[$role]));
        exit;
    }
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
