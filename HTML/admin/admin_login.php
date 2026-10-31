<?php
// Admin / Owner login -> redirects to admin/dashboard.php
require_once __DIR__ . '/../includes/auth.php';

$role       = 'admin';
$portalName = 'Admin';
$portalIcon = 'fa-user-shield';
$subtitle   = 'Sign in to manage staff, services and inventory';

$error = handle_login($pdo, $role);

require __DIR__ . '/../includes/login_layout.php';
