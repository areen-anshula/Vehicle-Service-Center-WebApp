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
            <a href="../homepage.php" class="flex items-center gap-3">
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
                <!-- User Management -->
                <li>
                    <a href="userManagement.php" class="flex items-center gap-3 rounded-lg bg-[#E89000] px-4 py-3 text-xs font-semibold text-white shadow-sm">
                        <i class="fa-solid fa-table-columns w-4 text-center"></i>
                        Dashboard
                    </a>
                </li>

                <li>
                    <a href="dashboard.php" class="group flex items-center justify-between rounded-lg px-4 py-3 text-xs font-medium text-gray-300 transition duration-200 hover:bg-white/10 hover:text-white">
                        <span class="flex items-center gap-3">
                            <i class="fa-solid fa-users w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>
                            User Management
                        </span>
                        <i class="fa-solid fa-chevron-right text-[9px] text-white-500"></i>
                    </a>
                </li>

                <!-- Services -->
                <li>
                    <a href="services.php" class="group flex items-center justify-between rounded-lg px-4 py-3 text-xs font-medium text-gray-300 transition duration-200 hover:bg-white/10 hover:text-white">
                        <span class="flex items-center gap-3">
                            <i class="fa-solid fa-screwdriver-wrench w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>
                            Services
                        </span>
                        <i class="fa-solid fa-chevron-right text-[9px] text-gray-500"></i>
                    </a>
                </li>

                <!-- Inventory -->
                <li>
                    <a href="inventory.php" class="group flex items-center justify-between rounded-lg px-4 py-3 text-xs font-medium text-gray-300 transition duration-200 hover:bg-white/10 hover:text-white">
                        <span class="flex items-center gap-3">
                            <i class="fa-solid fa-boxes-stacked w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>
                            Inventory
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
                    <h2 class="text-lg font-bold text-gray-900 sm:text-xl">Mechanic Dashboard</h2>
                    <p class="mt-0.5 text-[11px] text-gray-500">
                        Welcome back, <span class="font-semibold text-[#E89000]">Mechanic</span>
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
<section class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-100 text-[#E89000]">
                    <i class="fa-solid fa-gauge-high text-lg"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-gray-900">Dashboard Overview</h1>
                    <p class="mt-0.5 text-xs text-gray-500">Track your assigned vehicle service jobs and daily progress</p>
                </div>
            </div>

            <div class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-[11px] text-gray-500 shadow-sm">
                <i class="fa-solid fa-house text-[#E89000]"></i>Dashboard
            </div>
        </section>

        <!-- STATISTICS -->
        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">

            <!-- Assigned Jobs -->
            <div class="rounded-xl border border-orange-100 bg-gradient-to-br from-white to-orange-50/70 p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                <div class="flex items-center justify-between">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-100 text-[#E89000]">
                        <i class="fa-solid fa-clipboard-list text-lg"></i>
                    </div>
                    <span class="rounded-full bg-orange-50 px-2.5 py-1 text-[10px] font-semibold text-orange-600">Assigned</span>
                </div>
                <p class="mt-4 text-xs font-medium text-gray-500">Assigned Jobs</p>
                <div class="mt-1 flex items-end justify-between">
                    <h3 class="text-3xl font-bold text-gray-900">5</h3>
                    <span class="text-[10px] text-gray-400">Awaiting work</span>
                </div>
            </div>

            <!-- In Progress -->
            <div class="rounded-xl border border-blue-100 bg-gradient-to-br from-white to-blue-50/70 p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                <div class="flex items-center justify-between">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100 text-blue-600">
                        <i class="fa-solid fa-gears text-lg"></i>
                    </div>
                    <span class="rounded-full bg-blue-50 px-2.5 py-1 text-[10px] font-semibold text-blue-600">Ongoing</span>
                </div>
                <p class="mt-4 text-xs font-medium text-gray-500">In Progress</p>
                <div class="mt-1 flex items-end justify-between">
                    <h3 class="text-3xl font-bold text-gray-900">3</h3>
                    <span class="text-[10px] text-gray-400">Currently working</span>
                </div>
            </div>

            <!-- Completed Jobs -->
            <div class="rounded-xl border border-green-100 bg-gradient-to-br from-white to-green-50/70 p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg sm:col-span-2 xl:col-span-1">
                <div class="flex items-center justify-between">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-green-100 text-green-600">
                        <i class="fa-solid fa-circle-check text-lg"></i>
                    </div>
                    <span class="rounded-full bg-green-50 px-2.5 py-1 text-[10px] font-semibold text-green-600">Completed</span>
                </div>
                <p class="mt-4 text-xs font-medium text-gray-500">Completed Jobs</p>
                <div class="mt-1 flex items-end justify-between">
                    <h3 class="text-3xl font-bold text-gray-900">8</h3>
                    <span class="text-[10px] text-gray-400">Successfully finished</span>
                </div>
            </div>
        </section>

        <!-- WORK SUMMARY -->
        <section class="grid grid-cols-1 gap-4 xl:grid-cols-3">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm xl:col-span-2">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-gray-900">Work Summary</h2>
                        <p class="mt-1 text-[11px] text-gray-500">Your current job distribution</p>
                    </div>
                    <span class="w-fit rounded-full bg-gray-100 px-3 py-1 text-[10px] text-gray-600">All assigned jobs</span>
                </div>

                <div class="mt-5 space-y-4">
                    <div>
                        <div class="mb-2 flex justify-between text-[11px]">
                            <span class="font-medium text-gray-600">Assigned</span>
                            <span class="font-semibold text-gray-800">5 jobs</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                            <div class="h-full w-[31%] rounded-full bg-[#E89000]"></div>
                        </div>
                    </div>

                    <div>
                        <div class="mb-2 flex justify-between text-[11px]">
                            <span class="font-medium text-gray-600">In Progress</span>
                            <span class="font-semibold text-gray-800">3 jobs</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                            <div class="h-full w-[19%] rounded-full bg-blue-500"></div>
                        </div>
                    </div>

                    <div>
                        <div class="mb-2 flex justify-between text-[11px]">
                            <span class="font-medium text-gray-600">Completed</span>
                            <span class="font-semibold text-gray-800">8 jobs</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                            <div class="h-full w-[50%] rounded-full bg-green-500"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TODAY'S FOCUS -->
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-100 text-[#E89000]">
                        <i class="fa-solid fa-calendar-day"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-gray-900">Today's Focus</h2>
                        <p class="mt-0.5 text-[11px] text-gray-500">Your workshop schedule</p>
                    </div>
                </div>

                <div class="mt-5 rounded-lg bg-orange-50 p-4">
                    <p class="text-[10px] font-semibold uppercase tracking-wide text-orange-700">Jobs to review</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900">5</p>
                    <p class="mt-1 text-[11px] text-gray-600">Check your assigned jobs and keep each job status up to date.</p>
                </div>

                <a href="#assignedJobs" class="mt-4 inline-flex items-center gap-2 text-xs font-semibold text-[#E89000] hover:text-orange-700">
                    View assigned jobs <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </section>

        <!-- ASSIGNED JOBS TABLE -->
        <section id="assignedJobs" class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-5 py-4">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-bold text-gray-900">My Assigned Jobs</h2>
                            <span class="rounded-full bg-orange-50 px-2.5 py-1 text-[10px] font-semibold text-[#E89000]">5 Jobs</span>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">View the vehicle service jobs assigned to you</p>
                    </div>

                    <div class="flex flex-col gap-2 sm:flex-row">
                        <div class="relative">
                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                            <input id="jobSearch" type="search" placeholder="Search job, customer or vehicle..." class="h-10 w-full rounded-lg border border-gray-200 bg-white pl-9 pr-3 text-xs outline-none placeholder:text-gray-400 focus:border-[#E89000] focus:ring-2 focus:ring-orange-100 sm:w-64">
                        </div>

                        <select id="statusFilter" class="h-10 rounded-lg border border-gray-200 bg-white px-3 text-xs text-gray-600 outline-none focus:border-[#E89000] focus:ring-2 focus:ring-orange-100">
                            <option value="all">All Statuses</option>
                            <option value="assigned">Assigned</option>
                            <option value="in progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>

                        <button id="clearFilters" type="button" class="h-10 rounded-lg bg-[#E89000] px-4 text-xs font-semibold text-white hover:bg-orange-600">
                            Clear
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[850px] text-left">
                    <thead class="bg-gray-50">
                        <tr class="text-[10px] font-bold uppercase tracking-wide text-gray-500">
                            <th class="px-5 py-3">Job Card ID</th>
                            <th class="px-5 py-3">Customer</th>
                            <th class="px-5 py-3">Vehicle</th>
                            <th class="px-5 py-3">Assigned Date</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody id="jobsTableBody" class="divide-y divide-gray-100">

                        <!-- JOB 1 -->
                        <tr class="job-row hover:bg-orange-50/30" data-search="jc-0048 nimal perera cab-1234 toyota axio" data-status="assigned" data-id="JC-0048" data-customer="Nimal Perera" data-phone="077 123 4567" data-vehicle="CAB-1234 - Toyota Axio" data-date="05 Nov 2026" data-appointment="06 Nov 2026, 09:00 AM" data-services="Engine Oil Change, Battery Inspection" data-note="Please check the engine oil and battery condition.">
                            <td class="px-5 py-4 text-xs font-bold text-gray-800">JC-0048</td>
                            <td class="px-5 py-4">
                                <p class="text-xs font-semibold">Nimal Perera</p>
                                <p class="mt-1 text-[10px] text-gray-400">Customer</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-xs font-semibold">CAB-1234</p>
                                <p class="mt-1 text-[10px] text-gray-500">Toyota Axio</p>
                            </td>
                            <td class="px-5 py-4 text-xs text-gray-600">05 Nov 2026</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full bg-orange-50 px-2.5 py-1 text-[10px] font-semibold text-orange-600">Assigned</span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <button class="view-job rounded-lg border border-gray-200 px-3 py-2 text-[10px] font-semibold text-gray-600 hover:border-[#E89000] hover:text-[#E89000]">
                                    <i class="fa-regular fa-eye mr-1"></i>View
                                </button>
                            </td>
                        </tr>

                        <!-- JOB 2 -->
                        <tr class="job-row hover:bg-orange-50/30" data-search="jc-0047 kasun fernando kdh-5678 honda civic" data-status="in progress" data-id="JC-0047" data-customer="Kasun Fernando" data-phone="071 234 5678" data-vehicle="KDH-5678 - Honda Civic" data-date="04 Nov 2026" data-appointment="05 Nov 2026, 10:30 AM" data-services="Full Vehicle Service, Engine Diagnostics" data-note="Customer reported an unusual engine noise.">
                            <td class="px-5 py-4 text-xs font-bold">JC-0047</td>
                            <td class="px-5 py-4">
                                <p class="text-xs font-semibold">Kasun Fernando</p>
                                <p class="mt-1 text-[10px] text-gray-400">Customer</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-xs font-semibold">KDH-5678</p>
                                <p class="mt-1 text-[10px] text-gray-500">Honda Civic</p>
                            </td>
                            <td class="px-5 py-4 text-xs text-gray-600">04 Nov 2026</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full bg-blue-50 px-2.5 py-1 text-[10px] font-semibold text-blue-600">In Progress</span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <button class="view-job rounded-lg border border-gray-200 px-3 py-2 text-[10px] font-semibold text-gray-600 hover:border-[#E89000] hover:text-[#E89000]">
                                    <i class="fa-regular fa-eye mr-1"></i>View
                                </button>
                            </td>
                        </tr>

                        <!-- JOB 3 -->
                        <tr class="job-row hover:bg-orange-50/30" data-search="jc-0046 amali silva wp-9012 suzuki swift" data-status="completed" data-id="JC-0046" data-customer="Amali Silva" data-phone="076 345 6789" data-vehicle="WP-9012 - Suzuki Swift" data-date="03 Nov 2026" data-appointment="03 Nov 2026, 01:00 PM" data-services="Brake Inspection, Brake Pad Replacement" data-note="Brake inspection and replacement completed.">
                            <td class="px-5 py-4 text-xs font-bold">JC-0046</td>
                            <td class="px-5 py-4">
                                <p class="text-xs font-semibold">Amali Silva</p>
                                <p class="mt-1 text-[10px] text-gray-400">Customer</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-xs font-semibold">WP-9012</p>
                                <p class="mt-1 text-[10px] text-gray-500">Suzuki Swift</p>
                            </td>
                            <td class="px-5 py-4 text-xs text-gray-600">03 Nov 2026</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full bg-green-50 px-2.5 py-1 text-[10px] font-semibold text-green-600">Completed</span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <button class="view-job rounded-lg border border-gray-200 px-3 py-2 text-[10px] font-semibold text-gray-600 hover:border-[#E89000] hover:text-[#E89000]">
                                    <i class="fa-regular fa-eye mr-1"></i>View
                                </button>
                            </td>
                        </tr>

                        <!-- JOB 4 -->
                        <tr class="job-row hover:bg-orange-50/30" data-search="jc-0045 tharindu jayasinghe cba-7788 toyota prius" data-status="assigned" data-id="JC-0045" data-customer="Tharindu Jayasinghe" data-phone="075 456 7890" data-vehicle="CBA-7788 - Toyota Prius" data-date="02 Nov 2026" data-appointment="06 Nov 2026, 11:30 AM" data-services="Hybrid Battery Inspection" data-note="Please inspect hybrid battery health.">
                            <td class="px-5 py-4 text-xs font-bold">JC-0045</td>
                            <td class="px-5 py-4">
                                <p class="text-xs font-semibold">Tharindu Jayasinghe</p>
                                <p class="mt-1 text-[10px] text-gray-400">Customer</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-xs font-semibold">CBA-7788</p>
                                <p class="mt-1 text-[10px] text-gray-500">Toyota Prius</p>
                            </td>
                            <td class="px-5 py-4 text-xs text-gray-600">02 Nov 2026</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full bg-orange-50 px-2.5 py-1 text-[10px] font-semibold text-orange-600">Assigned</span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <button class="view-job rounded-lg border border-gray-200 px-3 py-2 text-[10px] font-semibold text-gray-600 hover:border-[#E89000] hover:text-[#E89000]">
                                    <i class="fa-regular fa-eye mr-1"></i>View
                                </button>
                            </td>
                        </tr>

                        <!-- JOB 5 -->
                        <tr class="job-row hover:bg-orange-50/30" data-search="jc-0044 dinuka wijesinghe wp-cab-4433 nissan march" data-status="in progress" data-id="JC-0044" data-customer="Dinuka Wijesinghe" data-phone="078 567 8901" data-vehicle="WP-CAB-4433 - Nissan March" data-date="01 Nov 2026" data-appointment="05 Nov 2026, 02:00 PM" data-services="Engine Diagnostics, Air Conditioning Service" data-note="Air conditioning is not cooling properly.">
                            <td class="px-5 py-4 text-xs font-bold">JC-0044</td>
                            <td class="px-5 py-4">
                                <p class="text-xs font-semibold">Dinuka Wijesinghe</p>
                                <p class="mt-1 text-[10px] text-gray-400">Customer</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-xs font-semibold">WP-CAB-4433</p>
                                <p class="mt-1 text-[10px] text-gray-500">Nissan March</p>
                            </td>
                            <td class="px-5 py-4 text-xs text-gray-600">01 Nov 2026</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full bg-blue-50 px-2.5 py-1 text-[10px] font-semibold text-blue-600">In Progress</span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <button class="view-job rounded-lg border border-gray-200 px-3 py-2 text-[10px] font-semibold text-gray-600 hover:border-[#E89000] hover:text-[#E89000]">
                                    <i class="fa-regular fa-eye mr-1"></i>View
                                </button>
                            </td>
                        </tr>

                        <!-- NO RESULTS -->
                        <tr id="noResults" class="hidden">
                            <td colspan="6" class="px-5 py-12 text-center">
                                <i class="fa-solid fa-magnifying-glass text-2xl text-gray-300"></i>
                                <p class="mt-3 text-sm font-semibold text-gray-700">No matching jobs found</p>
                                <p class="mt-1 text-xs text-gray-500">Try changing your search or status filter.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- TABLE FOOTER -->
            <div class="flex flex-col gap-2 border-t border-gray-100 px-5 py-3 sm:flex-row sm:items-center sm:justify-between">
                <p id="resultsCount" class="text-[11px] text-gray-500">Showing 5 of 5 assigned jobs</p>
                <div class="flex items-center gap-1">
                    <button disabled class="h-8 rounded-lg border border-gray-200 px-3 text-[10px] text-gray-400">Previous</button>
                    <button class="h-8 rounded-lg bg-[#E89000] px-3 text-[10px] font-semibold text-white">1</button>
                    <button class="h-8 rounded-lg border border-gray-200 px-3 text-[10px] text-gray-500">Next</button>
                </div>
            </div>
        </section>


        </div>
    </main>
</div>
</body>

</html>