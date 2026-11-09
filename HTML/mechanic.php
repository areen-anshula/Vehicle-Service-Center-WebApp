<?php
require_once __DIR__ . '/includes/auth.php';
require_role('mechanic');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoPulse | Mechanic Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4.3.3"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Dashboard Header -->
    <header class="sticky top-0 z-50 bg-gray-950 text-white shadow-lg">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <!-- Left Side -->
                <div class="flex items-center gap-4 lg:gap-6">
                    <!-- Logo -->
                    <a href="homepage.html" class="flex items-center gap-2">
                        <img src="../MEDIA/AutoPulse Automotive Service Logo.png" alt="AutoPulse Logo" class="h-15 w-15 rounded-lg object-contain">
                        <h1 class="text-xl font-bold tracking-tight">
                            Auto<span class="text-[#E89000]">Pulse</span>
                        </h1>
                    </a>

                    <!-- Divider -->
                    <div class="hidden h-8 w-px bg-gray-700 lg:block"></div>

                    <!-- Dashboard Title -->
                    <h2 class="hidden text-lg font-semibold text-gray-100 lg:block">
                        Mechanic Dashboard
                    </h2>
                </div>

                <!-- Right Side -->
                <div class="flex items-center gap-3 sm:gap-5">
                    <!-- Notification -->
                    <button aria-label="Notifications" class="relative flex h-10 w-10 items-center justify-center rounded-full bg-gray-800 transition hover:bg-gray-700">
                        <i class="fa-regular fa-bell text-lg text-white"></i>
                        <span class="absolute -right-0.5 -top-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">
                            2
                        </span>
                    </button>

                    <!-- Divider -->
                    <div class="hidden h-8 w-px bg-gray-700 sm:block"></div>

                    <!-- Profile -->
                    <div class="flex items-center gap-2 sm:gap-3">
                        <div class="hidden sm:block">
                            <h4 class="text-sm font-semibold text-white"><?= e($_SESSION['full_name']) ?></h4>
                            <p class="text-xs text-gray-400">Mechanic</p>
                        </div>
                        <button aria-label="Profile options" class="text-xs text-gray-400 transition hover:text-white">
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                    </div>

                    <!-- Divider -->
                    <div class="hidden h-8 w-px bg-gray-700 md:block"></div>

                    <!-- Logout -->
                    <a href="logout.php" class="flex items-center gap-2 rounded-lg bg-[#FFA500] px-3 py-2 text-xs font-semibold text-white transition hover:bg-[#E89000] sm:px-4 sm:text-sm">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span class="hidden sm:block">Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    
<!-- Dashboard Statistics -->
<section class="px-4 py-6 sm:px-6 lg:px-8">
    <div class="mb-5">
        <h2 class="text-lg font-bold text-gray-900">Dashboard Overview</h2>
        <p class="mt-1 text-xs text-gray-500">Track your assigned jobs and work progress.</p>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">

        <!-- Assigned Jobs -->
        <div class="group rounded-xl border border-gray-100 border-l-4 border-l-[#E89000] bg-white p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-md">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-50 transition group-hover:bg-orange-100">
                        <i class="fa-solid fa-clipboard-list text-xl text-[#E89000]"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500">Assigned Jobs</p>
                        <h3 class="mt-1 text-2xl font-bold text-gray-900">5</h3>
                    </div>
                </div>
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-50 transition group-hover:bg-orange-50">
                    <i class="fa-solid fa-arrow-right text-xs text-gray-400 group-hover:text-[#E89000]"></i>
                </div>
            </div>
            <div class="mt-4 border-t border-gray-100 pt-3">
                <p class="text-[11px] text-gray-500">
                    <span class="font-semibold text-[#E89000]">5 jobs</span> waiting for your attention
                </p>
            </div>
        </div>

        <!-- In Progress -->
        <div class="group rounded-xl border border-gray-100 border-l-4 border-l-blue-500 bg-white p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-md">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 transition group-hover:bg-blue-100">
                        <i class="fa-solid fa-car text-xl text-blue-500"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500">In Progress</p>
                        <h3 class="mt-1 text-2xl font-bold text-gray-900">3</h3>
                    </div>
                </div>
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-50 transition group-hover:bg-blue-50">
                    <i class="fa-solid fa-arrow-right text-xs text-gray-400 group-hover:text-blue-500"></i>
                </div>
            </div>
            <div class="mt-4 border-t border-gray-100 pt-3">
                <p class="text-[11px] text-gray-500">
                    <span class="font-semibold text-blue-500">3 jobs</span> currently being serviced
                </p>
            </div>
        </div>

        <!-- Completed Jobs -->
        <div class="group rounded-xl border border-gray-100 border-l-4 border-l-green-500 bg-white p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-md">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-50 transition group-hover:bg-green-100">
                        <i class="fa-solid fa-circle-check text-xl text-green-500"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500">Completed Jobs</p>
                        <h3 class="mt-1 text-2xl font-bold text-gray-900">8</h3>
                    </div>
                </div>
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-50 transition group-hover:bg-green-50">
                    <i class="fa-solid fa-arrow-right text-xs text-gray-400 group-hover:text-green-500"></i>
                </div>
            </div>
            <div class="mt-4 border-t border-gray-100 pt-3">
                <p class="text-[11px] text-gray-500">
                    <span class="font-semibold text-green-600">8 jobs</span> successfully completed
                </p>
            </div>
        </div>

    </div>
</section>


<!-- Assigned Jobs Section -->
<section class="px-4 py-6 sm:px-6 lg:px-8">
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

        <!-- Section Header -->
        <div class="flex flex-col gap-4 border-b border-gray-100 p-5 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-orange-50">
                    <i class="fa-solid fa-briefcase text-lg text-[#E89000]"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">My Assigned Jobs</h2>
                    <p class="mt-0.5 text-xs text-gray-500">Manage and track your assigned service requests</p>
                </div>
            </div>

            <!-- Search and Filter -->
            <div class="flex flex-col gap-2 sm:flex-row">
                <!-- Search Input -->
                <div class="relative flex-1">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                    <input
                        type="text"
                        placeholder="Search Job Card ID..."
                        class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 pl-9 pr-3 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100 sm:w-52">
                </div>

                <!-- Status Filter -->
                <select class="h-10 rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-600 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                    <option value="">All Statuses</option>
                    <option value="assigned">Assigned</option>
                    <option value="in-progress">In Progress</option>
                    <option value="completed">Completed</option>
                </select>

                <!-- Search Button -->
                <button type="button" class="flex h-10 items-center justify-center gap-2 rounded-lg bg-[#FFA500] cursor-pointer px-4 text-xs font-semibold text-white shadow-sm transition duration-200 hover:bg-[#E89000] hover:shadow-md active:scale-95">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    Search
                </button>
            </div>
        </div>

        <!-- Jobs Table -->
        <div class="overflow-x-auto">
            <table class="w-full min-w-[850px] text-left">
                <thead>
                    <tr class="bg-gray-50 text-[11px] font-semibold uppercase tracking-wide text-gray-500">
                        <th class="px-5 py-4">Job Card ID</th>
                        <th class="px-5 py-4">Customer</th>
                        <th class="px-5 py-4">Vehicle No</th>
                        <th class="px-5 py-4">Vehicle Model</th>
                        <th class="px-5 py-4">Assigned Date</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4 text-center">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 text-xs text-gray-600">

                    <!-- Job 1 -->
                    <tr class="transition duration-200 hover:bg-orange-50/40">
                        <td class="px-5 py-4 font-semibold text-gray-900">JC-0045</td>
                        <td class="px-5 py-4 font-medium text-gray-700">Nimal Shanaka</td>
                        <td class="px-5 py-4">WP-KK-1234</td>
                        <td class="px-5 py-4">Toyota Corolla</td>
                        <td class="px-5 py-4">24 Apr 2025</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-3 py-1.5 text-[11px] font-semibold text-blue-600">
                                <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                Assigned
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <a href="job-details.html" class="inline-flex items-center gap-1.5 rounded-md bg-[#E89000] px-3 py-2 text-[11px] font-semibold text-white transition hover:bg-[#E89000]">
                                <i class="fa-solid fa-eye"></i>
                                View
                            </a>
                        </td>
                    </tr>

                    <!-- Job 2 -->
                    <tr class="transition duration-200 hover:bg-orange-50/40">
                        <td class="px-5 py-4 font-semibold text-gray-900">JC-0046</td>
                        <td class="px-5 py-4 font-medium text-gray-700">Sithara Fernando</td>
                        <td class="px-5 py-4">WP-AB-5678</td>
                        <td class="px-5 py-4">Honda Civic</td>
                        <td class="px-5 py-4">24 Apr 2025</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-orange-50 px-3 py-1.5 text-[11px] font-semibold text-orange-600">
                                <span class="h-1.5 w-1.5 rounded-full bg-orange-500"></span>
                                In Progress
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <a href="job-details.html" class="inline-flex items-center gap-1.5 rounded-md bg-[#E89000] px-3 py-2 text-[11px] font-semibold text-white transition hover:bg-[#E89000]">
                                <i class="fa-solid fa-eye"></i>
                                View
                            </a>
                        </td>
                    </tr>

                    <!-- Job 3 -->
                    <tr class="transition duration-200 hover:bg-orange-50/40">
                        <td class="px-5 py-4 font-semibold text-gray-900">JC-0047</td>
                        <td class="px-5 py-4 font-medium text-gray-700">Kasun Perera</td>
                        <td class="px-5 py-4">WP-BB-9012</td>
                        <td class="px-5 py-4">Suzuki Alto</td>
                        <td class="px-5 py-4">23 Apr 2025</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-3 py-1.5 text-[11px] font-semibold text-green-600">
                                <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                Completed
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <a href="job-details.html" class="inline-flex items-center gap-1.5 rounded-md bg-[#E89000] px-3 py-2 text-[11px] font-semibold text-white transition hover:bg-[#E89000]">
                                <i class="fa-solid fa-eye"></i>
                                View
                            </a>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

        <!-- Table Footer -->
        <div class="flex flex-col gap-3 border-t border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs text-gray-500">
                Showing <span class="font-semibold text-
                gray-800">1–3</span> of <span class="font-semibold text-gray-800">3</span> jobs
            </p>

            <!-- Pagination -->
            <div class="flex items-center gap-1.5">
                <button disabled class="flex h-8 items-center gap-1 rounded-md border border-gray-200 px-3 text-[11px] font-medium text-gray-400 disabled:cursor-not-allowed">
                    <i class="fa-solid fa-chevron-left text-[9px]"></i>
                    Previous
                </button>

                <button class="flex h-8 w-8 items-center justify-center rounded-md bg-[#E89000] text-xs font-semibold text-white shadow-sm">
                    1
                </button>

                <button disabled class="flex h-8 items-center gap-1 rounded-md border border-gray-200 px-3 text-[11px] font-medium text-gray-400 disabled:cursor-not-allowed">
                    Next
                    <i class="fa-solid fa-chevron-right text-[9px]"></i>
                </button>
            </div>
        </div>

    </div>
</section>


</body>
</html>
