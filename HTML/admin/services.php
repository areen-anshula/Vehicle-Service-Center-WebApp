<?php
// ========================================
// SERVICE MANAGEMENT - DATABASE CONNECTION
// ========================================
$required_role = "admin";
require_once "../../PHP/auth_guard.php";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Generate CSRF token for service management actions
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

try {
    // Connect to AutoPulse database
    $conn = new mysqli("localhost", "root", "", "autopulse");
    $conn->set_charset("utf8mb4");

    // Get all service statistics
    $statsQuery = "
        SELECT
            COUNT(*) AS total_services,
            SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) AS active_services,
            SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) AS inactive_services,
            COUNT(DISTINCT service_type) AS total_categories
        FROM services
    ";

    $statsResult = $conn->query($statsQuery);
    $serviceStats = $statsResult->fetch_assoc();

    // Store the statistics
    $totalServices = (int) $serviceStats['total_services'];
    $activeServices = (int) $serviceStats['active_services'];
    $inactiveServices = (int) $serviceStats['inactive_services'];
    $totalCategories = (int) $serviceStats['total_categories'];

} catch (mysqli_sql_exception $e) {
    error_log("Service statistics error: " . $e->getMessage());
    die("Unable to load service statistics. Please check the database connection.");
}

// Get only available (active) services
$availableServicesQuery = "
    SELECT
        id,
        service_name,
        service_type,
        description,
        price,
        estimate_duration
    FROM services
    WHERE is_active = 1
    ORDER BY id DESC
";

$availableServicesResult = $conn->query($availableServicesQuery);

$availableServicesCount = $availableServicesResult->num_rows;


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoPulse | Service Management</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4.3.3"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css">
</head>

<body class="min-h-screen bg-[#F5F5F5] font-poppins text-gray-800">
    <div class="flex min-h-screen">

        <!-- SIDEBAR -->
        <aside
            class="fixed left-0 top-0 z-50 hidden h-screen w-64 flex-col overflow-y-auto bg-gray-950 text-white shadow-xl lg:flex">

            <div class="border-b border-white/10 px-5 py-5">
                <a href="../homepage.php" class="flex items-center gap-3">
                    <img src="../../MEDIA/AutoPulse Automotive Service Logo.png" alt="AutoPulse Logo"
                        class="h-15 w-15 rounded-lg object-contain">
                    <div>
                        <h1 class="text-xl font-bold tracking-tight">Auto<span class="text-[#E89000]">Pulse</span></h1>
                        <p class="mt-0.5 text-[10px] tracking-wide text-gray-400">VEHICLE SERVICE CENTER</p>
                    </div>
                </a>
            </div>

            <div class="px-5 pb-2 pt-6">
                <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-500">Main Menu</p>
            </div>

            <nav class="flex-1 px-3">
                <ul class="space-y-1">

                    <!-- Dashboard -->
                    <li>
                        <a href="dashboard.php"
                            class="group flex items-center gap-3 justify-between rounded-lg px-4 py-3 text-xs font-medium text-gray-300 transition duration-200 hover:bg-white/10 hover:text-white">
                            <span class="flex items-center gap-3">
                                <i
                                    class="fa-solid fa-table-columns w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>
                                Dashboard
                            </span>
                            <i class="fa-solid fa-chevron-right text-[9px] text-gray-500"></i>
                        </a>
                    </li>

                    <!-- User Management -->
                    <li>
                        <a href="userManagement.php"
                            class="group flex items-center justify-between rounded-lg px-4 py-3 text-xs font-medium text-gray-300 transition duration-200 hover:bg-white/10 hover:text-white">
                            <span class="flex items-center gap-3">
                                <i
                                    class="fa-solid fa-users w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>
                                User Management
                            </span>
                            <i class="fa-solid fa-chevron-right text-[9px] text-gray-500"></i>
                        </a>
                    </li>

                    <!-- Services - Active -->
                    <li>
                        <a href="services.php"
                            class="flex items-center justify-between rounded-lg bg-[#E89000] px-4 py-3 text-xs font-semibold text-white shadow-sm">
                            <span class="flex items-center gap-3">
                                <i class="fa-solid fa-screwdriver-wrench w-4 text-center"></i>
                                Services
                            </span>
                            <i class="fa-solid fa-chevron-right text-[9px]"></i>
                        </a>
                    </li>

                    <!-- Inventory -->
                    <li>
                        <a href="inventory.php"
                            class="group flex items-center justify-between rounded-lg px-4 py-3 text-xs font-medium text-gray-300 transition duration-200 hover:bg-white/10 hover:text-white">
                            <span class="flex items-center gap-3">
                                <i
                                    class="fa-solid fa-boxes-stacked w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>
                                Inventory
                            </span>
                            <i class="fa-solid fa-chevron-right text-[9px] text-gray-500"></i>
                        </a>
                    </li>
                    <!-- Reports -->
                    <li>
                        <a href="reports.html"
                            class="group flex items-center justify-between rounded-lg px-4 py-3 text-xs font-medium text-gray-300 transition duration-200 hover:bg-white/10 hover:text-white">
                            <span class="flex items-center gap-3">
                                <i
                                    class="fa-solid fa-chart-column w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>
                                Reports
                            </span>
                            <i class="fa-solid fa-chevron-right text-[9px] text-gray-500"></i>
                        </a>
                    </li>


                </ul>

                <!-- Account Menu -->
                <div class="mt-7 border-t border-white/10 pt-5">
                    <p class="mb-3 px-4 text-[10px] font-semibold uppercase tracking-widest text-gray-500">Account</p>
                    <ul class="space-y-1">
                        <li>
                            <a href="admin-profile.html"
                                class="group flex items-center gap-3 rounded-lg px-4 py-3 text-xs font-medium text-gray-300 transition hover:bg-white/10 hover:text-white">
                                <i
                                    class="fa-solid fa-user w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>
                                Profile
                            </a>
                        </li>
                        <li>
                            <a href="../../PHP/logout.php"
                                class="flex items-center gap-3 rounded-lg px-4 py-3 text-xs font-medium text-red-400 transition hover:bg-red-500/10 hover:text-red-300">
                                <i class="fa-solid fa-right-from-bracket w-4 text-center"></i>
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Sidebar Footer -->
            <div class="border-t border-white/10 p-4">
                <div class="flex items-center gap-2 rounded-lg bg-white/5 p-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-orange-500/20">
                        <i class="fa-solid fa-shield-halved text-sm text-[#E89000]"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-gray-200">Admin Panel</p>
                        <p class="text-[10px] text-gray-500">System Management</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="min-w-0 flex-1 lg:ml-64">

            <!-- TOP NAVBAR -->
            <header class="sticky top-0 z-40 border-b border-gray-200 bg-white shadow-sm">
                <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">

                    <div>
                        <h2 class="text-lg font-bold text-gray-900 sm:text-xl">Admin Dashboard</h2>
                        <p class="mt-0.5 text-[11px] text-gray-500">
                            Welcome back, <span class="font-semibold text-[#E89000]">Admin</span>
                        </p>
                    </div>

                    <div class="flex items-center gap-3 sm:gap-5">

                        <!-- Notification Dropdown -->
                        <details class="group relative">
                            <summary aria-label="Notifications"
                                class="relative flex h-9 w-9 cursor-pointer list-none items-center justify-center rounded-full bg-gray-100 transition hover:bg-orange-50 [&::-webkit-details-marker]:hidden">
                                <i
                                    class="fa-regular fa-bell text-base text-gray-700 transition group-open:text-[#E89000]"></i>
                                <span
                                    class="absolute right-0 top-0 flex h-4 w-4 items-center justify-center rounded-full border-2 border-white bg-red-500 text-[9px] font-bold text-white">3</span>
                            </summary>

                            <div
                                class="absolute right-0 top-12 z-50 w-80 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl sm:w-96">

                                <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                                    <div>
                                        <h3 class="text-sm font-bold text-gray-900">Notifications</h3>
                                        <p class="mt-0.5 text-[11px] text-gray-500">You have 3 unread notifications</p>
                                    </div>
                                    <button type="button"
                                        class="cursor-pointer text-[11px] font-semibold text-[#FFA500] hover:text-[#E89000]">Mark
                                        all as read</button>
                                </div>

                                <div class="max-h-80 overflow-y-auto">

                                    <div
                                        class="flex gap-3 border-b border-gray-100 bg-orange-50/40 px-4 py-3 transition hover:bg-gray-50">
                                        <div
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-100">
                                            <i class="fa-solid fa-clipboard-check text-sm text-blue-600"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center justify-between gap-2">
                                                <p class="text-xs font-semibold text-gray-800">New Job Card Assigned</p>
                                                <span class="h-2 w-2 shrink-0 rounded-full bg-[#E89000]"></span>
                                            </div>
                                            <p class="mt-1 text-[11px] leading-relaxed text-gray-500">A new job card
                                                JC-0048 has been assigned to a mechanic.</p>
                                            <span class="mt-1.5 block text-[10px] text-gray-400">5 minutes ago</span>
                                        </div>
                                    </div>

                                    <div
                                        class="flex gap-3 border-b border-gray-100 bg-orange-50/40 px-4 py-3 transition hover:bg-gray-50">
                                        <div
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-green-100">
                                            <i class="fa-solid fa-circle-check text-sm text-green-600"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center justify-between gap-2">
                                                <p class="text-xs font-semibold text-gray-800">Service Completed</p>
                                                <span class="h-2 w-2 shrink-0 rounded-full bg-[#E89000]"></span>
                                            </div>
                                            <p class="mt-1 text-[11px] leading-relaxed text-gray-500">Job card JC-0046
                                                has been marked as completed.</p>
                                            <span class="mt-1.5 block text-[10px] text-gray-400">30 minutes ago</span>
                                        </div>
                                    </div>

                                    <div
                                        class="flex gap-3 border-b border-gray-100 bg-orange-50/40 px-4 py-3 transition hover:bg-gray-50">
                                        <div
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-purple-100">
                                            <i class="fa-solid fa-user-plus text-sm text-purple-600"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center justify-between gap-2">
                                                <p class="text-xs font-semibold text-gray-800">New Customer Registered
                                                </p>
                                                <span class="h-2 w-2 shrink-0 rounded-full bg-[#E89000]"></span>
                                            </div>
                                            <p class="mt-1 text-[11px] leading-relaxed text-gray-500">A new customer has
                                                registered on AutoPulse.</p>
                                            <span class="mt-1.5 block text-[10px] text-gray-400">1 hour ago</span>
                                        </div>
                                    </div>

                                    <div class="flex gap-3 px-4 py-3 transition hover:bg-gray-50">
                                        <div
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-orange-100">
                                            <i class="fa-solid fa-boxes-stacked text-sm text-[#E89000]"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs font-semibold text-gray-800">Spare Parts Updated</p>
                                            <p class="mt-1 text-[11px] leading-relaxed text-gray-500">Spare parts
                                                inventory has been updated successfully.</p>
                                            <span class="mt-1.5 block text-[10px] text-gray-400">Yesterday</span>
                                        </div>
                                    </div>

                                </div>

                                <div class="border-t border-gray-100 p-3 text-center">
                                    <a href="#"
                                        class="text-xs font-semibold text-[#FFA500] transition hover:text-[#E89000]">
                                        View All Notifications
                                        <i class="fa-solid fa-arrow-right ml-1 text-[10px]"></i>
                                    </a>
                                </div>
                            </div>
                        </details>

                        <div class="hidden h-8 w-px bg-gray-200 sm:block"></div>

                        <a href="admin-profile.html" class="flex items-center gap-2 sm:gap-3">
                            <div class="hidden sm:block">
                                <h4 class="text-xs font-semibold text-gray-900">Admin</h4>
                                <p class="mt-0.5 text-[10px] text-gray-500">System Administrator</p>
                            </div>
                            <i class="fa-solid fa-chevron-down hidden text-[10px] text-gray-400 sm:block"></i>
                        </a>

                    </div>
                </div>
            </header>

            <?php if (isset($_GET['added'])): ?>

                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-xs font-medium text-green-700">
                    <i class="fa-solid fa-circle-check mr-2"></i>
                    New service added successfully!
                </div>

            <?php elseif (isset($_GET['updated'])): ?>

                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-xs font-medium text-green-700">
                    <i class="fa-solid fa-circle-check mr-2"></i>
                    <?php echo htmlspecialchars($_GET['updated'], ENT_QUOTES, 'UTF-8'); ?>
                </div>

            <?php elseif (isset($_GET['deleted'])): ?>

                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-xs font-medium text-green-700">
                    <i class="fa-solid fa-circle-check mr-2"></i>
                    <?php echo htmlspecialchars($_GET['deleted'], ENT_QUOTES, 'UTF-8'); ?>
                </div>

            <?php elseif (isset($_GET['error'])): ?>

                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-xs font-medium text-red-700">
                    <i class="fa-solid fa-circle-exclamation mr-2"></i>
                    <?php echo htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'); ?>
                </div>

            <?php endif; ?>



            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const alert = document.getElementById('alert-message');

                    if (alert) {
                        setTimeout(() => {
                            alert.remove(); // completely removes from DOM
                        }, 5000); // 5000ms = 5 seconds
                    }
                });
            </script>
            <div class="space-y-6 p-4 sm:p-6 lg:p-8">

                <!-- Page Heading -->
                <section class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-100 text-[#E89000]">
                            <i class="fa-solid fa-screwdriver-wrench text-lg"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold tracking-tight text-gray-900">Service Management</h1>
                            <p class="mt-0.5 text-xs text-gray-500">View and manage available vehicle services</p>
                        </div>
                    </div>

                    <div
                        class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-[11px] text-gray-500 shadow-sm">
                        <i class="fa-solid fa-house text-[#E89000]"></i>
                        <span>Dashboard</span>
                        <i class="fa-solid fa-chevron-right text-[9px]"></i>
                        <span class="font-semibold text-gray-800">Services</span>
                    </div>
                </section>

                <!-- Service Statistics -->
                <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

                    <div
                        class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                        <div class="flex items-center justify-between">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-50 text-[#E89000]">
                                <i class="fa-solid fa-screwdriver-wrench text-lg"></i>
                            </div>
                            <span
                                class="rounded-full bg-orange-50 px-2.5 py-1 text-[10px] font-semibold text-[#E89000]">All
                                Services</span>
                        </div>
                        <h3 class="mt-3 text-2xl font-bold text-gray-900"><?php echo $totalServices; ?></h3>
                        <p class="mt-0.5 text-xs font-semibold text-gray-700">Total Services</p>
                        <p class="mt-1 text-[10px] text-gray-400">Services listed in the system</p>
                    </div>

                    <div
                        class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                        <div class="flex items-center justify-between">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-50 text-green-600">
                                <i class="fa-solid fa-circle-check text-lg"></i>
                            </div>
                            <span
                                class="rounded-full bg-green-50 px-2.5 py-1 text-[10px] font-semibold text-green-600">Available</span>
                        </div>
                        <h3 class="mt-3 text-2xl font-bold text-gray-900"><?php echo $activeServices; ?></h3>
                        <p class="mt-0.5 text-xs font-semibold text-gray-700">Active Services</p>
                        <p class="mt-1 text-[10px] text-gray-400">Currently available for booking</p>
                    </div>

                    <div
                        class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                        <div class="flex items-center justify-between">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                                <i class="fa-solid fa-layer-group text-lg"></i>
                            </div>
                            <span
                                class="rounded-full bg-blue-50 px-2.5 py-1 text-[10px] font-semibold text-blue-600">Categories</span>
                        </div>
                        <h3 class="mt-3 text-2xl font-bold text-gray-900"><?php echo $totalCategories; ?></h3>
                        <p class="mt-0.5 text-xs font-semibold text-gray-700">Service Categories</p>
                        <p class="mt-1 text-[10px] text-gray-400">Different types of services</p>
                    </div>

                    <div
                        class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                        <div class="flex items-center justify-between">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-50 text-red-500">
                                <i class="fa-solid fa-ban text-lg"></i>
                            </div>
                            <span
                                class="rounded-full bg-red-50 px-2.5 py-1 text-[10px] font-semibold text-red-500">Unavailable</span>
                        </div>
                        <h3 class="mt-3 text-2xl font-bold text-gray-900"><?php echo $inactiveServices; ?></h3>
                        <p class="mt-0.5 text-xs font-semibold text-gray-700">Inactive Services</p>
                        <p class="mt-1 text-[10px] text-gray-400">Currently unavailable services</p>
                    </div>

                </section>

                <!-- Available Services -->
                <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

                    <!-- Section Header -->
                    <div
                        class="flex flex-col gap-3 border-b border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-100">
                                <i class="fa-solid fa-list-check text-lg text-[#E89000]"></i>
                            </div>
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="text-base font-bold text-gray-900">Available Services</h2>
                                    <span
                                        class="rounded-full bg-orange-100 px-2 py-0.5 text-[10px] font-semibold text-[#E89000]"><?php echo $totalServices; ?></span>
                                </div>
                                <p class="mt-0.5 text-xs text-gray-500">Browse service details, pricing and availability
                                </p>
                            </div>
                        </div>

                        <a href="#add-service"
                            class="inline-flex h-9 cursor-pointer items-center justify-center gap-2 rounded-lg bg-[#E89000] px-4 text-xs font-semibold text-white shadow-sm transition hover:bg-orange-600">
                            <i class="fa-solid fa-plus text-[10px]"></i>
                            Add New Service
                        </a>
                    </div>

                    <!-- Search and Filters -->
                    <div class="flex flex-col gap-3 px-5 py-4 lg:flex-row lg:items-center">

                        <div class="relative flex-1">
                            <i
                                class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                            <input type="search" placeholder="Search by service name or description..."
                                class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 pl-9 pr-3 text-xs text-gray-700 outline-none transition placeholder:text-gray-400 focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                        </div>

                        <div class="flex flex-col gap-2 sm:flex-row">
                            <select
                                class="h-10 rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-600 outline-none transition focus:border-[#E89000] focus:ring-2 focus:ring-orange-100">
                                <option>All Categories</option>
                                <option>Maintenance</option>
                                <option>Repair</option>
                                <option>Inspection</option>
                                <option>Cleaning</option>
                                <option>Other</option>
                            </select>

                            <select
                                class="h-10 rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-600 outline-none transition focus:border-[#E89000] focus:ring-2 focus:ring-orange-100">
                                <option>All Statuses</option>
                                <option>Active</option>
                                <option>Inactive</option>
                            </select>

                            <button type="button"
                                class="inline-flex h-10 cursor-pointer items-center justify-center gap-2 rounded-lg bg-[#FFA500] px-4 text-xs font-semibold text-white transition hover:bg-[#E89000]">
                                <i class="fa-solid fa-filter text-[10px]"></i>
                                Filter
                            </button>
                        </div>
                    </div>

                    <!-- Service Cards -->
                    <div
                        class="grid grid-cols-1 gap-4 border-t border-gray-100 bg-gray-50/50 p-5 sm:grid-cols-2 xl:grid-cols-3">


                        <?php if ($availableServicesCount > 0): ?>

                            <?php while ($service = $availableServicesResult->fetch_assoc()): ?>

                                <article
                                    class="group cursor-pointer rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-orange-300 hover:shadow-md">

                                    <!-- Service Icon and Status -->
                                    <div class="flex items-start justify-between">
                                        <div
                                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-50 text-[#E89000] transition group-hover:bg-[#E89000] group-hover:text-white">
                                            <i class="fa-solid fa-screwdriver-wrench text-xl"></i>
                                        </div>

                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-1 text-[10px] font-semibold text-green-600">
                                            <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                            Active
                                        </span>
                                    </div>

                                    <!-- Service Details -->
                                    <div class="mt-4">
                                        <p class="text-[10px] font-semibold uppercase tracking-wider text-[#E89000]">
                                            <?php echo htmlspecialchars($service['service_type'], ENT_QUOTES, 'UTF-8'); ?>
                                        </p>

                                        <h3 class="mt-1 text-sm font-bold text-gray-900">
                                            <?php echo htmlspecialchars($service['service_name'], ENT_QUOTES, 'UTF-8'); ?>
                                        </h3>

                                        <p class="mt-2 min-h-10 text-[11px] leading-relaxed text-gray-500">
                                            <?php echo htmlspecialchars($service['description'], ENT_QUOTES, 'UTF-8'); ?>
                                        </p>
                                    </div>

                                    <!-- Price and Duration -->
                                    <div class="mt-4 flex items-center justify-between border-t border-gray-100 pt-3">
                                        <div>
                                            <p class="text-[10px] text-gray-400">Starting Price</p>
                                            <p class="mt-0.5 text-lg font-bold text-gray-900">
                                                Rs. <?php echo number_format((float) $service['price'], 2); ?>
                                            </p>
                                        </div>

                                        <div class="text-right">
                                            <p class="text-[10px] text-gray-400">Duration</p>
                                            <p class="mt-0.5 text-xs font-semibold text-gray-700">
                                                <i class="fa-regular fa-clock mr-1 text-gray-400"></i>
                                                <?php echo htmlspecialchars($service['estimate_duration'], ENT_QUOTES, 'UTF-8'); ?>
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="mt-4 flex gap-2">
                                        <button type="button" onclick="openEditServiceModal(this)"
                                            data-id="<?php echo (int) $service['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($service['service_name'], ENT_QUOTES, 'UTF-8'); ?>"
                                            data-type="<?php echo htmlspecialchars($service['service_type'], ENT_QUOTES, 'UTF-8'); ?>"
                                            data-description="<?php echo htmlspecialchars($service['description'], ENT_QUOTES, 'UTF-8'); ?>"
                                            data-price="<?php echo htmlspecialchars($service['price'], ENT_QUOTES, 'UTF-8'); ?>"
                                            data-duration="<?php echo htmlspecialchars($service['estimate_duration'], ENT_QUOTES, 'UTF-8'); ?>"
                                            data-status="active"
                                            class="flex h-9 flex-1 cursor-pointer items-center justify-center gap-2 rounded-lg border border-orange-200 text-[11px] font-semibold text-[#E89000] transition hover:bg-[#E89000] hover:text-white">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                            Edit Service
                                        </button>


                                    </div>

                                </article>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <div
                                class="col-span-full rounded-xl border border-dashed border-gray-300 bg-white px-5 py-12 text-center">
                                <div
                                    class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-orange-50 text-[#E89000]">
                                    <i class="fa-solid fa-screwdriver-wrench text-2xl"></i>
                                </div>

                                <h3 class="mt-4 text-sm font-bold text-gray-900">
                                    No Available Services
                                </h3>

                                <p class="mt-1 text-xs text-gray-500">
                                    There are currently no active services available.
                                </p>
                            </div>

                        <?php endif; ?>


                    </div>

                    <!-- Pagination -->
                    <div class="border-t border-gray-100 px-5 py-4">
                        <p class="text-[11px] text-gray-500">
                            Showing
                            <span class="font-semibold text-gray-800">
                                <?php echo $availableServicesCount; ?>
                            </span>
                            available services
                        </p>
                    </div>

                </section>

                <!-- EDIT SERVICE MODAL -->
                <div id="editServiceModal"
                    class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 p-4">

                    <div class="w-full max-w-2xl overflow-hidden rounded-xl bg-white shadow-2xl">

                        <!-- Modal Header -->
                        <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-100 text-[#E89000]">
                                    <i class="fa-solid fa-pen-to-square text-lg"></i>
                                </div>
                                <div>
                                    <h2 class="text-base font-bold text-gray-900">Edit Service</h2>
                                    <p class="mt-0.5 text-xs text-gray-500">Update service information and availability
                                    </p>
                                </div>
                            </div>

                            <button type="button" onclick="closeEditServiceModal()"
                                class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-lg text-gray-400 transition hover:bg-gray-100 hover:text-gray-700">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>

                        <!-- Edit Form -->
                        <form action="edit-service.php" method="POST" class="space-y-5 p-5">

                            <input type="hidden" name="csrf_token"
                                value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="service_id" id="edit-service-id">

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                                <!-- Service Name -->
                                <div>
                                    <label for="edit-service-name"
                                        class="mb-1.5 block text-xs font-semibold text-gray-700">
                                        Service Name
                                    </label>
                                    <input type="text" name="service_name" id="edit-service-name" required
                                        class="h-10 w-full rounded-lg border border-gray-200 px-3 text-xs outline-none transition focus:border-[#E89000] focus:ring-2 focus:ring-orange-100">
                                </div>

                                <!-- Category -->
                                <div>
                                    <label for="edit-service-category"
                                        class="mb-1.5 block text-xs font-semibold text-gray-700">
                                        Service Category
                                    </label>
                                    <select name="category" id="edit-service-category" required
                                        class="h-10 w-full rounded-lg border border-gray-200 bg-white px-3 text-xs outline-none transition focus:border-[#E89000] focus:ring-2 focus:ring-orange-100">
                                        <option value="">Select Category</option>
                                        <option value="maintenance">Maintenance</option>
                                        <option value="repair">Repair</option>
                                        <option value="inspection">Inspection</option>
                                        <option value="cleaning">Cleaning</option>
                                        <option value="electrical">Electrical</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>

                                <!-- Price -->
                                <div>
                                    <label for="edit-service-price"
                                        class="mb-1.5 block text-xs font-semibold text-gray-700">
                                        Service Price (Rs.)
                                    </label>
                                    <input type="number" name="price" id="edit-service-price" min="0" step="0.01"
                                        required
                                        class="h-10 w-full rounded-lg border border-gray-200 px-3 text-xs outline-none transition focus:border-[#E89000] focus:ring-2 focus:ring-orange-100">
                                </div>

                                <!-- Duration -->
                                <div>
                                    <label for="edit-service-duration"
                                        class="mb-1.5 block text-xs font-semibold text-gray-700">
                                        Estimated Duration
                                    </label>
                                    <input type="text" name="duration" id="edit-service-duration" required
                                        placeholder="e.g. 45 min or 2 hours"
                                        class="h-10 w-full rounded-lg border border-gray-200 px-3 text-xs outline-none transition focus:border-[#E89000] focus:ring-2 focus:ring-orange-100">
                                </div>

                            </div>

                            <!-- Description -->
                            <div>
                                <label for="edit-service-description"
                                    class="mb-1.5 block text-xs font-semibold text-gray-700">
                                    Service Description
                                </label>
                                <textarea name="description" id="edit-service-description" rows="3" required
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-xs outline-none transition focus:border-[#E89000] focus:ring-2 focus:ring-orange-100"></textarea>
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="edit-service-status"
                                    class="mb-1.5 block text-xs font-semibold text-gray-700">
                                    Service Status
                                </label>
                                <select name="status" id="edit-service-status"
                                    class="h-10 w-full rounded-lg border border-gray-200 bg-white px-3 text-xs outline-none transition focus:border-[#E89000] focus:ring-2 focus:ring-orange-100">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>

                            <!-- Buttons -->
                            <div
                                class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-4 sm:flex-row sm:items-center sm:justify-between">

                                <button type="button" onclick="deleteService()"
                                    class="flex h-10 cursor-pointer items-center justify-center gap-2 rounded-lg border border-red-200 px-4 text-xs font-semibold text-red-600 transition hover:bg-red-50">
                                    <i class="fa-solid fa-trash-can"></i>
                                    Delete Service
                                </button>

                                <div class="flex gap-2">
                                    <button type="button" onclick="closeEditServiceModal()"
                                        class="h-10 cursor-pointer rounded-lg border border-gray-200 px-4 text-xs font-semibold text-gray-600 transition hover:bg-gray-50">
                                        Cancel
                                    </button>

                                    <button type="submit"
                                        class="h-10 cursor-pointer rounded-lg bg-[#E89000] px-5 text-xs font-semibold text-white transition hover:bg-orange-600">
                                        <i class="fa-solid fa-floppy-disk mr-1"></i>
                                        Save Changes
                                    </button>
                                </div>

                            </div>
                        </form>

                        <!-- Separate Delete Form -->
                        <form id="deleteServiceForm" action="edit-service.php" method="POST" class="hidden">
                            <input type="hidden" name="csrf_token"
                                value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="service_id" id="delete-service-id">
                        </form>

                    </div>
                </div>

                <!-- Add New Service -->
                <section id="add-service" class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

                    <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-100 text-[#E89000]">
                            <i class="fa-solid fa-plus text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-gray-900">Add New Service</h2>
                            <p class="mt-0.5 text-xs text-gray-500">Add a new service to the available services list</p>
                        </div>
                    </div>

                    <form action="add-service.php" method="POST" class="space-y-5 p-5">

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                            <div>
                                <label for="service-name"
                                    class="mb-1.5 block text-xs font-semibold text-gray-700">Service Name</label>
                                <input id="service-name" name="service_name" type="text"
                                    placeholder="Enter service name" required
                                    class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                            </div>

                            <div>
                                <label for="service-category"
                                    class="mb-1.5 block text-xs font-semibold text-gray-700">Service Category</label>
                                <select id="service-category" name="category" required
                                    class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                                    <option value="">Select Category</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="repair">Repair</option>
                                    <option value="inspection">Inspection</option>
                                    <option value="cleaning">Cleaning</option>
                                    <option value="electrical">Electrical</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <div>
                                <label for="service-price"
                                    class="mb-1.5 block text-xs font-semibold text-gray-700">Service Price (Rs.)</label>
                                <input id="service-price" name="price" type="number" min="0" step="0.01"
                                    placeholder="Enter service price" required
                                    class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                            </div>

                            <div>
                                <label for="service-duration"
                                    class="mb-1.5 block text-xs font-semibold text-gray-700">Estimated Duration</label>
                                <input id="service-duration" name="duration" type="text"
                                    placeholder="e.g. 1 hour, 45 minutes" required
                                    class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                            </div>

                            <div class="md:col-span-2">
                                <label for="service-description"
                                    class="mb-1.5 block text-xs font-semibold text-gray-700">Service Description</label>
                                <textarea id="service-description" name="description" rows="3"
                                    placeholder="Describe what this service includes..." required
                                    class="w-full resize-none rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100"></textarea>
                            </div>

                            <div>
                                <label for="service-status"
                                    class="mb-1.5 block text-xs font-semibold text-gray-700">Service Status</label>
                                <select id="service-status" name="status"
                                    class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>

                        </div>

                        <div
                            class="flex flex-col-reverse gap-2 border-t border-gray-100 pt-4 sm:flex-row sm:justify-end">
                            <button type="reset"
                                class="inline-flex h-10 cursor-pointer items-center justify-center gap-2 rounded-lg border border-gray-200 px-5 text-xs font-semibold text-gray-600 transition hover:bg-gray-50">
                                <i class="fa-solid fa-rotate-left"></i>
                                Clear Form
                            </button>

                            <button type="submit"
                                class="inline-flex h-10 cursor-pointer items-center justify-center gap-2 rounded-lg bg-[#E89000] px-5 text-xs font-semibold text-white shadow-sm transition hover:bg-orange-600">
                                <i class="fa-solid fa-plus"></i>
                                Add Service
                            </button>
                        </div>

                    </form>
                </section>

            </div>
        </main>
    </div>


    <script>
        const editServiceModal = document.getElementById("editServiceModal");

        function openEditServiceModal(button) {
            document.getElementById("edit-service-id").value = button.dataset.id;
            document.getElementById("edit-service-name").value = button.dataset.name;
            document.getElementById("edit-service-category").value = button.dataset.type.toLowerCase();
            document.getElementById("edit-service-description").value = button.dataset.description;
            document.getElementById("edit-service-price").value = button.dataset.price;
            document.getElementById("edit-service-duration").value = button.dataset.duration;
            document.getElementById("edit-service-status").value = button.dataset.status;

            editServiceModal.classList.remove("hidden");
            editServiceModal.classList.add("flex");
            document.body.classList.add("overflow-hidden");
        }

        function closeEditServiceModal() {
            editServiceModal.classList.add("hidden");
            editServiceModal.classList.remove("flex");
            document.body.classList.remove("overflow-hidden");
        }

        function deleteService() {
            const serviceId = document.getElementById("edit-service-id").value;
            const serviceName = document.getElementById("edit-service-name").value;

            if (!serviceId) {
                alert("Please select a service first.");
                return;
            }

            const confirmed = confirm(
                'Are you sure you want to delete "' + serviceName + '"? This action cannot be undone.'
            );

            if (confirmed) {
                document.getElementById("delete-service-id").value = serviceId;
                document.getElementById("deleteServiceForm").submit();
            }
        }

        // Close the modal when clicking the dark background
        editServiceModal.addEventListener("click", function (event) {
            if (event.target === editServiceModal) {
                closeEditServiceModal();
            }
        });

        // Close the modal with the Escape key
        document.addEventListener("keydown", function (event) {
            if (event.key === "Escape" && !editServiceModal.classList.contains("hidden")) {
                closeEditServiceModal();
            }
        });
    </script>
</body>

</html>