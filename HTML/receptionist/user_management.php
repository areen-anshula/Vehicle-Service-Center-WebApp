<?php
include "../../PHP/admin_dashboard_backend.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoPulse | User Management</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4.3.3"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css">
</head>

<body class="min-h-screen bg-[#F5F5F5] font-poppins text-gray-800">
<div class="flex min-h-screen">

    <!-- SIDEBAR -->
    <aside class="fixed left-0 top-0 z-50 hidden h-screen w-64 flex-col overflow-y-auto bg-gray-950 text-white shadow-xl lg:flex">
        <div class="border-b border-white/10 px-5 py-5">
            <a href="../homepage.php" class="flex items-center gap-3">
                <img src="../../MEDIA/AutoPulse Automotive Service Logo.png" alt="AutoPulse Logo" class="h-15 w-15 rounded-lg object-contain">
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
                    <a href="dashboard.php" class="group flex items-center gap-3 rounded-lg px-4 py-3 text-xs font-medium text-gray-300 transition duration-200 hover:bg-white/10 hover:text-white">
                        <i class="fa-solid fa-table-columns w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>
                        Dashboard
                    </a>
                </li>

                <!-- User Management - Active -->
                <li>
                    <a href="user_management.php" class="flex items-center justify-between rounded-lg bg-[#E89000] px-4 py-3 text-xs font-semibold text-white shadow-sm">
                        <span class="flex items-center gap-3">
                            <i class="fa-solid fa-users w-4 text-center"></i>
                            User Management
                        </span>
                        <i class="fa-solid fa-chevron-right text-[9px]"></i>
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

                <div>
                    <h2 class="text-lg font-bold text-gray-900 sm:text-xl">Receptionist Dashboard</h2>
                    <p class="mt-0.5 text-[11px] text-gray-500">
                        Welcome back, <span class="font-semibold text-[#E89000]">Admin</span>
                    </p>
                </div>

                <div class="flex items-center gap-3 sm:gap-5">

                    <!-- Notification Dropdown -->
                    <details class="group relative">
                        <summary aria-label="Notifications" class="relative flex h-9 w-9 cursor-pointer list-none items-center justify-center rounded-full bg-gray-100 transition hover:bg-orange-50 [&::-webkit-details-marker]:hidden">
                            <i class="fa-regular fa-bell text-base text-gray-700 transition group-open:text-[#E89000]"></i>
                            <span class="absolute right-0 top-0 flex h-4 w-4 items-center justify-center rounded-full border-2 border-white bg-red-500 text-[9px] font-bold text-white">3</span>
                        </summary>

                        <div class="absolute right-0 top-12 z-50 w-80 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl sm:w-96">
                            <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900">Notifications</h3>
                                    <p class="mt-0.5 text-[11px] text-gray-500">You have 3 unread notifications</p>
                                </div>
                                <button type="button" class="cursor-pointer text-[11px] font-semibold text-[#FFA500] hover:text-[#E89000]">Mark all as read</button>
                            </div>

                            <div class="max-h-80 overflow-y-auto">
                                <div class="flex gap-3 border-b border-gray-100 bg-orange-50/40 px-4 py-3 transition hover:bg-gray-50">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-100">
                                        <i class="fa-solid fa-clipboard-check text-sm text-blue-600"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center justify-between gap-2">
                                            <p class="text-xs font-semibold text-gray-800">New Job Card Assigned</p>
                                            <span class="h-2 w-2 shrink-0 rounded-full bg-[#E89000]"></span>
                                        </div>
                                        <p class="mt-1 text-[11px] leading-relaxed text-gray-500">A new job card JC-0048 has been assigned to a mechanic.</p>
                                        <span class="mt-1.5 block text-[10px] text-gray-400">5 minutes ago</span>
                                    </div>
                                </div>

                                <div class="flex gap-3 border-b border-gray-100 bg-orange-50/40 px-4 py-3 transition hover:bg-gray-50">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-green-100">
                                        <i class="fa-solid fa-circle-check text-sm text-green-600"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center justify-between gap-2">
                                            <p class="text-xs font-semibold text-gray-800">Service Completed</p>
                                            <span class="h-2 w-2 shrink-0 rounded-full bg-[#E89000]"></span>
                                        </div>
                                        <p class="mt-1 text-[11px] leading-relaxed text-gray-500">Job card JC-0046 has been marked as completed.</p>
                                        <span class="mt-1.5 block text-[10px] text-gray-400">30 minutes ago</span>
                                    </div>
                                </div>

                                <div class="flex gap-3 border-b border-gray-100 bg-orange-50/40 px-4 py-3 transition hover:bg-gray-50">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-purple-100">
                                        <i class="fa-solid fa-user-plus text-sm text-purple-600"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center justify-between gap-2">
                                            <p class="text-xs font-semibold text-gray-800">New Customer Registered</p>
                                            <span class="h-2 w-2 shrink-0 rounded-full bg-[#E89000]"></span>
                                        </div>
                                        <p class="mt-1 text-[11px] leading-relaxed text-gray-500">A new customer has registered on AutoPulse.</p>
                                        <span class="mt-1.5 block text-[10px] text-gray-400">1 hour ago</span>
                                    </div>
                                </div>

                                <div class="flex gap-3 px-4 py-3 transition hover:bg-gray-50">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-orange-100">
                                        <i class="fa-solid fa-boxes-stacked text-sm text-[#E89000]"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-semibold text-gray-800">Spare Parts Updated</p>
                                        <p class="mt-1 text-[11px] leading-relaxed text-gray-500">Spare parts inventory has been updated successfully.</p>
                                        <span class="mt-1.5 block text-[10px] text-gray-400">Yesterday</span>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-gray-100 p-3 text-center">
                                <a href="#" class="text-xs font-semibold text-[#FFA500] transition hover:text-[#E89000]">
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

        <!-- PAGE CONTENT: USER MANAGEMENT -->
        <div class="space-y-6 p-4 sm:p-6 lg:p-8">

            <!-- Page Heading -->
            <section class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-100 text-[#E89000]">
                            <i class="fa-solid fa-users text-lg"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold tracking-tight text-gray-900">User Management</h1>
                            <p class="mt-0.5 text-xs text-gray-500">View and manage registered customers</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-[11px] text-gray-500 shadow-sm">
                    <i class="fa-solid fa-house text-[#E89000]"></i>
                    <span>Dashboard</span>
                    <i class="fa-solid fa-chevron-right text-[9px]"></i>
                    <span class="font-semibold text-gray-800">User Management</span>
                </div>
            </section>

            <!-- Customer Statistics -->
            <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

                <!-- Total Customers -->
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-50 text-[#E89000]">
                            <i class="fa-solid fa-users text-lg"></i>
                        </div>
                        <span class="rounded-full bg-orange-50 px-2.5 py-1 text-[10px] font-semibold text-[#E89000]">All Users</span>
                    </div>
                    <h3 class="mt-3 text-2xl font-bold text-gray-900">135</h3>
                    <p class="mt-0.5 text-xs font-semibold text-gray-700">Total Customers</p>
                    <p class="mt-1 text-[10px] text-gray-400">Registered customer accounts</p>
                </div>

                <!-- Active Customers -->
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-50 text-green-600">
                            <i class="fa-solid fa-user-check text-lg"></i>
                        </div>
                        <span class="rounded-full bg-green-50 px-2.5 py-1 text-[10px] font-semibold text-green-600">Active</span>
                    </div>
                    <h3 class="mt-3 text-2xl font-bold text-gray-900"><?php echo "$clients_count"; ?></h3>
                    <p class="mt-0.5 text-xs font-semibold text-gray-700">Active Customers</p>
                    <p class="mt-1 text-[10px] text-gray-400">Currently active accounts</p>
                </div>

                <!-- New Customers -->
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                            <i class="fa-solid fa-user-plus text-lg"></i>
                        </div>
                        <span class="rounded-full bg-blue-50 px-2.5 py-1 text-[10px] font-semibold text-blue-600">This Month</span>
                    </div>
                    <h3 class="mt-3 text-2xl font-bold text-gray-900">14</h3>
                    <p class="mt-0.5 text-xs font-semibold text-gray-700">New Customers</p>
                    <p class="mt-1 text-[10px] text-gray-400">Recently registered customers</p>
                </div>

                <!-- Inactive Customers -->
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-50 text-red-500">
                            <i class="fa-solid fa-user-slash text-lg"></i>
                        </div>
                        <span class="rounded-full bg-red-50 px-2.5 py-1 text-[10px] font-semibold text-red-500">Inactive</span>
                    </div>
                    <h3 class="mt-3 text-2xl font-bold text-gray-900">17</h3>
                    <p class="mt-0.5 text-xs font-semibold text-gray-700">Inactive Customers</p>
                    <p class="mt-1 text-[10px] text-gray-400">Currently inactive accounts</p>
                </div>

            </section>

            <!-- Customer Management Table -->
            <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

                <!-- Table Header -->
                <div class="flex flex-col gap-3 border-b border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-100">
                            <i class="fa-solid fa-address-book text-lg text-[#E89000]"></i>
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="text-base font-bold text-gray-900">Registered Customers</h2>
                                <span class="rounded-full bg-orange-100 px-2 py-0.5 text-[10px] font-semibold text-[#E89000]">248 Customers</span>
                            </div>
                            <p class="mt-0.5 text-xs text-gray-500">View customer information and update their details</p>
                        </div>
                    </div>

                    <span class="inline-flex items-center gap-2 text-[11px] font-medium text-gray-500">
                        <i class="fa-solid fa-shield-halved text-green-600"></i>
                        Customer account management
                    </span>
                </div>

                <!-- Search and Filters -->
                <div class="flex flex-col gap-3 px-5 py-4 lg:flex-row lg:items-center">

                    <div class="relative flex-1">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                        <input
                            type="search"
                            placeholder="Search by customer name, ID, email or phone..."
                            class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 pl-9 pr-3 text-xs text-gray-700 outline-none transition placeholder:text-gray-400 focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                    </div>

                    <div class="flex flex-col gap-2 sm:flex-row">
                        <select class="h-10 rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-600 outline-none transition focus:border-[#E89000] focus:ring-2 focus:ring-orange-100">
                            <option>All Customers</option>
                            <option>Active</option>
                            <option>Inactive</option>
                        </select>

                        <select class="h-10 rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-600 outline-none transition focus:border-[#E89000] focus:ring-2 focus:ring-orange-100">
                            <option>Newest First</option>
                            <option>Oldest First</option>
                            <option>Name A - Z</option>
                            <option>Name Z - A</option>
                        </select>

                        <button type="button" class="inline-flex h-10 cursor-pointer items-center justify-center gap-2 rounded-lg bg-[#FFA500] px-4 text-xs font-semibold text-white transition hover:bg-[#E89000]">
                            <i class="fa-solid fa-filter text-[10px]"></i>
                            Filter
                        </button>
                    </div>
                </div>

                <!-- Customer Table -->
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1050px] text-left">

                        <thead>
                            <tr class="border-y border-gray-100 bg-gray-50">
                                <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">Customer</th>
                                <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">Customer ID</th>
                                <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">Contact Details</th>
                                <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">Registered Date</th>
                                <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">Account Status</th>
                                <th class="px-5 py-3 text-center text-[10px] font-bold uppercase tracking-wider text-gray-500">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 text-xs text-gray-600">

                            <!-- Customer 1 -->
                            <tr class="transition hover:bg-orange-50/30">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-orange-100 text-xs font-bold text-[#E89000]">NS</div>
                                        <div>
                                            <p class="font-semibold text-gray-800">Nimal Shanaka</p>
                                            <p class="mt-0.5 text-[10px] text-gray-400">Customer</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 font-semibold text-gray-800">CUS-0001</td>
                                <td class="px-5 py-3.5">
                                    <p class="font-medium text-gray-700">nimal@gmail.com</p>
                                    <p class="mt-1 text-[10px] text-gray-400">077 123 4567</p>
                                </td>
                                <td class="px-5 py-3.5 text-gray-500">12 Jan 2025</td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-1 text-[10px] font-semibold text-green-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                        Active
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <a href="edit-customer.php?id=1" class="inline-flex cursor-pointer items-center gap-1.5 rounded-md border border-orange-200 px-3 py-1.5 text-[11px] font-semibold text-[#E89000] transition hover:border-[#E89000] hover:bg-[#E89000] hover:text-white">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        Edit
                                    </a>
                                </td>
                            </tr>

                            <!-- Customer 2 -->
                            <tr class="transition hover:bg-orange-50/30">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-purple-100 text-xs font-bold text-purple-600">SF</div>
                                        <div>
                                            <p class="font-semibold text-gray-800">Sithara Fernando</p>
                                            <p class="mt-0.5 text-[10px] text-gray-400">Customer</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 font-semibold text-gray-800">CUS-0002</td>
                                <td class="px-5 py-3.5">
                                    <p class="font-medium text-gray-700">sithara@gmail.com</p>
                                    <p class="mt-1 text-[10px] text-gray-400">071 456 7890</p>
                                </td>
                                <td class="px-5 py-3.5 text-gray-500">18 Feb 2025</td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-1 text-[10px] font-semibold text-green-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                        Active
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <a href="edit-customer.php?id=2" class="inline-flex cursor-pointer items-center gap-1.5 rounded-md border border-orange-200 px-3 py-1.5 text-[11px] font-semibold text-[#E89000] transition hover:border-[#E89000] hover:bg-[#E89000] hover:text-white">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        Edit
                                    </a>
                                </td>
                            </tr>

                            <!-- Customer 3 -->
                            <tr class="transition hover:bg-orange-50/30">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-600">KP</div>
                                        <div>
                                            <p class="font-semibold text-gray-800">Kasun Perera</p>
                                            <p class="mt-0.5 text-[10px] text-gray-400">Customer</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 font-semibold text-gray-800">CUS-0003</td>
                                <td class="px-5 py-3.5">
                                    <p class="font-medium text-gray-700">kasun@gmail.com</p>
                                    <p class="mt-1 text-[10px] text-gray-400">075 789 1234</p>
                                </td>
                                <td class="px-5 py-3.5 text-gray-500">05 Mar 2025</td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-1 text-[10px] font-semibold text-green-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                        Active
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <a href="edit-customer.php?id=3" class="inline-flex cursor-pointer items-center gap-1.5 rounded-md border border-orange-200 px-3 py-1.5 text-[11px] font-semibold text-[#E89000] transition hover:border-[#E89000] hover:bg-[#E89000] hover:text-white">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        Edit
                                    </a>
                                </td>
                            </tr>

                            <!-- Customer 4 -->
                            <tr class="transition hover:bg-orange-50/30">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-teal-100 text-xs font-bold text-teal-600">DM</div>
                                        <div>
                                            <p class="font-semibold text-gray-800">Dilshan Maduranga</p>
                                            <p class="mt-0.5 text-[10px] text-gray-400">Customer</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 font-semibold text-gray-800">CUS-0004</td>
                                <td class="px-5 py-3.5">
                                    <p class="font-medium text-gray-700">dilshan@gmail.com</p>
                                    <p class="mt-1 text-[10px] text-gray-400">076 234 5678</p>
                                </td>
                                <td class="px-5 py-3.5 text-gray-500">14 Mar 2025</td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-2.5 py-1 text-[10px] font-semibold text-red-500">
                                        <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                        Inactive
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <a href="edit-customer.php?id=4" class="inline-flex cursor-pointer items-center gap-1.5 rounded-md border border-orange-200 px-3 py-1.5 text-[11px] font-semibold text-[#E89000] transition hover:border-[#E89000] hover:bg-[#E89000] hover:text-white">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        Edit
                                    </a>
                                </td>
                            </tr>

                            <!-- Customer 5 -->
                            <tr class="transition hover:bg-orange-50/30">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-pink-100 text-xs font-bold text-pink-600">AF</div>
                                        <div>
                                            <p class="font-semibold text-gray-800">Amaya Fernando</p>
                                            <p class="mt-0.5 text-[10px] text-gray-400">Customer</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 font-semibold text-gray-800">CUS-0005</td>
                                <td class="px-5 py-3.5">
                                    <p class="font-medium text-gray-700">amaya@gmail.com</p>
                                    <p class="mt-1 text-[10px] text-gray-400">072 345 6789</p>
                                </td>
                                <td class="px-5 py-3.5 text-gray-500">22 Apr 2025</td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-1 text-[10px] font-semibold text-green-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                        Active
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <a href="edit-customer.php?id=5" class="inline-flex cursor-pointer items-center gap-1.5 rounded-md border border-orange-200 px-3 py-1.5 text-[11px] font-semibold text-[#E89000] transition hover:border-[#E89000] hover:bg-[#E89000] hover:text-white">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        Edit
                                    </a>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>

                <!-- Table Footer -->
                <div class="flex flex-col gap-3 border-t border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-[11px] text-gray-500">
                        Showing <span class="font-semibold text-gray-800">1–5</span> of <span class="font-semibold text-gray-800">248</span> customers
                    </p>

                    <div class="flex items-center gap-1.5">
                        <button disabled class="flex h-8 items-center gap-1 rounded-md border border-gray-200 px-3 text-[11px] font-medium text-gray-400 disabled:cursor-not-allowed">
                            <i class="fa-solid fa-chevron-left text-[9px]"></i>
                            Previous
                        </button>
                        <button class="flex h-8 w-8 items-center justify-center rounded-md bg-[#E89000] text-xs font-semibold text-white">1</button>
                        <button class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-md border border-gray-200 text-xs font-medium text-gray-600 transition hover:border-orange-300 hover:text-[#E89000]">2</button>
                        <button class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-md border border-gray-200 text-xs font-medium text-gray-600 transition hover:border-orange-300 hover:text-[#E89000]">3</button>
                        <span class="px-1 text-xs text-gray-400">...</span>
                        <button class="flex h-8 cursor-pointer items-center gap-1 rounded-md border border-gray-200 px-3 text-[11px] font-medium text-gray-600 transition hover:border-orange-300 hover:text-[#E89000]">
                            Next
                            <i class="fa-solid fa-chevron-right text-[9px]"></i>
                        </button>
                    </div>
                </div>

            </section>

            <!-- Customer Edit Information -->
            <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

                <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-100 text-[#E89000]">
                        <i class="fa-solid fa-user-pen text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Customer Information</h2>
                        <p class="mt-0.5 text-xs text-gray-500">Update a customer's personal and contact details</p>
                    </div>
                </div>

                <form action="../../PHP/update_customer.php" method="POST" class="space-y-5 p-5">

                    <div class="flex flex-col gap-3 rounded-lg border border-orange-100 bg-orange-50/50 p-4 sm:flex-row sm:items-center">
                        <div class="flex h-11 w-11 items-center justify-center rounded-full bg-orange-100 text-sm font-bold text-[#E89000]">NS</div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">Editing Customer: Nimal Shanaka</p>
                            <p class="mt-0.5 text-[11px] text-gray-500">Customer ID: CUS-0001</p>
                        </div>
                        <span class="sm:ml-auto rounded-full bg-green-100 px-3 py-1 text-[10px] font-semibold text-green-600">Active Account</span>
                    </div>

                    <input type="hidden" name="customer_id" value="1">

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                        <div>
                            <label for="customer-name" class="mb-1.5 block text-xs font-semibold text-gray-700">Full Name</label>
                            <input id="customer-name" name="full_name" type="text" value="Nimal Shanaka" placeholder="Enter full name" class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                        </div>

                        <div>
                            <label for="customer-email" class="mb-1.5 block text-xs font-semibold text-gray-700">Email Address</label>
                            <input id="customer-email" name="email" type="email" value="nimal@gmail.com" placeholder="Enter email address" class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                        </div>

                        <div>
                            <label for="customer-phone" class="mb-1.5 block text-xs font-semibold text-gray-700">Phone Number</label>
                            <input id="customer-phone" name="phone" type="tel" value="077 123 4567" placeholder="Enter phone number" class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                        </div>

                        <div>
                            <label for="customer-status" class="mb-1.5 block text-xs font-semibold text-gray-700">Account Status</label>
                            <select id="customer-status" name="status" class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label for="customer-address" class="mb-1.5 block text-xs font-semibold text-gray-700">Address</label>
                            <textarea id="customer-address" name="address" rows="3" placeholder="Enter customer address" class="w-full resize-none rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">No. 25, Main Street, Colombo</textarea>
                        </div>

                    </div>

                    <div class="flex flex-col-reverse gap-2 border-t border-gray-100 pt-4 sm:flex-row sm:justify-end">
                        <button type="reset" class="inline-flex h-10 cursor-pointer items-center justify-center gap-2 rounded-lg border border-gray-200 px-5 text-xs font-semibold text-gray-600 transition hover:bg-gray-50">
                            <i class="fa-solid fa-rotate-left"></i>
                            Reset Changes
                        </button>

                        <button type="submit" class="inline-flex h-10 cursor-pointer items-center justify-center gap-2 rounded-lg bg-[#E89000] px-5 text-xs font-semibold text-white shadow-sm transition hover:bg-orange-600">
                            <i class="fa-solid fa-floppy-disk"></i>
                            Save Changes
                        </button>
                    </div>

                </form>
            </section>

        </div>
    </main>
</div>
</body>
</html>
