<?php
$required_role = "admin";
require_once "../../PHP/auth_guard.php";

// Load admin dashboard data
include "../../PHP/admin_dashboard_backend.php";

/* ==========================================
   RECENT JOB CARDS - DATABASE DATA
========================================== */

// Get total number of job cards
$total_job_cards = 0;

$countQuery = "SELECT COUNT(*) AS total FROM job_cards";
$countResult = $conn->query($countQuery);

if ($countResult) {
    $countRow = $countResult->fetch_assoc();
    $total_job_cards = (int) $countRow['total'];
}

// Get all job cards for filtering and searching (not limited to 3)
$recentJobCards = [];

$recentQuery = "
    SELECT
        jc.id AS job_card_id,
        jc.status AS job_status,
        jc.created_at,
        a.vehicle_registration_number,
        customer.full_name AS customer_name,
        customer.phone AS customer_phone,
        customer.email AS customer_email,
        mechanic.full_name AS mechanic_name

    FROM job_cards jc

    LEFT JOIN appointment a
        ON jc.appointment_id = a.id

    LEFT JOIN users customer
        ON a.user_id = customer.id

    LEFT JOIN users mechanic
        ON jc.mechanic_id = mechanic.id

    ORDER BY jc.created_at DESC
";

$recentResult = $conn->query($recentQuery);

if ($recentResult) {
    while ($row = $recentResult->fetch_assoc()) {
        $recentJobCards[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoPulse | Book Appointment</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4.3.3"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css">
</head>

<body class="min-h-screen bg-[#F5F5F5] font-poppins text-gray-800">
    <div class="flex min-h-screen">

        <!-- SIDEBAR -->
        <aside
            class="fixed left-0 top-0 z-50 hidden h-screen w-64 flex-col overflow-y-auto bg-gray-950 text-white shadow-xl lg:flex">
            <!-- Logo -->
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

            <!-- Navigation Label -->
            <div class="px-5 pb-2 pt-6">
                <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-500">Main Menu</p>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-3">
                <ul class="space-y-1">

                    <!-- Dashboard -->
                    <li>
                        <a href="dashboard.php"
                            class="flex items-center gap-3 rounded-lg bg-[#E89000] px-4 py-3 text-xs font-semibold text-white shadow-sm">
                            <i class="fa-solid fa-table-columns w-4 text-center"></i>
                            Dashboard
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

                    <!-- Services -->
                    <li>
                        <a href="services.php"
                            class="group flex items-center justify-between rounded-lg px-4 py-3 text-xs font-medium text-gray-300 transition duration-200 hover:bg-white/10 hover:text-white">
                            <span class="flex items-center gap-3">
                                <i
                                    class="fa-solid fa-screwdriver-wrench w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>
                                Services
                            </span>
                            <i class="fa-solid fa-chevron-right text-[9px] text-gray-500"></i>
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

                    <!-- Page Title -->
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 sm:text-xl">Admin Dashboard</h2>
                        <p class="mt-0.5 text-[11px] text-gray-500">
                            Welcome back, <span class="font-semibold text-[#E89000]">
                                <?php echo htmlspecialchars($_SESSION["full_name"]); ?>
                            </span>
                        </p>
                    </div>

                    <!-- Right Actions -->
                    <div class="flex items-center gap-3 sm:gap-5">

                        <!-- Notification -->

                        <!-- Notification Dropdown -->
                        <details class="group relative">
                            <!-- Notification Button -->
                            <summary aria-label="Notifications"
                                class="relative flex h-9 w-9 cursor-pointer list-none items-center justify-center rounded-full bg-gray-100 transition hover:bg-orange-50 [&::-webkit-details-marker]:hidden">
                                <i
                                    class="fa-regular fa-bell text-base text-gray-700 transition group-open:text-[#E89000]"></i>
                                <span
                                    class="absolute right-0 top-0 flex h-4 w-4 items-center justify-center rounded-full border-2 border-white bg-red-500 text-[9px] font-bold text-white">
                                    3
                                </span>
                            </summary>

                            <!-- Dropdown Panel -->
                            <div
                                class="absolute right-0 top-12 z-50 w-80 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl sm:w-96">

                                <!-- Header -->
                                <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                                    <div>
                                        <h3 class="text-sm font-bold text-gray-900">Notifications</h3>
                                        <p class="mt-0.5 text-[11px] text-gray-500">You have 3 unread notifications</p>
                                    </div>
                                    <button type="button"
                                        class="text-[11px] font-semibold text-[#FFA500] hover:text-[#E89000] cursor-pointer">
                                        Mark all as read
                                    </button>
                                </div>

                                <!-- Notification List -->
                                <div class="max-h-80 overflow-y-auto">

                                    <!-- Notification 1 -->
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
                                            <p class="mt-1 text-[11px] leading-relaxed text-gray-500">
                                                A new job card JC-0048 has been assigned to a mechanic.
                                            </p>
                                            <span class="mt-1.5 block text-[10px] text-gray-400">5 minutes ago</span>
                                        </div>
                                    </div>

                                    <!-- Notification 2 -->
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
                                            <p class="mt-1 text-[11px] leading-relaxed text-gray-500">
                                                Job card JC-0046 has been marked as completed.
                                            </p>
                                            <span class="mt-1.5 block text-[10px] text-gray-400">30 minutes ago</span>
                                        </div>
                                    </div>

                                    <!-- Notification 3 -->
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
                                            <p class="mt-1 text-[11px] leading-relaxed text-gray-500">
                                                A new customer has registered on AutoPulse.
                                            </p>
                                            <span class="mt-1.5 block text-[10px] text-gray-400">1 hour ago</span>
                                        </div>
                                    </div>

                                    <!-- Notification 4 -->
                                    <div class="flex gap-3 px-4 py-3 transition hover:bg-gray-50">
                                        <div
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-orange-100">
                                            <i class="fa-solid fa-boxes-stacked text-sm text-[#E89000]"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs font-semibold text-gray-800">Spare Parts Updated</p>
                                            <p class="mt-1 text-[11px] leading-relaxed text-gray-500">
                                                Spare parts inventory has been updated successfully.
                                            </p>
                                            <span class="mt-1.5 block text-[10px] text-gray-400">Yesterday</span>
                                        </div>
                                    </div>

                                </div>

                                <!-- Footer -->
                                <div class="border-t border-gray-100 p-3 text-center">
                                    <a href="#"
                                        class="text-xs font-semibold text-[#FFA500] transition hover:text-[#E89000]">
                                        View All Notifications
                                        <i class="fa-solid fa-arrow-right ml-1 text-[10px]"></i>
                                    </a>
                                </div>

                            </div>
                        </details>


                        <!-- Divider -->
                        <div class="hidden h-8 w-px bg-gray-200 sm:block"></div>

                        <!-- Profile -->
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

            <!-- PAGE CONTENT -->
            <div class="space-y-6 p-4 sm:p-6 lg:p-8">

                <!-- Admin Dashboard Statistics -->
                <section class="mb-6">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Overview Statistics</h2>
                            <p class="mt-0.5 text-xs text-gray-500">A quick overview of your service center</p>
                        </div>
                        <span
                            class="hidden items-center gap-1.5 rounded-full bg-green-50 px-3 py-1.5 text-[10px] font-semibold text-green-600 sm:inline-flex">
                            <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                            Live Overview
                        </span>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

                        <!-- Total Customers -->
                        <div
                            class="group rounded-xl border border-gray-100 border-l-4 border-l-[#E89000] bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-50 transition group-hover:bg-orange-100">
                                        <i class="fa-solid fa-users text-xl text-[#E89000]"></i>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-medium text-gray-500">Total Customers</p>
                                        <h3 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">
                                            <?php echo $clients_count; ?>
                                        </h3>
                                    </div>
                                </div>
                                <i class="fa-solid fa-arrow-trend-up text-sm text-[#E89000]"></i>
                            </div>
                            <div class="mt-4 border-t border-gray-100 pt-3">
                                <p class="text-[10px] text-gray-500">Registered customers</p>
                            </div>
                        </div>

                        <!-- Total Mechanics -->
                        <div
                            class="group rounded-xl border border-gray-100 border-l-4 border-l-blue-500 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 transition group-hover:bg-blue-100">
                                        <i class="fa-solid fa-user-gear text-xl text-blue-500"></i>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-medium text-gray-500">Total Mechanics</p>
                                        <h3 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">
                                            <?php echo $mechanics_count; ?>
                                        </h3>
                                    </div>
                                </div>
                                <i class="fa-solid fa-users text-sm text-blue-500"></i>
                            </div>
                            <div class="mt-4 border-t border-gray-100 pt-3">
                                <p class="text-[10px] text-gray-500">Registered service technicians</p>
                            </div>
                        </div>

                        <!-- Active Job Cards -->
                        <div
                            class="group rounded-xl border border-gray-100 border-l-4 border-l-green-500 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-50 transition group-hover:bg-green-100">
                                        <i class="fa-solid fa-car text-xl text-green-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-medium text-gray-500">Active Job Cards</p>
                                        <h3 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">
                                            <?php echo $active_jobs_cards; ?>
                                        </h3>
                                    </div>
                                </div>
                                <i class="fa-solid fa-clipboard-list text-sm text-green-600"></i>
                            </div>
                            <div class="mt-4 border-t border-gray-100 pt-3">
                                <p class="text-[10px] text-gray-500">Currently active service jobs</p>
                            </div>
                        </div>

                        <!-- Spare Parts -->
                        <div
                            class="group rounded-xl border border-gray-100 border-l-4 border-l-red-500 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-50 transition group-hover:bg-red-100">
                                        <i class="fa-solid fa-box-open text-xl text-red-500"></i>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-medium text-gray-500">Total Spare Parts</p>
                                        <h3 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">
                                            <?php echo $spare_parts_count; ?>
                                        </h3>
                                    </div>
                                </div>
                                <i class="fa-solid fa-boxes-stacked text-sm text-red-500"></i>
                            </div>
                            <div class="mt-4 border-t border-gray-100 pt-3">
                                <p class="text-[10px] text-gray-500">Parts available in inventory</p>
                            </div>
                        </div>

                    </div>
                </section>



                <!-- Recent Job Cards -->
                <section class="mt-6">
                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

                        <!-- Header -->
                        <div
                            class="flex flex-col gap-3 border-b border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-100">
                                    <i class="fa-solid fa-clipboard-list text-lg text-[#E89000]"></i>
                                </div>
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h2 class="text-base font-bold text-gray-900">Job Cards</h2>
                                        <span
                                            class="rounded-full bg-orange-100 px-2 py-0.5 text-[10px] font-semibold text-[#E89000]"><span id="tableCount"><?php echo count($recentJobCards); ?></span>
                                            Total</span>
                                    </div>
                                    <p class="mt-0.5 text-xs text-gray-500">Service requests and repair activities</p>
                                </div>
                            </div>
                        </div>

                        <!-- Search & Filter -->
                        <div class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center">
                            <div class="relative flex-1">
                                <i
                                    class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                                <input type="text" id="searchInput" placeholder="Search by Job Card ID, Customer, or Vehicle..."
                                    class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 pl-9 pr-3 text-xs text-gray-700 outline-none transition placeholder:text-gray-400 focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                            </div>

                            <div class="flex flex-col gap-2 sm:flex-row">
                                <select id="statusFilter"
                                    class="h-10 rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-600 outline-none transition focus:border-[#E89000] focus:ring-2 focus:ring-orange-100">
                                    <option value="All">All Statuses</option>
                                    <option value="assigned">Assigned</option>
                                    <option value="in progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[950px] text-left" id="jobCardsTable">
                                <thead>
                                    <tr class="border-y border-gray-100 bg-gray-50">
                                        <th
                                            class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">
                                            Job Card ID</th>
                                        <th
                                            class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">
                                            Customer</th>
                                        <th
                                            class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">
                                            Vehicle No.</th>
                                        <th
                                            class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">
                                            Assigned Mechanic</th>
                                        <th
                                            class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">
                                            Created Date</th>
                                        <th
                                            class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">
                                            Status</th>
                                        <th
                                            class="px-5 py-3 text-center text-[10px] font-bold uppercase tracking-wider text-gray-500">
                                            Action</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-gray-100 text-xs text-gray-600" id="tableBody">

                                    <?php if (!empty($recentJobCards)): ?>

                                        <?php foreach ($recentJobCards as $job): ?>

                                            <?php
                                            // Format job card ID
                                            $jobCardNumber = 'JC-' . str_pad(
                                                $job['job_card_id'],
                                                4,
                                                '0',
                                                STR_PAD_LEFT
                                            );

                                            // Customer name
                                            $customerName = $job['customer_name'] ?: 'Unknown Customer';
                                            $customerPhone = $job['customer_phone'] ?: 'N/A';
                                            $customerEmail = $job['customer_email'] ?: 'N/A';

                                            // Vehicle registration
                                            $vehicleNumber = $job['vehicle_registration_number'] ?: 'Not Available';

                                            // Mechanic name
                                            $mechanicName = $job['mechanic_name'] ?: 'Not Assigned';

                                            // Created date
                                            $createdDate = !empty($job['created_at'])
                                                ? date('d M Y', strtotime($job['created_at']))
                                                : 'Not Available';
                                                
                                            $fullDate = !empty($job['created_at'])
                                                ? date('d M Y, h:i A', strtotime($job['created_at']))
                                                : 'Not Available';

                                            // Job card status
                                            $status = strtolower(trim($job['job_status'] ?? ''));

                                            // Status badge colors
                                            $statusClasses = [
                                                'assigned' => 'bg-blue-50 text-blue-600',
                                                'in progress' => 'bg-orange-50 text-orange-600',
                                                'completed' => 'bg-green-50 text-green-600',
                                                'closed' => 'bg-gray-100 text-gray-600',
                                                'pending' => 'bg-yellow-50 text-yellow-600'
                                            ];

                                            $statusColor = $statusClasses[$status] ?? 'bg-gray-100 text-gray-600';

                                            $statusLabel = $status !== ''
                                                ? ucwords($status)
                                                : 'Unknown';

                                            // Mechanic initials
                                            $mechanicInitials = 'NA';

                                            if ($mechanicName !== 'Not Assigned') {
                                                $nameParts = explode(' ', trim($mechanicName));
                                                $mechanicInitials = strtoupper(
                                                    substr($nameParts[0], 0, 1) .
                                                    (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : '')
                                                );
                                            }
                                            
                                            // Data for the modal
                                            $modalData = htmlspecialchars(json_encode([
                                                'jobCardId' => $jobCardNumber,
                                                'customerName' => $customerName,
                                                'customerPhone' => $customerPhone,
                                                'customerEmail' => $customerEmail,
                                                'vehicleNo' => $vehicleNumber,
                                                'mechanicName' => $mechanicName,
                                                'createdDate' => $fullDate,
                                                'status' => $statusLabel,
                                                'statusColorClass' => $statusColor
                                            ]), ENT_QUOTES, 'UTF-8');
                                            ?>

                                            <tr class="transition hover:bg-orange-50/30 table-row-item" data-status="<?php echo $status; ?>">

                                                <!-- Job Card ID -->
                                                <td class="px-5 py-3.5 search-target">
                                                    <span class="font-bold text-gray-900">
                                                        <?php echo htmlspecialchars($jobCardNumber); ?>
                                                    </span>
                                                </td>

                                                <!-- Customer -->
                                                <td class="px-5 py-3.5 search-target">
                                                    <div class="font-semibold text-gray-800">
                                                        <?php echo htmlspecialchars($customerName); ?>
                                                    </div>
                                                    <span class="mt-0.5 block text-[10px] text-gray-400">
                                                        Customer
                                                    </span>
                                                </td>

                                                <!-- Vehicle Number -->
                                                <td class="px-5 py-3.5 search-target">
                                                    <span class="inline-flex items-center gap-2 font-medium text-gray-700">
                                                        <i class="fa-solid fa-car text-[11px] text-gray-400"></i>
                                                        <?php echo htmlspecialchars($vehicleNumber); ?>
                                                    </span>
                                                </td>

                                                <!-- Assigned Mechanic -->
                                                <td class="px-5 py-3.5">
                                                    <div class="flex items-center gap-2">

                                                        <div
                                                            class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-100 text-[10px] font-bold text-blue-600">
                                                            <?php echo htmlspecialchars($mechanicInitials); ?>
                                                        </div>

                                                        <span class="font-medium text-gray-700">
                                                            <?php echo htmlspecialchars($mechanicName); ?>
                                                        </span>

                                                    </div>
                                                </td>

                                                <!-- Created Date -->
                                                <td class="px-5 py-3.5 text-gray-500">
                                                    <?php echo htmlspecialchars($createdDate); ?>
                                                </td>

                                                <!-- Status -->
                                                <td class="px-5 py-3.5">
                                                    <span
                                                        class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[10px] font-semibold <?php echo $statusColor; ?>">

                                                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>

                                                        <?php echo htmlspecialchars($statusLabel); ?>

                                                    </span>
                                                </td>

                                                <!-- Action -->
                                                <td class="px-5 py-3.5 text-center">
                                                    <button type="button" onclick="openJobCardModal(<?php echo $modalData; ?>)"
                                                        class="inline-flex items-center gap-1.5 rounded-md border border-orange-200 px-3 py-1.5 text-[11px] font-semibold text-[#E89000] transition hover:border-[#E89000] hover:bg-[#E89000] hover:text-white cursor-pointer">
                                                        <i class="fa-solid fa-eye"></i>
                                                        View
                                                    </button>
                                                </td>

                                            </tr>

                                        <?php endforeach; ?>

                                    <?php else: ?>

                                        <tr id="noResultsRow">
                                            <td colspan="7" class="px-5 py-12 text-center">

                                                <div class="flex flex-col items-center gap-2">

                                                    <i class="fa-solid fa-clipboard-list text-3xl text-gray-300"></i>

                                                    <p class="text-sm font-semibold text-gray-500">
                                                        No Job Cards Found
                                                    </p>

                                                    <p class="text-xs text-gray-400">
                                                        There are currently no job cards matching your criteria.
                                                    </p>

                                                </div>

                                            </td>
                                        </tr>

                                    <?php endif; ?>
                                        
                                        <!-- Hidden row for JS when no results found after filtering -->
                                        <tr id="jsNoResultsRow" class="hidden">
                                            <td colspan="7" class="px-5 py-12 text-center">
                                                <div class="flex flex-col items-center gap-2">
                                                    <i class="fa-solid fa-search text-3xl text-gray-300"></i>
                                                    <p class="text-sm font-semibold text-gray-500">No Job Cards Found</p>
                                                    <p class="text-xs text-gray-400">Try adjusting your search or filter.</p>
                                                </div>
                                            </td>
                                        </tr>

                                </tbody>
                            </table>
                        </div>

                    </div>
                </section>


            </div>
        </main>
    </div>
    
    <!-- Job Card Details Modal -->
    <div id="jobCardDetailsModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/60 p-4 backdrop-blur-sm">
        <div class="relative w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl transition-all">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50/50 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-100 text-[#E89000]">
                        <i class="fa-solid fa-clipboard-list text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Job Card Details</h3>
                        <p class="text-[11px] text-gray-500" id="modalJobCardIdSubtitle">Viewing details for JC-XXXX</p>
                    </div>
                </div>
                <button type="button" onclick="closeJobCardModal()" class="flex h-8 w-8 items-center justify-center rounded-full text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-700 cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="p-6">
                <!-- Status Banner -->
                <div class="mb-6 flex items-center justify-between rounded-xl border border-gray-100 bg-gray-50 p-4">
                    <div>
                        <p class="text-[10px] font-medium uppercase tracking-wider text-gray-500">Current Status</p>
                        <span id="modalStatusBadge" class="mt-1 inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-bold">
                            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                            <span id="modalStatusText">Status</span>
                        </span>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-medium uppercase tracking-wider text-gray-500">Created On</p>
                        <p class="mt-1 text-xs font-semibold text-gray-800" id="modalCreatedDate">Date</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    
                    <!-- Customer Details -->
                    <div>
                        <h4 class="mb-3 flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-gray-400">
                            <i class="fa-solid fa-user text-[#E89000]"></i> Customer Information
                        </h4>
                        <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
                            <p class="text-sm font-bold text-gray-900" id="modalCustomerName">Customer Name</p>
                            
                            <div class="mt-3 space-y-2">
                                <div class="flex items-center gap-2 text-xs text-gray-600">
                                    <i class="fa-solid fa-phone w-3 text-center text-gray-400"></i>
                                    <span id="modalCustomerPhone">Phone</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-gray-600">
                                    <i class="fa-solid fa-envelope w-3 text-center text-gray-400"></i>
                                    <span id="modalCustomerEmail">Email</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Vehicle & Job Details -->
                    <div>
                        <h4 class="mb-3 flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-gray-400">
                            <i class="fa-solid fa-car text-[#E89000]"></i> Service Information
                        </h4>
                        <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
                            
                            <div class="mb-3 pb-3 border-b border-gray-100">
                                <p class="text-[10px] text-gray-500">Vehicle Registration No.</p>
                                <p class="mt-0.5 text-sm font-bold text-gray-900" id="modalVehicleNo">Vehicle No</p>
                            </div>
                            
                            <div>
                                <p class="text-[10px] text-gray-500">Assigned Mechanic</p>
                                <div class="mt-1 flex items-center gap-2">
                                    <div class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-50 text-[9px] font-bold text-blue-600">
                                        <i class="fa-solid fa-wrench"></i>
                                    </div>
                                    <p class="text-xs font-semibold text-gray-800" id="modalMechanicName">Mechanic Name</p>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 border-t border-gray-100 bg-gray-50 px-6 py-4">
                <button type="button" onclick="closeJobCardModal()" class="rounded-lg bg-white px-4 py-2 text-xs font-semibold text-gray-700 border border-gray-200 shadow-sm transition hover:bg-gray-50 hover:text-gray-900 cursor-pointer">
                    Close Details
                </button>
            </div>
            
        </div>
    </div>
    
    <!-- Scripts for Search, Filter, and Modal -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const tableRows = document.querySelectorAll('.table-row-item');
            const tableCount = document.getElementById('tableCount');
            const jsNoResultsRow = document.getElementById('jsNoResultsRow');
            const phpNoResultsRow = document.getElementById('noResultsRow');
            
            function filterTable() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedStatus = statusFilter.value.toLowerCase();
                let visibleCount = 0;
                
                tableRows.forEach(row => {
                    const rowStatus = row.getAttribute('data-status');
                    
                    // Get text from targets (Job ID, Customer Name, Vehicle No)
                    const targetCells = row.querySelectorAll('.search-target');
                    let rowText = '';
                    targetCells.forEach(cell => {
                        rowText += cell.textContent.toLowerCase() + ' ';
                    });
                    
                    const matchesSearch = rowText.includes(searchTerm);
                    const matchesStatus = selectedStatus === 'all' || rowStatus === selectedStatus;
                    
                    if (matchesSearch && matchesStatus) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                // Update counter
                tableCount.textContent = visibleCount;
                
                // Show/hide no results message
                if (phpNoResultsRow) {
                    phpNoResultsRow.style.display = 'none'; // Always hide PHP empty state when typing/filtering
                }
                
                if (visibleCount === 0 && tableRows.length > 0) {
                    jsNoResultsRow.classList.remove('hidden');
                } else {
                    jsNoResultsRow.classList.add('hidden');
                }
            }
            
            searchInput.addEventListener('input', filterTable);
            statusFilter.addEventListener('change', filterTable);
        });

        // Modal Functions
        function openJobCardModal(data) {
            // Populate data
            document.getElementById('modalJobCardIdSubtitle').textContent = 'Viewing details for ' + data.jobCardId;
            
            document.getElementById('modalStatusText').textContent = data.status;
            const badge = document.getElementById('modalStatusBadge');
            badge.className = 'mt-1 inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-bold ' + data.statusColorClass;
            
            document.getElementById('modalCreatedDate').textContent = data.createdDate;
            document.getElementById('modalCustomerName').textContent = data.customerName;
            document.getElementById('modalCustomerPhone').textContent = data.customerPhone;
            document.getElementById('modalCustomerEmail').textContent = data.customerEmail;
            document.getElementById('modalVehicleNo').textContent = data.vehicleNo;
            document.getElementById('modalMechanicName').textContent = data.mechanicName;
            
            // Show modal
            const modal = document.getElementById('jobCardDetailsModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }
        
        function closeJobCardModal() {
            const modal = document.getElementById('jobCardDetailsModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }
        
        // Close modal on escape key or clicking outside
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeJobCardModal();
        });
        
        document.getElementById('jobCardDetailsModal').addEventListener('click', (e) => {
            if (e.target === document.getElementById('jobCardDetailsModal')) {
                closeJobCardModal();
            }
        });
    </script>
</body>

</html>