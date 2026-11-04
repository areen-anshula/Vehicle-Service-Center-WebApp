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
            <a href="../book_appointment.php"
               class="group flex flex-col items-center rounded-xl border border-gray-200 bg-white p-6 text-center transition-all duration-300 hover:-translate-y-1 hover:border-[#E89000] hover:shadow-lg focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#E89000]">

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

            </a>

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
</body>

</html>