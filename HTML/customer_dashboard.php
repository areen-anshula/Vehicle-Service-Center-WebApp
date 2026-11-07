<?php
require_once __DIR__ . '/includes/auth.php';
require_role('customer');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoPulse | Customer Dashboard</title>
    <script src="https:
    <link rel="stylesheet" href="https:
</head>
<body class="min-h-screen bg-gray-100">
    <header class="bg-gray-950 text-white">
        <div class="flex h-16 items-center justify-between px-4 sm:px-8">
            <h1 class="text-xl font-bold">Auto<span class="text-[#E89000]">Pulse</span> <span class="ml-2 text-sm font-normal text-gray-400">Customer</span></h1>
            <a href="logout.php" class="flex items-center gap-2 rounded-lg bg-[#FFA500] px-4 py-2 text-sm font-semibold hover:bg-[#E89000]">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        </div>
    </header>
    <main class="p-4 sm:p-8">
        <h2 class="text-2xl font-bold text-gray-900">Welcome, <span class="text-[#E89000]"><?= e($_SESSION['full_name']) ?></span></h2>
        <p class="mt-2 text-gray-500">Customer dashboard - vehicles, appointments and invoices will appear here.</p>
    </main>
</body>
</html>
