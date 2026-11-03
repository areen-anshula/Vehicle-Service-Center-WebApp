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
    <aside class="fixed left-0 top-0 z-50 hidden h-screen w-64 flex-col overflow-y-auto bg-gray-950 text-white shadow-xl lg:flex">
        <!-- Logo -->
        <div class="border-b border-white/10 px-5 py-5">
            <a href="admin-dashboard.html" class="flex items-center gap-3">
                <img src="../../MEDIA/AutoPulse Automotive Service Logo.png" alt="AutoPulse Logo" class="h-15 w-15 rounded-lg object-contain">
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
                    <a href="admin-dashboard.html" class="flex items-center gap-3 rounded-lg bg-[#E89000] px-4 py-3 text-xs font-semibold text-white shadow-sm">
                        <i class="fa-solid fa-table-columns w-4 text-center"></i>
                        Dashboard
                    </a>
                </li>

                <!-- User Management -->
                <li>
                    <a href="userManagement.php" class="group flex items-center justify-between rounded-lg px-4 py-3 text-xs font-medium text-gray-300 transition duration-200 hover:bg-white/10 hover:text-white">
                        <span class="flex items-center gap-3">
                            <i class="fa-solid fa-users w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>
                            User Management
                        </span>
                        <i class="fa-solid fa-chevron-right text-[9px] text-gray-500"></i>
                    </a>
                </li>

                <!-- Services -->
                <li>
                    <a href="service-management.html" class="group flex items-center justify-between rounded-lg px-4 py-3 text-xs font-medium text-gray-300 transition duration-200 hover:bg-white/10 hover:text-white">
                        <span class="flex items-center gap-3">
                            <i class="fa-solid fa-screwdriver-wrench w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>
                            Services
                        </span>
                        <i class="fa-solid fa-chevron-right text-[9px] text-gray-500"></i>
                    </a>
                </li>

                <!-- Inventory -->
                <li>
                    <a href="inventory.html" class="group flex items-center justify-between rounded-lg px-4 py-3 text-xs font-medium text-gray-300 transition duration-200 hover:bg-white/10 hover:text-white">
                        <span class="flex items-center gap-3">
                            <i class="fa-solid fa-boxes-stacked w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>
                            Inventory
                        </span>
                        <i class="fa-solid fa-chevron-right text-[9px] text-gray-500"></i>
                    </a>
                </li>

                <!-- Job Cards -->
                <li>
                    <a href="job-cards.html" class="group flex items-center justify-between rounded-lg px-4 py-3 text-xs font-medium text-gray-300 transition duration-200 hover:bg-white/10 hover:text-white">
                        <span class="flex items-center gap-3">
                            <i class="fa-solid fa-file-circle-check w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>
                            Job Cards
                        </span>
                        <i class="fa-solid fa-chevron-right text-[9px] text-gray-500"></i>
                    </a>
                </li>

                <!-- Reports -->
                <li>
                    <a href="reports.html" class="group flex items-center justify-between rounded-lg px-4 py-3 text-xs font-medium text-gray-300 transition duration-200 hover:bg-white/10 hover:text-white">
                        <span class="flex items-center gap-3">
                            <i class="fa-solid fa-chart-column w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>
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
                        <a href="admin-profile.html" class="group flex items-center gap-3 rounded-lg px-4 py-3 text-xs font-medium text-gray-300 transition hover:bg-white/10 hover:text-white">
                            <i class="fa-solid fa-user w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>
                            Profile
                        </a>
                    </li>
                    <li>
                        <a href="login.html" class="flex items-center gap-3 rounded-lg px-4 py-3 text-xs font-medium text-red-400 transition hover:bg-red-500/10 hover:text-red-300">
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
                        Welcome back, <span class="font-semibold text-[#E89000]">Admin</span>
                    </p>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center gap-3 sm:gap-5">

                    <!-- Notification -->
                    
<!-- Notification Dropdown -->
<details class="group relative">
    <!-- Notification Button -->
    <summary aria-label="Notifications" class="relative flex h-9 w-9 cursor-pointer list-none items-center justify-center rounded-full bg-gray-100 transition hover:bg-orange-50 [&::-webkit-details-marker]:hidden">
        <i class="fa-regular fa-bell text-base text-gray-700 transition group-open:text-[#E89000]"></i>
        <span class="absolute right-0 top-0 flex h-4 w-4 items-center justify-center rounded-full border-2 border-white bg-red-500 text-[9px] font-bold text-white">
            3
        </span>
    </summary>

    <!-- Dropdown Panel -->
    <div class="absolute right-0 top-12 z-50 w-80 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl sm:w-96">

        <!-- Header -->
        <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
            <div>
                <h3 class="text-sm font-bold text-gray-900">Notifications</h3>
                <p class="mt-0.5 text-[11px] text-gray-500">You have 3 unread notifications</p>
            </div>
            <button type="button" class="text-[11px] font-semibold text-[#E89000] hover:text-orange-600">
                Mark all as read
            </button>
        </div>

        <!-- Notification List -->
        <div class="max-h-80 overflow-y-auto">

            <!-- Notification 1 -->
            <div class="flex gap-3 border-b border-gray-100 bg-orange-50/40 px-4 py-3 transition hover:bg-gray-50">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-100">
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
            <div class="flex gap-3 border-b border-gray-100 bg-orange-50/40 px-4 py-3 transition hover:bg-gray-50">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-green-100">
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
            <div class="flex gap-3 border-b border-gray-100 bg-orange-50/40 px-4 py-3 transition hover:bg-gray-50">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-purple-100">
                    <i class="fa-solid fa-user-plus text-sm text-purple-600"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-xs font-semibold text-gray-800">New Customer Registered</p>
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
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-orange-100">
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
            <a href="#" class="text-xs font-semibold text-[#E89000] transition hover:text-orange-600">
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
        <span class="hidden items-center gap-1.5 rounded-full bg-green-50 px-3 py-1.5 text-[10px] font-semibold text-green-600 sm:inline-flex">
            <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
            Live Overview
        </span>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

        <!-- Total Customers -->
        <div class="group rounded-xl border border-gray-100 border-l-4 border-l-[#E89000] bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-50 transition group-hover:bg-orange-100">
                        <i class="fa-solid fa-users text-xl text-[#E89000]"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-medium text-gray-500">Total Customers</p>
                        <h3 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">245</h3>
                    </div>
                </div>
                <i class="fa-solid fa-arrow-trend-up text-sm text-[#E89000]"></i>
            </div>
            <div class="mt-4 border-t border-gray-100 pt-3">
                <p class="text-[10px] text-gray-500">Registered customers</p>
            </div>
        </div>

        <!-- Total Mechanics -->
        <div class="group rounded-xl border border-gray-100 border-l-4 border-l-blue-500 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 transition group-hover:bg-blue-100">
                        <i class="fa-solid fa-user-gear text-xl text-blue-500"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-medium text-gray-500">Total Mechanics</p>
                        <h3 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">18</h3>
                    </div>
                </div>
                <i class="fa-solid fa-users text-sm text-blue-500"></i>
            </div>
            <div class="mt-4 border-t border-gray-100 pt-3">
                <p class="text-[10px] text-gray-500">Registered service technicians</p>
            </div>
        </div>

        <!-- Active Job Cards -->
        <div class="group rounded-xl border border-gray-100 border-l-4 border-l-green-500 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-50 transition group-hover:bg-green-100">
                        <i class="fa-solid fa-car text-xl text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-medium text-gray-500">Active Job Cards</p>
                        <h3 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">32</h3>
                    </div>
                </div>
                <i class="fa-solid fa-clipboard-list text-sm text-green-600"></i>
            </div>
            <div class="mt-4 border-t border-gray-100 pt-3">
                <p class="text-[10px] text-gray-500">Currently active service jobs</p>
            </div>
        </div>

        <!-- Spare Parts -->
        <div class="group rounded-xl border border-gray-100 border-l-4 border-l-red-500 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-50 transition group-hover:bg-red-100">
                        <i class="fa-solid fa-box-open text-xl text-red-500"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-medium text-gray-500">Total Spare Parts</p>
                        <h3 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">1,250</h3>
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
        <div class="flex flex-col gap-3 border-b border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-100">
                    <i class="fa-solid fa-clipboard-list text-lg text-[#E89000]"></i>
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-base font-bold text-gray-900">Recent Job Cards</h2>
                        <span class="rounded-full bg-orange-100 px-2 py-0.5 text-[10px] font-semibold text-[#E89000]">32 Total</span>
                    </div>
                    <p class="mt-0.5 text-xs text-gray-500">Latest service requests and repair activities</p>
                </div>
            </div>

            <a href="#" class="inline-flex items-center gap-2 text-xs font-semibold text-[#E89000] transition hover:text-[#FFA500]">
                View All Job Cards
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <!-- Search & Filter -->
        <div class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                <input
                    type="text"
                    placeholder="Search by Job Card ID..."
                    class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 pl-9 pr-3 text-xs text-gray-700 outline-none transition placeholder:text-gray-400 focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
            </div>

            <div class="flex flex-col gap-2 sm:flex-row">
                <select class="h-10 rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-600 outline-none transition focus:border-[#E89000] focus:ring-2 focus:ring-orange-100">
                    <option>All Statuses</option>
                    <option>Assigned</option>
                    <option>In Progress</option>
                    <option>Completed</option>
                    <option>Closed</option>
                </select>

                <button class="inline-flex h-10 items-center justify-center gap-2 rounded-lg bg-[#FFA500] px-4 text-xs font-semibold text-white transition hover:bg-[#E89000] cursor-pointer">
                    <i class="fa-solid fa-filter text-[10px]"></i>
                    Filter
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full min-w-[950px] text-left">
                <thead>
                    <tr class="border-y border-gray-100 bg-gray-50">
                        <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">Job Card ID</th>
                        <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">Customer</th>
                        <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">Vehicle No.</th>
                        <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">Assigned Mechanic</th>
                        <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">Created Date</th>
                        <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-5 py-3 text-center text-[10px] font-bold uppercase tracking-wider text-gray-500">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 text-xs text-gray-600">

                    <!-- Row 1 -->
                    <tr class="transition hover:bg-orange-50/30">
                        <td class="px-5 py-3.5">
                            <span class="font-bold text-gray-900">JC-0048</span>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="font-semibold text-gray-800">Nimal Shanaka</div>
                            <span class="mt-0.5 block text-[10px] text-gray-400">Customer</span>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center gap-2 font-medium text-gray-700">
                                <i class="fa-solid fa-car text-[11px] text-gray-400"></i>
                                WP-KK-1234
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2">
                                <div class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-100 text-[10px] font-bold text-blue-600">RP</div>
                                <span class="font-medium text-gray-700">Ruwan Perera</span>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-gray-500">24 Apr 2025</td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-2.5 py-1 text-[10px] font-semibold text-blue-600">
                                <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                Assigned
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <a href="#" class="inline-flex items-center gap-1.5 rounded-md border border-orange-200 px-3 py-1.5 text-[11px] font-semibold text-[#E89000] transition hover:border-[#E89000] hover:bg-[#E89000] hover:text-white">
                                <i class="fa-solid fa-eye"></i>
                                View
                            </a>
                        </td>
                    </tr>

                    <!-- Row 2 -->
                    <tr class="transition hover:bg-orange-50/30">
                        <td class="px-5 py-3.5">
                            <span class="font-bold text-gray-900">JC-0047</span>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="font-semibold text-gray-800">Sithara Fernando</div>
                            <span class="mt-0.5 block text-[10px] text-gray-400">Customer</span>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center gap-2 font-medium text-gray-700">
                                <i class="fa-solid fa-car text-[11px] text-gray-400"></i>
                                WP-AB-5678
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2">
                                <div class="flex h-7 w-7 items-center justify-center rounded-full bg-purple-100 text-[10px] font-bold text-purple-600">KJ</div>
                                <span class="font-medium text-gray-700">Kasun Jayasinghe</span>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-gray-500">23 Apr 2025</td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-orange-50 px-2.5 py-1 text-[10px] font-semibold text-orange-600">
                                <span class="h-1.5 w-1.5 rounded-full bg-orange-500"></span>
                                In Progress
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <a href="#" class="inline-flex items-center gap-1.5 rounded-md border border-orange-200 px-3 py-1.5 text-[11px] font-semibold text-[#E89000] transition hover:border-[#E89000] hover:bg-[#E89000] hover:text-white">
                                <i class="fa-solid fa-eye"></i>
                                View
                            </a>
                        </td>
                    </tr>

                    <!-- Row 3 -->
                    <tr class="transition hover:bg-orange-50/30">
                        <td class="px-5 py-3.5">
                            <span class="font-bold text-gray-900">JC-0046</span>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="font-semibold text-gray-800">Kasun Perera</div>
                            <span class="mt-0.5 block text-[10px] text-gray-400">Customer</span>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center gap-2 font-medium text-gray-700">
                                <i class="fa-solid fa-car text-[11px] text-gray-400"></i>
                                WP-BB-9012
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2">
                                <div class="flex h-7 w-7 items-center justify-center rounded-full bg-green-100 text-[10px] font-bold text-green-600">MS</div>
                                <span class="font-medium text-gray-700">Malith Silva</span>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-gray-500">22 Apr 2025</td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-1 text-[10px] font-semibold text-green-600">
                                <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                Completed
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <a href="#" class="inline-flex items-center gap-1.5 rounded-md border border-orange-200 px-3 py-1.5 text-[11px] font-semibold text-[#E89000] transition hover:border-[#E89000] hover:bg-[#E89000] hover:text-white">
                                <i class="fa-solid fa-eye"></i>
                                View
                            </a>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

        <!-- Footer / Pagination -->
        <div class="flex flex-col gap-3 border-t border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-[11px] text-gray-500">
                Showing <span class="font-semibold text-gray-800">1–3</span> of <span class="font-semibold text-gray-800">32</span> job cards
            </p>

            <div class="flex items-center gap-1.5">
                <button disabled class="flex h-8 items-center gap-1 rounded-md border border-gray-200 px-3 text-[11px] font-medium text-gray-400 disabled:cursor-not-allowed">
                    <i class="fa-solid fa-chevron-left text-[9px]"></i>
                    Previous
                </button>
                <button class="flex h-8 w-8 items-center justify-center rounded-md bg-[#E89000] text-xs font-semibold text-white">1</button>
                <button class="flex h-8 w-8 items-center justify-center rounded-md border border-gray-200 text-xs font-medium text-gray-600 transition hover:border-orange-300 hover:text-[#E89000]">2</button>
                <button class="flex h-8 w-8 items-center justify-center rounded-md border border-gray-200 text-xs font-medium text-gray-600 transition hover:border-orange-300 hover:text-[#E89000]">3</button>
                <span class="px-1 text-xs text-gray-400">...</span>
                <button class="flex h-8 items-center gap-1 rounded-md border border-gray-200 px-3 text-[11px] font-medium cursor-pointer text-gray-600 transition hover:border-orange-300 hover:text-[#E89000]">
                    Next
                    <i class="fa-solid fa-chevron-right text-[9px]"></i>
                </button>
            </div>
        </div>

    </div>
</section>


        </div>
    </main>
</div>
</body>

</html>