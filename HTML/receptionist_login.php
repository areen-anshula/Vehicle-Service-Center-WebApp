<?php

/*
// Receptionist login -> redirects to receptionist_dashboard.php
require_once __DIR__ . '/includes/auth.php';

$role       = 'receptionist';
$portalName = 'Receptionist';
$portalIcon = 'fa-headset';
$subtitle   = 'Sign in to manage appointments, job cards and invoices';

$error = handle_login($pdo, $role);

require __DIR__ . '/includes/login_layout.php';
