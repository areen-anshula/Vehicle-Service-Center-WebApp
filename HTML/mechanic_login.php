<?php
// Mechanic login -> redirects to mechanic.php
require_once __DIR__ . '/includes/auth.php';

$role       = 'mechanic';
$portalName = 'Mechanic';
$portalIcon = 'fa-wrench';
$subtitle   = 'Sign in to view and update your assigned jobs';

$error = handle_login($pdo, $role);

require __DIR__ . '/includes/login_layout.php';
