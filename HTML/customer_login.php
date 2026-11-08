<?php
// Customer login -> redirects to customer_dashboard.php
require_once __DIR__ . '/includes/auth.php';

$role       = 'customer';
$portalName = 'Customer';
$portalIcon = 'fa-user';
$subtitle   = 'Sign in to book services and track your vehicle';

$error = handle_login($pdo, $role);

require __DIR__ . '/includes/login_layout.php';
