<?php
// Logs out any role and sends the user back to that role's login page.
require_once __DIR__ . '/includes/auth.php';

$loginPage = ROLE_LOGIN[$_SESSION['role'] ?? 'customer'] ?? ROLE_LOGIN['customer'];

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
session_destroy();

header('Location: ' . $loginPage);
exit;
