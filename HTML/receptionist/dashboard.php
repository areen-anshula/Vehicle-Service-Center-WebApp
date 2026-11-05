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
                <li>
                    <a href="dashboard.php" class="flex items-center gap-3 rounded-lg bg-[#E89000] px-4 py-3 text-xs font-semibold text-white shadow-sm">
                        <i class="fa-solid fa-table-columns w-4 text-center"></i>
                        Dashboard
                    </a>
                </li>

                <!-- User Management -->
                <li>
                    <a href="user_management.php" class="group flex items-center justify-between rounded-lg px-4 py-3 text-xs font-medium text-gray-300 transition duration-200 hover:bg-white/10 hover:text-white">
                        <span class="flex items-center gap-3">
                            <i class="fa-solid fa-users w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>
                            User Management
                        </span>
                        <i class="fa-solid fa-chevron-right text-[9px] text-gray-500"></i>
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

                <!-- Page Title -->
                <div>
                    <h2 class="text-lg font-bold text-gray-900 sm:text-xl">Receptionist Dashboard</h2>
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
            <button type="button" class="text-[11px] font-semibold text-[#FFA500] hover:text-[#E89000] cursor-pointer">
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
            <a href="#" class="text-xs font-semibold text-[#FFA500] transition hover:text-[#E89000]">
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


<!-- Receptionist Dashboard Statistics -->

<!-- Reception Dashboard Status Overview -->
<section class="mb-6">

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

                <div class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-[11px] text-gray-500 shadow-sm">
                    <i class="fa-solid fa-house text-[#E89000]"></i>
                    <span>Dashboard</span>
                </div>
            </section>

    <!-- 4 Column Grid -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4 mt-5">

        <!-- Today's Appointments - Full Width -->
        <div class="group relative overflow-hidden rounded-2xl border border-orange-100 bg-gradient-to-br from-white to-orange-50/70 p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-orange-100/60 sm:col-span-2 xl:col-span-4">

            <div class="relative z-10 flex flex-col justify-between gap-5 sm:flex-row sm:items-center">

                <div class="flex items-center gap-4">

                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-orange-100">
                        <i class="fa-solid fa-calendar-days text-2xl text-[#E89000]"></i>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-gray-600">
                            Today's Appointments
                        </p>

                        <div class="mt-1 flex items-baseline gap-2">
                            <h3 class="text-4xl font-bold tracking-tight text-gray-900">
                                12
                            </h3>
                            <span class="text-xs font-medium text-green-600">
                                Scheduled today
                            </span>
                        </div>

                        <p class="mt-1 text-xs text-gray-500">
                            Total appointments scheduled for today
                        </p>
                    </div>

                </div>

                <div class="flex flex-wrap items-center gap-3">

                    <div class="rounded-xl border border-orange-100 bg-white/80 px-4 py-3">
                        <p class="text-[10px] font-medium text-gray-500">
                            Appointment Status
                        </p>
                        <p class="mt-1 text-sm font-semibold text-[#E89000]">
                            Daily Overview
                        </p>
                    </div>

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white text-[#E89000] shadow-sm transition-transform duration-300 group-hover:translate-x-1 cursor-pointer">
                        <i class="fa-solid fa-arrow-right"></i>
                    </div>

                </div>

            </div>

            <div class="absolute -right-10 -top-12 h-40 w-40 rounded-full bg-orange-100/40"></div>
        </div>

        <!-- Pending Requests -->
    <div class="group cursor-pointer rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-orange-300 hover:shadow-md">
        <div class="flex items-center justify-between">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-50 text-orange-500">
                <i class="fa-solid fa-clock text-lg"></i>
            </div>
            <span class="rounded-full bg-orange-50 px-2.5 py-1 text-xs font-medium text-orange-600">Pending</span>
        </div>
        <h3 class="mt-3 text-2xl font-bold text-gray-800">5</h3>
        <p class="mt-0.5 text-sm font-semibold text-gray-700">Pending Requests</p>
        <p class="mt-1 text-xs text-gray-500">Requests waiting for approval</p>
    </div>

    <!-- Accepted -->
    <div class="group cursor-pointer rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-green-300 hover:shadow-md">
        <div class="flex items-center justify-between">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-50 text-green-500">
                <i class="fa-solid fa-check-circle text-lg"></i>
            </div>
            <span class="rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-600">Accepted</span>
        </div>
        <h3 class="mt-3 text-2xl font-bold text-gray-800">8</h3>
        <p class="mt-0.5 text-sm font-semibold text-gray-700">Accepted</p>
        <p class="mt-1 text-xs text-gray-500">Approved service requests</p>
    </div>

    <!-- Rejected -->
    <div class="group cursor-pointer rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-red-300 hover:shadow-md">
        <div class="flex items-center justify-between">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-50 text-red-500">
                <i class="fa-solid fa-circle-xmark text-lg"></i>
            </div>
            <span class="rounded-full bg-red-50 px-2.5 py-1 text-xs font-medium text-red-600">Rejected</span>
        </div>
        <h3 class="mt-3 text-2xl font-bold text-gray-800">2</h3>
        <p class="mt-0.5 text-sm font-semibold text-gray-700">Rejected</p>
        <p class="mt-1 text-xs text-gray-500">Declined service requests</p>
    </div>

    <!-- Assigned Jobs -->
    <div class="group cursor-pointer rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-blue-300 hover:shadow-md">
        <div class="flex items-center justify-between">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-500">
                <i class="fa-solid fa-clipboard-list text-lg"></i>
            </div>
            <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-600">Assigned</span>
        </div>
        <h3 class="mt-3 text-2xl font-bold text-gray-800">10</h3>
        <p class="mt-0.5 text-sm font-semibold text-gray-700">Assigned Jobs</p>
        <p class="mt-1 text-xs text-gray-500">Jobs assigned to mechanics</p>
    </div>

    <!-- In Progress -->
    <div class="group cursor-pointer rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-purple-300 hover:shadow-md">
        <div class="flex items-center justify-between">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-50 text-purple-500">
                <i class="fa-solid fa-gears text-lg"></i>
            </div>
            <span class="rounded-full bg-purple-50 px-2.5 py-1 text-xs font-medium text-purple-600">Ongoing</span>
        </div>
        <h3 class="mt-3 text-2xl font-bold text-gray-800">6</h3>
        <p class="mt-0.5 text-sm font-semibold text-gray-700">In Progress</p>
        <p class="mt-1 text-xs text-gray-500">Currently active service jobs</p>
    </div>

    <!-- Closed Jobs -->
    <div class="group cursor-pointer rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-gray-400 hover:shadow-md">
        <div class="flex items-center justify-between">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 text-gray-600">
                <i class="fa-solid fa-circle-check text-lg"></i>
            </div>
            <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">Completed</span>
        </div>
        <h3 class="mt-3 text-2xl font-bold text-gray-800">18</h3>
        <p class="mt-0.5 text-sm font-semibold text-gray-700">Closed Jobs</p>
        <p class="mt-1 text-xs text-gray-500">Successfully completed jobs</p>
    </div>

    <!-- Pending Invoices -->
    <div class="group cursor-pointer rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-yellow-300 hover:shadow-md">
        <div class="flex items-center justify-between">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-yellow-50 text-yellow-600">
                <i class="fa-solid fa-file-invoice-dollar text-lg"></i>
            </div>
            <span class="rounded-full bg-yellow-50 px-2.5 py-1 text-xs font-medium text-yellow-600">Awaiting</span>
        </div>
        <h3 class="mt-3 text-2xl font-bold text-gray-800">4</h3>
        <p class="mt-0.5 text-sm font-semibold text-gray-700">Pending Invoices</p>
        <p class="mt-1 text-xs text-gray-500">Invoices awaiting payment</p>
    </div>

    <!-- Payments Today -->
    <div class="group cursor-pointer rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-teal-300 hover:shadow-md">
        <div class="flex items-center justify-between">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-teal-50 text-teal-600">
                <i class="fa-solid fa-money-bill-wave text-lg"></i>
            </div>
            <span class="rounded-full bg-teal-50 px-2.5 py-1 text-xs font-medium text-teal-600">Received</span>
        </div>
        <h3 class="mt-3 text-2xl font-bold text-gray-800">8</h3>
        <p class="mt-0.5 text-sm font-semibold text-gray-700">Payments Today</p>
        <p class="mt-1 text-xs text-gray-500">Payments received today</p>
    </div>

    </div>

</section>

<!-- Quick Actions -->
<section class="mt-6">

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

        <!-- Header -->
        <div class="border-b border-gray-100 px-5 py-4">

            <div class="flex items-center gap-3">

                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-100">
                    <i class="fa-solid fa-bolt text-lg text-[#E89000]"></i>
                </div>

                <div>
                    <h2 class="text-base font-bold text-gray-900">
                        Quick Actions
                    </h2>

                    <p class="mt-0.5 text-xs text-gray-500">
                        Frequently used receptionist actions
                    </p>
                </div>

            </div>

        </div>

        <!-- Action Cards -->
        <div class="grid grid-cols-1 gap-4 p-5 md:grid-cols-2 xl:grid-cols-3">

            <!-- Register Client -->
            <a href="../register.php"
               class="group flex flex-col items-center rounded-xl border border-gray-200 bg-white p-6 text-center transition-all duration-300 hover:-translate-y-1 hover:border-[#E89000] hover:shadow-lg focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#E89000]">

                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-orange-50 transition-all duration-300 group-hover:scale-110 group-hover:bg-orange-100">
                    <i class="fa-solid fa-user-plus text-3xl text-[#E89000]"></i>
                </div>

                <h3 class="text-lg font-semibold text-gray-900 transition-colors group-hover:text-[#E89000]">
                    Register Client
                </h3>

                <p class="mt-2 max-w-xs text-sm leading-relaxed text-gray-500">
                    Create a new customer profile and store contact details.
                </p>

                <div class="mt-5 flex items-center gap-2 text-sm font-semibold text-[#E89000]">
                    <span>Get Started</span>
                    <i class="fa-solid fa-arrow-right text-xs transition-transform duration-300 group-hover:translate-x-1"></i>
                </div>

            </a>

            <!-- Create Job Card -->
            <button
    type="button"
    id="openJobCardModal"
    class="group flex cursor-pointer flex-col items-center rounded-xl border border-gray-200 bg-white p-6 text-center transition-all duration-300 hover:-translate-y-1 hover:border-[#E89000] hover:shadow-lg focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#E89000]">

    <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-orange-50 transition-all duration-300 group-hover:scale-110 group-hover:bg-orange-100">
        <i class="fa-solid fa-clipboard-list text-3xl text-[#E89000]"></i>
    </div>

    <h3 class="text-lg font-semibold text-gray-900 transition-colors group-hover:text-[#E89000]">
        Create Job Card
    </h3>

    <p class="mt-2 max-w-xs text-sm leading-relaxed text-gray-500">
        Create a new service request and assign vehicle details.
    </p>

    <div class="mt-5 flex items-center gap-2 text-sm font-semibold text-[#E89000]">
        <span>Get Started</span>
        <i class="fa-solid fa-arrow-right text-xs transition-transform duration-300 group-hover:translate-x-1"></i>
    </div>
</button>

            <!-- Create Invoice -->
            <a href="create-invoice.php"
               class="group flex flex-col items-center rounded-xl border border-gray-200 bg-white p-6 text-center transition-all duration-300 hover:-translate-y-1 hover:border-[#E89000] hover:shadow-lg focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#E89000]">

                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-orange-50 transition-all duration-300 group-hover:scale-110 group-hover:bg-orange-100">
                    <i class="fa-solid fa-file-invoice-dollar text-3xl text-[#E89000]"></i>
                </div>

                <h3 class="text-lg font-semibold text-gray-900 transition-colors group-hover:text-[#E89000]">
                    Create Invoice
                </h3>

                <p class="mt-2 max-w-xs text-sm leading-relaxed text-gray-500">
                    Generate a customer invoice for completed vehicle services.
                </p>

                <div class="mt-5 flex items-center gap-2 text-sm font-semibold text-[#E89000]">
                    <span>Get Started</span>
                    <i class="fa-solid fa-arrow-right text-xs transition-transform duration-300 group-hover:translate-x-1"></i>
                </div>

            </a>

        </div>

    </div>

</section>

        </div>
    </main>
</div>

<!-- CREATE JOB CARD MODAL -->
<div
    id="jobCardModal"
    class="fixed inset-0 z-[100] hidden items-center justify-center bg-gray-950/60 p-3 backdrop-blur-sm sm:p-6"
    role="dialog"
    aria-modal="true"
    aria-labelledby="jobCardModalTitle">

    <div class="relative flex max-h-[95vh] w-full max-w-3xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">

        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4 sm:px-6">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-100 text-[#E89000]">
                    <i class="fa-solid fa-clipboard-list text-lg"></i>
                </div>

                <div>
                    <h2 id="jobCardModalTitle" class="text-base font-bold text-gray-900 sm:text-lg">
                        Create New Job Card
                    </h2>
                    <p class="mt-0.5 text-[11px] text-gray-500">
                        Enter customer, vehicle and service information
                    </p>
                </div>
            </div>

            <!-- Close Button -->
            <button
                type="button"
                data-close-job-modal
                aria-label="Close modal"
                class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-lg text-gray-400 transition hover:bg-red-50 hover:text-red-500">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Modal Form -->
        <form
            id="jobCardForm"
            action="create-job-card.php"
            method="POST"
            class="flex min-h-0 flex-1 flex-col">

            <!-- Scrollable Form Content -->
            <div class="space-y-5 overflow-y-auto p-5 sm:p-6">

                <!-- Job Card Information -->
                <section>
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="text-xs font-bold uppercase tracking-wide text-gray-800">
                            <i class="fa-solid fa-file-lines mr-2 text-[#E89000]"></i>
                            Job Card Information
                        </h3>

                        <span class="rounded-full bg-orange-50 px-3 py-1 text-[10px] font-semibold text-[#E89000]">
                            New Job
                        </span>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                        <div>
                            <label for="job-card-number" class="mb-1.5 block text-xs font-semibold text-gray-700">
                                Job Card Number
                            </label>
                            <input
                                id="job-card-number"
                                name="job_card_number"
                                type="text"
                                readonly
                                class="h-10 w-full rounded-lg border border-gray-200 bg-gray-100 px-3 text-xs font-semibold text-gray-600 outline-none">
                        </div>

                        <div>
                            <label for="job-date" class="mb-1.5 block text-xs font-semibold text-gray-700">
                                Job Date <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="job-date"
                                name="job_date"
                                type="date"
                                required
                                class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                        </div>

                    </div>
                </section>

                <div class="border-t border-gray-100"></div>

                <!-- Customer Information -->
                <section>
                    <h3 class="mb-3 text-xs font-bold uppercase tracking-wide text-gray-800">
                        <i class="fa-solid fa-user mr-2 text-[#E89000]"></i>
                        Customer Information
                    </h3>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                        <div>
                            <label for="customer-name" class="mb-1.5 block text-xs font-semibold text-gray-700">
                                Customer Name <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="customer-name"
                                name="customer_name"
                                type="text"
                                placeholder="Enter customer name"
                                required
                                class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                        </div>

                        <div>
                            <label for="customer-phone" class="mb-1.5 block text-xs font-semibold text-gray-700">
                                Contact Number <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="customer-phone"
                                name="customer_phone"
                                type="tel"
                                placeholder="e.g. 0771234567"
                                required
                                class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                        </div>

                        <div class="sm:col-span-2">
                            <label for="customer-email" class="mb-1.5 block text-xs font-semibold text-gray-700">
                                Email Address
                            </label>
                            <input
                                id="customer-email"
                                name="customer_email"
                                type="email"
                                placeholder="Enter customer email address"
                                class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                        </div>

                    </div>
                </section>

                <div class="border-t border-gray-100"></div>

                <!-- Vehicle Information -->
                <section>
                    <h3 class="mb-3 text-xs font-bold uppercase tracking-wide text-gray-800">
                        <i class="fa-solid fa-car mr-2 text-[#E89000]"></i>
                        Vehicle Information
                    </h3>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                        <div>
                            <label for="vehicle-number" class="mb-1.5 block text-xs font-semibold text-gray-700">
                                Vehicle Registration Number <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="vehicle-number"
                                name="vehicle_number"
                                type="text"
                                placeholder="e.g. ABC-1234"
                                required
                                class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs uppercase text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                        </div>

                        <div>
                            <label for="vehicle-type" class="mb-1.5 block text-xs font-semibold text-gray-700">
                                Vehicle Type <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="vehicle-type"
                                name="vehicle_type"
                                required
                                class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                                <option value="">Select vehicle type</option>
                                <option value="Car">Car</option>
                                <option value="SUV">SUV</option>
                                <option value="Van">Van</option>
                                <option value="Motorcycle">Motorcycle</option>
                                <option value="Three Wheeler">Three Wheeler</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div>
                            <label for="vehicle-make" class="mb-1.5 block text-xs font-semibold text-gray-700">
                                Vehicle Make <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="vehicle-make"
                                name="vehicle_make"
                                type="text"
                                placeholder="e.g. Toyota"
                                required
                                class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                        </div>

                        <div>
                            <label for="vehicle-model" class="mb-1.5 block text-xs font-semibold text-gray-700">
                                Vehicle Model <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="vehicle-model"
                                name="vehicle_model"
                                type="text"
                                placeholder="e.g. Axio"
                                required
                                class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                        </div>

                    </div>
                </section>

                <div class="border-t border-gray-100"></div>

                <!-- Service Details -->
                <section>
                    <h3 class="mb-3 text-xs font-bold uppercase tracking-wide text-gray-800">
                        <i class="fa-solid fa-screwdriver-wrench mr-2 text-[#E89000]"></i>
                        Service Details
                    </h3>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                        <div>
                            <label for="job-service" class="mb-1.5 block text-xs font-semibold text-gray-700">
                                Select Service <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="job-service"
                                name="service"
                                required
                                class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                                <option value="">Choose a service</option>
                                <option value="Engine Oil Change">Engine Oil Change</option>
                                <option value="Battery Inspection">Battery Inspection</option>
                                <option value="Full Vehicle Service">Full Vehicle Service</option>
                                <option value="Engine Diagnostics">Engine Diagnostics</option>
                                <option value="AC Service">AC Service</option>
                                <option value="Brake Inspection">Brake Inspection</option>
                                <option value="Full Vehicle Wash">Full Vehicle Wash</option>
                                <option value="Body & Paint Repair">Body & Paint Repair</option>
                                <option value="Wheel Alignment">Wheel Alignment</option>
                                <option value="Vehicle Safety Inspection">Vehicle Safety Inspection</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div>
                            <label for="job-priority" class="mb-1.5 block text-xs font-semibold text-gray-700">
                                Job Priority
                            </label>
                            <select
                                id="job-priority"
                                name="priority"
                                class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                                <option value="Normal">Normal</option>
                                <option value="High">High</option>
                                <option value="Urgent">Urgent</option>
                            </select>
                        </div>

                        <div>
                            <label for="appointment-time" class="mb-1.5 block text-xs font-semibold text-gray-700">
                                Appointment Time <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="appointment-time"
                                name="appointment_time"
                                type="time"
                                required
                                class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                        </div>

                        <div>
                            <label for="assigned-mechanic" class="mb-1.5 block text-xs font-semibold text-gray-700">
                                Assign Mechanic
                            </label>
                            <select
                                id="assigned-mechanic"
                                name="mechanic"
                                class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                                <option value="">Assign later</option>
                                <option value="Mechanic 1">Mechanic 1</option>
                                <option value="Mechanic 2">Mechanic 2</option>
                                <option value="Mechanic 3">Mechanic 3</option>
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="job-description" class="mb-1.5 block text-xs font-semibold text-gray-700">
                                Problem Description / Customer Request
                            </label>
                            <textarea
                                id="job-description"
                                name="description"
                                rows="3"
                                placeholder="Describe the vehicle issue or requested service..."
                                class="w-full resize-none rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100"></textarea>
                        </div>

                    </div>
                </section>

                <!-- Initial Status -->
                <div class="flex items-start gap-3 rounded-lg border border-orange-100 bg-orange-50/70 p-3">
                    <i class="fa-solid fa-circle-info mt-0.5 text-sm text-[#E89000]"></i>
                    <div>
                        <p class="text-xs font-semibold text-gray-800">Initial Job Status: Pending</p>
                        <p class="mt-1 text-[10px] leading-relaxed text-gray-500">
                            The job card will be created with a Pending status. It can be assigned and updated later.
                        </p>
                    </div>
                </div>

                <input type="hidden" name="status" value="Pending">

            </div>

            <!-- Modal Footer -->
            <div class="flex flex-col-reverse gap-2 border-t border-gray-100 bg-gray-50 px-5 py-4 sm:flex-row sm:justify-end sm:px-6">

                <button
                    type="button"
                    data-close-job-modal
                    class="inline-flex h-10 cursor-pointer items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-5 text-xs font-semibold text-gray-600 transition hover:bg-gray-100">
                    <i class="fa-solid fa-xmark"></i>
                    Cancel
                </button>

                <button
                    type="submit"
                    class="inline-flex h-10 cursor-pointer items-center justify-center gap-2 rounded-lg bg-[#E89000] px-5 text-xs font-semibold text-white shadow-sm transition hover:bg-orange-600">
                    <i class="fa-solid fa-plus"></i>
                    Create Job Card
                </button>

            </div>
        </form>
    </div>
</div>
</body>
<script>
    const jobCardModal = document.getElementById("jobCardModal");
    const openJobCardModal = document.getElementById("openJobCardModal");
    const jobCardForm = document.getElementById("jobCardForm");
    const jobCardNumber = document.getElementById("job-card-number");
    const jobDate = document.getElementById("job-date");

    // Generate a job card number for display and form submission
    function generateJobCardNumber() {
        const date = new Date();
        const datePart = date.getFullYear().toString().slice(-2)
            + String(date.getMonth() + 1).padStart(2, "0")
            + String(date.getDate()).padStart(2, "0");

        const randomPart = Math.floor(1000 + Math.random() * 9000);

        return `JC-${datePart}-${randomPart}`;
    }

    // Open modal
    function openModal() {
        jobCardModal.classList.remove("hidden");
        jobCardModal.classList.add("flex");
        document.body.classList.add("overflow-hidden");

        // Set today's date as the default job date
        const today = new Date();
        const localDate = new Date(
            today.getTime() - today.getTimezoneOffset() * 60000
        ).toISOString().split("T")[0];

        jobDate.value = localDate;
        jobDate.min = localDate;

        // Generate a new job card number
        jobCardNumber.value = generateJobCardNumber();

        // Focus the first input
        document.getElementById("customer-name").focus();
    }

    // Close modal
    function closeModal() {
        jobCardModal.classList.add("hidden");
        jobCardModal.classList.remove("flex");
        document.body.classList.remove("overflow-hidden");
    }

    // Open button
    openJobCardModal.addEventListener("click", openModal);

    // Close buttons
    document.querySelectorAll("[data-close-job-modal]").forEach(button => {
        button.addEventListener("click", closeModal);
    });

    // Close when clicking outside the modal content
    jobCardModal.addEventListener("click", function (event) {
        if (event.target === jobCardModal) {
            closeModal();
        }
    });

    // Close with Escape key
    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape" && !jobCardModal.classList.contains("hidden")) {
            closeModal();
        }
    });

    // Reset form when modal is closed
    document.querySelectorAll("[data-close-job-modal]").forEach(button => {
        button.addEventListener("click", function () {
            jobCardForm.reset();
        });
    });

    // Validate form before submission
    jobCardForm.addEventListener("submit", function (event) {
        if (!jobCardForm.checkValidity()) {
            event.preventDefault();
            jobCardForm.reportValidity();
            return;
        }

        // Keep the generated job card number in the submitted form.
        if (!jobCardNumber.value) {
            jobCardNumber.value = generateJobCardNumber();
        }
    });
</script>
</html>