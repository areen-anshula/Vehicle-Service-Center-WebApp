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
                    <a href="dashboard.php" class="group flex items-center justify-between rounded-lg px-4 py-3 text-xs font-medium text-gray-300 transition duration-200 hover:bg-white/10 hover:text-white">
                        <span class="flex items-center gap-3">
                            <i class="fa-solid fa-users w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>
                            Dashboard
                        </span>
                        <i class="fa-solid fa-chevron-right text-[9px] text-gray-500"></i>
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
                    <a href="services.php" class="flex items-center justify-between rounded-lg bg-[#E89000] px-4 py-3 text-xs font-semibold text-white shadow-sm">
                        <span class="flex items-center gap-3">
                            <i class="fa-solid fa-screwdriver-wrench w-4 text-center"></i>
                            Inventory
                        </span>
                        <i class="fa-solid fa-chevron-right text-[9px]"></i>
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

<!-- PAGE CONTENT: SPARE PARTS MANAGEMENT -->
<div class="space-y-6 p-4 sm:p-6 lg:p-8">

    <!-- Page Heading -->
    <section class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-100 text-[#E89000]">
                <i class="fa-solid fa-boxes-stacked text-lg"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold tracking-tight text-gray-900">Spare Parts Management</h1>
                <p class="mt-0.5 text-xs text-gray-500">View and manage spare parts inventory and stock levels</p>
            </div>
        </div>

        <div class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-[11px] text-gray-500 shadow-sm">
            <i class="fa-solid fa-house text-[#E89000]"></i>
            <span>Dashboard</span>
            <i class="fa-solid fa-chevron-right text-[9px]"></i>
            <span class="font-semibold text-gray-800">Spare Parts</span>
        </div>
    </section>

    <!-- Inventory Statistics -->
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

        <!-- Total Parts -->
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
            <div class="flex items-center justify-between">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-50 text-[#E89000]">
                    <i class="fa-solid fa-boxes-stacked text-lg"></i>
                </div>
                <span class="rounded-full bg-orange-50 px-2.5 py-1 text-[10px] font-semibold text-[#E89000]">Inventory</span>
            </div>
            <h3 class="mt-3 text-2xl font-bold text-gray-900">156</h3>
            <p class="mt-0.5 text-xs font-semibold text-gray-700">Total Spare Parts</p>
            <p class="mt-1 text-[10px] text-gray-400">All spare parts registered in the system</p>
        </div>

        <!-- In Stock -->
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
            <div class="flex items-center justify-between">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-50 text-green-600">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                </div>
                <span class="rounded-full bg-green-50 px-2.5 py-1 text-[10px] font-semibold text-green-600">Available</span>
            </div>
            <h3 class="mt-3 text-2xl font-bold text-gray-900">128</h3>
            <p class="mt-0.5 text-xs font-semibold text-gray-700">In Stock</p>
            <p class="mt-1 text-[10px] text-gray-400">Parts with sufficient stock quantity</p>
        </div>

        <!-- Low Stock -->
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
            <div class="flex items-center justify-between">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-yellow-50 text-yellow-600">
                    <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                </div>
                <span class="rounded-full bg-yellow-50 px-2.5 py-1 text-[10px] font-semibold text-yellow-600">Attention</span>
            </div>
            <h3 class="mt-3 text-2xl font-bold text-gray-900">18</h3>
            <p class="mt-0.5 text-xs font-semibold text-gray-700">Low Stock</p>
            <p class="mt-1 text-[10px] text-gray-400">Parts that need stock replenishment</p>
        </div>

        <!-- Out of Stock -->
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
            <div class="flex items-center justify-between">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-50 text-red-500">
                    <i class="fa-solid fa-ban text-lg"></i>
                </div>
                <span class="rounded-full bg-red-50 px-2.5 py-1 text-[10px] font-semibold text-red-500">Unavailable</span>
            </div>
            <h3 class="mt-3 text-2xl font-bold text-gray-900">10</h3>
            <p class="mt-0.5 text-xs font-semibold text-gray-700">Out of Stock</p>
            <p class="mt-1 text-[10px] text-gray-400">Parts currently unavailable</p>
        </div>

    </section>

    <!-- Stock Alert -->
    <section class="flex flex-col gap-3 rounded-xl border border-yellow-200 bg-yellow-50 p-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-start gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-yellow-100 text-yellow-600">
                <i class="fa-solid fa-triangle-exclamation text-lg"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-yellow-800">Inventory Stock Alert</h3>
                <p class="mt-1 text-xs leading-relaxed text-yellow-700">
                    You have <span class="font-bold">18 low-stock items</span> and
                    <span class="font-bold">10 out-of-stock items</span>.
                    Consider replenishing your inventory.
                </p>
            </div>
        </div>

        <a href="#spare-parts-list" class="inline-flex h-9 shrink-0 items-center justify-center gap-2 rounded-lg border border-yellow-300 bg-white px-4 text-xs font-semibold text-yellow-700 transition hover:bg-yellow-100">
            <i class="fa-solid fa-eye text-[10px]"></i>
            View Inventory
        </a>
    </section>

    <!-- Spare Parts List -->
    <section id="spare-parts-list" class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

        <!-- Section Header -->
        <div class="flex flex-col gap-3 border-b border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-100">
                    <i class="fa-solid fa-box-open text-lg text-[#E89000]"></i>
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-base font-bold text-gray-900">Spare Parts Inventory</h2>
                        <span class="rounded-full bg-orange-100 px-2 py-0.5 text-[10px] font-semibold text-[#E89000]">156 Parts</span>
                    </div>
                    <p class="mt-0.5 text-xs text-gray-500">Browse spare part details, prices and stock availability</p>
                </div>
            </div>

            <a href="#add-spare-part" class="inline-flex h-9 cursor-pointer items-center justify-center gap-2 rounded-lg bg-[#E89000] px-4 text-xs font-semibold text-white shadow-sm transition hover:bg-orange-600">
                <i class="fa-solid fa-plus text-[10px]"></i>
                Add New Spare Part
            </a>
        </div>

        <!-- Search and Filters -->
        <div class="flex flex-col gap-3 px-5 py-4 lg:flex-row lg:items-center">

            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                <input
                    type="search"
                    placeholder="Search by part name, code or supplier..."
                    class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 pl-9 pr-3 text-xs text-gray-700 outline-none transition placeholder:text-gray-400 focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
            </div>

            <div class="flex flex-col gap-2 sm:flex-row">
                <select class="h-10 rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-600 outline-none transition focus:border-[#E89000] focus:ring-2 focus:ring-orange-100">
                    <option>All Categories</option>
                    <option>Engine Parts</option>
                    <option>Brake System</option>
                    <option>Filters</option>
                    <option>Electrical Parts</option>
                    <option>Suspension</option>
                    <option>Lubricants</option>
                    <option>Other</option>
                </select>

                <select class="h-10 rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-600 outline-none transition focus:border-[#E89000] focus:ring-2 focus:ring-orange-100">
                    <option>All Stock Statuses</option>
                    <option>In Stock</option>
                    <option>Low Stock</option>
                    <option>Out of Stock</option>
                </select>

                <button type="button" class="inline-flex h-10 cursor-pointer items-center justify-center gap-2 rounded-lg bg-[#FFA500] px-4 text-xs font-semibold text-white transition hover:bg-[#E89000]">
                    <i class="fa-solid fa-filter text-[10px]"></i>
                    Filter
                </button>
            </div>
        </div>

        <!-- Spare Parts Cards -->
        <div class="grid grid-cols-1 gap-4 border-t border-gray-100 bg-gray-50/50 p-5 sm:grid-cols-2 xl:grid-cols-3">

            <!-- Part 1 -->
            <article class="group rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-orange-300 hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-50 text-[#E89000] transition group-hover:bg-[#E89000] group-hover:text-white">
                        <i class="fa-solid fa-filter text-xl"></i>
                    </div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-1 text-[10px] font-semibold text-green-600">
                        <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                        In Stock
                    </span>
                </div>
                <div class="mt-4">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-[#E89000]">Filters</p>
                    <h3 class="mt-1 text-sm font-bold text-gray-900">Engine Oil Filter</h3>
                    <p class="mt-1 text-[10px] font-medium text-gray-400">Part Code: FLT-001</p>
                    <p class="mt-2 min-h-10 text-[11px] leading-relaxed text-gray-500">High-quality oil filter for efficient engine oil filtration.</p>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-3 border-t border-gray-100 pt-3">
                    <div>
                        <p class="text-[10px] text-gray-400">Unit Price</p>
                        <p class="mt-0.5 text-lg font-bold text-gray-900">Rs. 2,500</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] text-gray-400">Available Quantity</p>
                        <p class="mt-0.5 text-lg font-bold text-green-600">45 <span class="text-[10px] font-medium text-gray-400">units</span></p>
                    </div>
                </div>
                <div class="mt-3 border-t border-gray-100 pt-3">
                    <p class="truncate text-[10px] text-gray-400"><i class="fa-solid fa-truck-fast mr-1"></i> Supplier: Auto Parts Lanka</p>
                </div>
                <div class="mt-4 flex gap-2">
                    <a href="edit-part.php?id=1" class="flex h-9 flex-1 items-center justify-center gap-2 rounded-lg border border-orange-200 text-[11px] font-semibold text-[#E89000] transition hover:bg-[#E89000] hover:text-white">
                        <i class="fa-solid fa-pen-to-square"></i> Edit Part
                    </a>
                    <form action="delete-part.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this spare part?');">
                        <input type="hidden" name="part_id" value="1">
                        <button type="submit" aria-label="Delete Engine Oil Filter" class="flex h-9 w-10 items-center justify-center rounded-lg border border-red-200 text-red-500 transition hover:bg-red-500 hover:text-white">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </article>

            <!-- Part 2 -->
            <article class="group rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-blue-300 hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600 transition group-hover:bg-blue-600 group-hover:text-white">
                        <i class="fa-solid fa-car-burst text-xl"></i>
                    </div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-yellow-50 px-2.5 py-1 text-[10px] font-semibold text-yellow-600">
                        <span class="h-1.5 w-1.5 rounded-full bg-yellow-500"></span>Low Stock
                    </span>
                </div>
                <div class="mt-4">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-blue-600">Brake System</p>
                    <h3 class="mt-1 text-sm font-bold text-gray-900">Brake Pads</h3>
                    <p class="mt-1 text-[10px] font-medium text-gray-400">Part Code: BRK-002</p>
                    <p class="mt-2 min-h-10 text-[11px] leading-relaxed text-gray-500">Durable brake pads designed for reliable braking performance.</p>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-3 border-t border-gray-100 pt-3">
                    <div>
                        <p class="text-[10px] text-gray-400">Unit Price</p>
                        <p class="mt-0.5 text-lg font-bold text-gray-900">Rs. 8,500</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] text-gray-400">Available Quantity</p>
                        <p class="mt-0.5 text-lg font-bold text-yellow-600">8 <span class="text-[10px] font-medium text-gray-400">units</span></p>
                    </div>
                </div>
                <div class="mt-3 border-t border-gray-100 pt-3">
                    <p class="truncate text-[10px] text-gray-400"><i class="fa-solid fa-truck-fast mr-1"></i> Supplier: City Auto Supplies</p>
                </div>
                <div class="mt-4 flex gap-2">
                    <a href="edit-part.php?id=2" class="flex h-9 flex-1 items-center justify-center gap-2 rounded-lg border border-orange-200 text-[11px] font-semibold text-[#E89000] transition hover:bg-[#E89000] hover:text-white">
                        <i class="fa-solid fa-pen-to-square"></i> Edit Part
                    </a>
                    <form action="delete-part.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this spare part?');">
                        <input type="hidden" name="part_id" value="2">
                        <button type="submit" aria-label="Delete Brake Pads" class="flex h-9 w-10 items-center justify-center rounded-lg border border-red-200 text-red-500 transition hover:bg-red-500 hover:text-white">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </article>

            <!-- Part 3 -->
            <article class="group rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-red-300 hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-50 text-red-500 transition group-hover:bg-red-500 group-hover:text-white">
                        <i class="fa-solid fa-bolt text-xl"></i>
                    </div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-2.5 py-1 text-[10px] font-semibold text-red-500">
                        <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>Out of Stock
                    </span>
                </div>
                <div class="mt-4">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-red-500">Engine Parts</p>
                    <h3 class="mt-1 text-sm font-bold text-gray-900">Spark Plug</h3>
                    <p class="mt-1 text-[10px] font-medium text-gray-400">Part Code: ENG-003</p>
                    <p class="mt-2 min-h-10 text-[11px] leading-relaxed text-gray-500">Ignition spark plug for efficient engine combustion.</p>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-3 border-t border-gray-100 pt-3">
                    <div>
                        <p class="text-[10px] text-gray-400">Unit Price</p>
                        <p class="mt-0.5 text-lg font-bold text-gray-900">Rs. 1,200</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] text-gray-400">Available Quantity</p>
                        <p class="mt-0.5 text-lg font-bold text-red-500">0 <span class="text-[10px] font-medium text-gray-400">units</span></p>
                    </div>
                </div>
                <div class="mt-3 border-t border-gray-100 pt-3">
                    <p class="truncate text-[10px] text-gray-400"><i class="fa-solid fa-truck-fast mr-1"></i> Supplier: Engine Pro Lanka</p>
                </div>
                <div class="mt-4 flex gap-2">
                    <a href="edit-part.php?id=3" class="flex h-9 flex-1 items-center justify-center gap-2 rounded-lg border border-orange-200 text-[11px] font-semibold text-[#E89000] transition hover:bg-[#E89000] hover:text-white">
                        <i class="fa-solid fa-pen-to-square"></i> Edit Part
                    </a>
                    <form action="delete-part.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this spare part?');">
                        <input type="hidden" name="part_id" value="3">
                        <button type="submit" aria-label="Delete Spark Plug" class="flex h-9 w-10 items-center justify-center rounded-lg border border-red-200 text-red-500 transition hover:bg-red-500 hover:text-white">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </article>

            <!-- Part 4 -->
            <article class="group rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-green-300 hover:shadow-md">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-50 text-green-600 transition group-hover:bg-green-600 group-hover:text-white">
                    <i class="fa-solid fa-filter-circle-dollar text-xl"></i>
                </div>
                <div class="mt-4 flex items-center justify-between">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-green-600">Filters</p>
                    <span class="rounded-full bg-green-50 px-2.5 py-1 text-[10px] font-semibold text-green-600">In Stock</span>
                </div>
                <h3 class="mt-1 text-sm font-bold text-gray-900">Air Filter</h3>
                <p class="mt-1 text-[10px] font-medium text-gray-400">Part Code: FLT-004</p>
                <p class="mt-2 min-h-10 text-[11px] leading-relaxed text-gray-500">Engine air filter for cleaner airflow and improved performance.</p>
                <div class="mt-4 grid grid-cols-2 gap-3 border-t border-gray-100 pt-3">
                    <div>
                        <p class="text-[10px] text-gray-400">Unit Price</p>
                        <p class="mt-0.5 text-lg font-bold text-gray-900">Rs. 3,200</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] text-gray-400">Available Quantity</p>
                        <p class="mt-0.5 text-lg font-bold text-green-600">32 <span class="text-[10px] font-medium text-gray-400">units</span></p>
                    </div>
                </div>
                <div class="mt-3 border-t border-gray-100 pt-3">
                    <p class="truncate text-[10px] text-gray-400"><i class="fa-solid fa-truck-fast mr-1"></i> Supplier: Auto Parts Lanka</p>
                </div>
                <div class="mt-4 flex gap-2">
                    <a href="edit-part.php?id=4" class="flex h-9 flex-1 items-center justify-center gap-2 rounded-lg border border-orange-200 text-[11px] font-semibold text-[#E89000] transition hover:bg-[#E89000] hover:text-white">
                        <i class="fa-solid fa-pen-to-square"></i> Edit Part
                    </a>
                    <form action="delete-part.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this spare part?');">
                        <input type="hidden" name="part_id" value="4">
                        <button type="submit" aria-label="Delete Air Filter" class="flex h-9 w-10 items-center justify-center rounded-lg border border-red-200 text-red-500 transition hover:bg-red-500 hover:text-white">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </article>

            <!-- Part 5 -->
            <article class="group rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-cyan-300 hover:shadow-md">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-cyan-50 text-cyan-600 transition group-hover:bg-cyan-600 group-hover:text-white">
                    <i class="fa-solid fa-droplet text-xl"></i>
                </div>
                <div class="mt-4 flex items-center justify-between">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-cyan-600">Lubricants</p>
                    <span class="rounded-full bg-yellow-50 px-2.5 py-1 text-[10px] font-semibold text-yellow-600">Low Stock</span>
                </div>
                <h3 class="mt-1 text-sm font-bold text-gray-900">Engine Coolant</h3>
                <p class="mt-1 text-[10px] font-medium text-gray-400">Part Code: LUB-005</p>
                <p class="mt-2 min-h-10 text-[11px] leading-relaxed text-gray-500">Engine coolant to help regulate temperature and prevent overheating.</p>
                <div class="mt-4 grid grid-cols-2 gap-3 border-t border-gray-100 pt-3">
                    <div>
                        <p class="text-[10px] text-gray-400">Unit Price</p>
                        <p class="mt-0.5 text-lg font-bold text-gray-900">Rs. 2,800</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] text-gray-400">Available Quantity</p>
                        <p class="mt-0.5 text-lg font-bold text-yellow-600">6 <span class="text-[10px] font-medium text-gray-400">units</span></p>
                    </div>
                </div>
                <div class="mt-3 border-t border-gray-100 pt-3">
                    <p class="truncate text-[10px] text-gray-400"><i class="fa-solid fa-truck-fast mr-1"></i> Supplier: Auto Parts Lanka</p>
                </div>
                <div class="mt-4 flex gap-2">
                    <a href="edit-part.php?id=5" class="flex h-9 flex-1 items-center justify-center gap-2 rounded-lg border border-orange-200 text-[11px] font-semibold text-[#E89000] transition hover:bg-[#E89000] hover:text-white">
                        <i class="fa-solid fa-pen-to-square"></i> Edit Part
                    </a>
                    <form action="delete-part.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this spare part?');">
                        <input type="hidden" name="part_id" value="5">
                        <button type="submit" aria-label="Delete Engine Coolant" class="flex h-9 w-10 items-center justify-center rounded-lg border border-red-200 text-red-500 transition hover:bg-red-500 hover:text-white">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </article>

            <!-- Part 6 -->
            <article class="group rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-purple-300 hover:shadow-md">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-50 text-purple-600 transition group-hover:bg-purple-600 group-hover:text-white">
                    <i class="fa-solid fa-car-battery text-xl"></i>
                </div>
                <div class="mt-4 flex items-center justify-between">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-purple-600">Electrical Parts</p>
                    <span class="rounded-full bg-green-50 px-2.5 py-1 text-[10px] font-semibold text-green-600">In Stock</span>
                </div>
                <h3 class="mt-1 text-sm font-bold text-gray-900">Car Battery</h3>
                <p class="mt-1 text-[10px] font-medium text-gray-400">Part Code: ELE-006</p>
                <p class="mt-2 min-h-10 text-[11px] leading-relaxed text-gray-500">Reliable automotive battery for vehicle starting and electrical systems.</p>
                <div class="mt-4 grid grid-cols-2 gap-3 border-t border-gray-100 pt-3">
                    <div>
                        <p class="text-[10px] text-gray-400">Unit Price</p>
                        <p class="mt-0.5 text-lg font-bold text-gray-900">Rs. 28,500</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] text-gray-400">Available Quantity</p>
                        <p class="mt-0.5 text-lg font-bold text-green-600">15 <span class="text-[10px] font-medium text-gray-400">units</span></p>
                    </div>
                </div>
                <div class="mt-3 border-t border-gray-100 pt-3">
                    <p class="truncate text-[10px] text-gray-400"><i class="fa-solid fa-truck-fast mr-1"></i> Supplier: PowerTech Lanka</p>
                </div>
                <div class="mt-4 flex gap-2">
                    <a href="edit-part.php?id=6" class="flex h-9 flex-1 items-center justify-center gap-2 rounded-lg border border-orange-200 text-[11px] font-semibold text-[#E89000] transition hover:bg-[#E89000] hover:text-white">
                        <i class="fa-solid fa-pen-to-square"></i> Edit Part
                    </a>
                    <form action="delete-part.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this spare part?');">
                        <input type="hidden" name="part_id" value="6">
                        <button type="submit" aria-label="Delete Car Battery" class="flex h-9 w-10 items-center justify-center rounded-lg border border-red-200 text-red-500 transition hover:bg-red-500 hover:text-white">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </article>

            <!-- Part 7 -->
            <article class="group rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-indigo-300 hover:shadow-md">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 transition group-hover:bg-indigo-600 group-hover:text-white">
                    <i class="fa-solid fa-gears text-xl"></i>
                </div>
                <div class="mt-4 flex items-center justify-between">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-indigo-600">Suspension</p>
                    <span class="rounded-full bg-green-50 px-2.5 py-1 text-[10px] font-semibold text-green-600">In Stock</span>
                </div>
                <h3 class="mt-1 text-sm font-bold text-gray-900">Shock Absorber</h3>
                <p class="mt-1 text-[10px] font-medium text-gray-400">Part Code: SUS-007</p>
                <p class="mt-2 min-h-10 text-[11px] leading-relaxed text-gray-500">Suspension component designed to improve ride comfort and stability.</p>
                <div class="mt-4 grid grid-cols-2 gap-3 border-t border-gray-100 pt-3">
                    <div>
                        <p class="text-[10px] text-gray-400">Unit Price</p>
                        <p class="mt-0.5 text-lg font-bold text-gray-900">Rs. 12,500</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] text-gray-400">Available Quantity</p>
                        <p class="mt-0.5 text-lg font-bold text-green-600">20 <span class="text-[10px] font-medium text-gray-400">units</span></p>
                    </div>
                </div>
                <div class="mt-3 border-t border-gray-100 pt-3">
                    <p class="truncate text-[10px] text-gray-400"><i class="fa-solid fa-truck-fast mr-1"></i> Supplier: City Auto Supplies</p>
                </div>
                <div class="mt-4 flex gap-2">
                    <a href="edit-part.php?id=7" class="flex h-9 flex-1 items-center justify-center gap-2 rounded-lg border border-orange-200 text-[11px] font-semibold text-[#E89000] transition hover:bg-[#E89000] hover:text-white">
                        <i class="fa-solid fa-pen-to-square"></i> Edit Part
                    </a>
                    <form action="delete-part.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this spare part?');">
                        <input type="hidden" name="part_id" value="7">
                        <button type="submit" aria-label="Delete Shock Absorber" class="flex h-9 w-10 items-center justify-center rounded-lg border border-red-200 text-red-500 transition hover:bg-red-500 hover:text-white">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </article>

            <!-- Part 8 -->
            <article class="group rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-yellow-300 hover:shadow-md">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-yellow-50 text-yellow-600 transition group-hover:bg-yellow-500 group-hover:text-white">
                    <i class="fa-solid fa-circle-stop text-xl"></i>
                </div>
                <div class="mt-4 flex items-center justify-between">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-yellow-600">Brake System</p>
                    <span class="rounded-full bg-green-50 px-2.5 py-1 text-[10px] font-semibold text-green-600">In Stock</span>
                </div>
                <h3 class="mt-1 text-sm font-bold text-gray-900">Brake Disc</h3>
                <p class="mt-1 text-[10px] font-medium text-gray-400">Part Code: BRK-008</p>
                <p class="mt-2 min-h-10 text-[11px] leading-relaxed text-gray-500">Precision-engineered brake disc for consistent stopping performance.</p>
                <div class="mt-4 grid grid-cols-2 gap-3 border-t border-gray-100 pt-3">
                    <div>
                        <p class="text-[10px] text-gray-400">Unit Price</p>
                        <p class="mt-0.5 text-lg font-bold text-gray-900">Rs. 11,000</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] text-gray-400">Available Quantity</p>
                        <p class="mt-0.5 text-lg font-bold text-green-600">18 <span class="text-[10px] font-medium text-gray-400">units</span></p>
                    </div>
                </div>
                <div class="mt-3 border-t border-gray-100 pt-3">
                    <p class="truncate text-[10px] text-gray-400"><i class="fa-solid fa-truck-fast mr-1"></i> Supplier: Auto Parts Lanka</p>
                </div>
                <div class="mt-4 flex gap-2">
                    <a href="edit-part.php?id=8" class="flex h-9 flex-1 items-center justify-center gap-2 rounded-lg border border-orange-200 text-[11px] font-semibold text-[#E89000] transition hover:bg-[#E89000] hover:text-white">
                        <i class="fa-solid fa-pen-to-square"></i> Edit Part
                    </a>
                    <form action="delete-part.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this spare part?');">
                        <input type="hidden" name="part_id" value="8">
                        <button type="submit" aria-label="Delete Brake Disc" class="flex h-9 w-10 items-center justify-center rounded-lg border border-red-200 text-red-500 transition hover:bg-red-500 hover:text-white">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </article>

            <!-- Part 9 -->
            <article class="group rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-teal-300 hover:shadow-md">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-teal-50 text-teal-600 transition group-hover:bg-teal-600 group-hover:text-white">
                    <i class="fa-solid fa-oil-can text-xl"></i>
                </div>
                <div class="mt-4 flex items-center justify-between">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-teal-600">Lubricants</p>
                    <span class="rounded-full bg-green-50 px-2.5 py-1 text-[10px] font-semibold text-green-600">In Stock</span>
                </div>
                <h3 class="mt-1 text-sm font-bold text-gray-900">Engine Oil</h3>
                <p class="mt-1 text-[10px] font-medium text-gray-400">Part Code: LUB-009</p>
                <p class="mt-2 min-h-10 text-[11px] leading-relaxed text-gray-500">Premium engine oil formulated to protect engine components.</p>
                <div class="mt-4 grid grid-cols-2 gap-3 border-t border-gray-100 pt-3">
                    <div>
                        <p class="text-[10px] text-gray-400">Unit Price</p>
                        <p class="mt-0.5 text-lg font-bold text-gray-900">Rs. 5,500</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] text-gray-400">Available Quantity</p>
                        <p class="mt-0.5 text-lg font-bold text-green-600">60 <span class="text-[10px] font-medium text-gray-400">units</span></p>
                    </div>
                </div>
                <div class="mt-3 border-t border-gray-100 pt-3">
                    <p class="truncate text-[10px] text-gray-400"><i class="fa-solid fa-truck-fast mr-1"></i> Supplier: Engine Pro Lanka</p>
                </div>
                <div class="mt-4 flex gap-2">
                    <a href="edit-part.php?id=9" class="flex h-9 flex-1 items-center justify-center gap-2 rounded-lg border border-orange-200 text-[11px] font-semibold text-[#E89000] transition hover:bg-[#E89000] hover:text-white">
                        <i class="fa-solid fa-pen-to-square"></i> Edit Part
                    </a>
                    <form action="delete-part.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this spare part?');">
                        <input type="hidden" name="part_id" value="9">
                        <button type="submit" aria-label="Delete Engine Oil" class="flex h-9 w-10 items-center justify-center rounded-lg border border-red-200 text-red-500 transition hover:bg-red-500 hover:text-white">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </article>

            <!-- Part 10 -->
            <article class="group rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-pink-300 hover:shadow-md">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-pink-50 text-pink-600 transition group-hover:bg-pink-600 group-hover:text-white">
                    <i class="fa-solid fa-fan text-xl"></i>
                </div>
                <div class="mt-4 flex items-center justify-between">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-pink-600">Electrical Parts</p>
                    <span class="rounded-full bg-yellow-50 px-2.5 py-1 text-[10px] font-semibold text-yellow-600">Low Stock</span>
                </div>
                <h3 class="mt-1 text-sm font-bold text-gray-900">Radiator Fan</h3>
                <p class="mt-1 text-[10px] font-medium text-gray-400">Part Code: ELE-010</p>
                <p class="mt-2 min-h-10 text-[11px] leading-relaxed text-gray-500">Cooling fan assembly for maintaining optimal engine temperature.</p>
                <div class="mt-4 grid grid-cols-2 gap-3 border-t border-gray-100 pt-3">
                    <div>
                        <p class="text-[10px] text-gray-400">Unit Price</p>
                        <p class="mt-0.5 text-lg font-bold text-gray-900">Rs. 14,500</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] text-gray-400">Available Quantity</p>
                        <p class="mt-0.5 text-lg font-bold text-yellow-600">5 <span class="text-[10px] font-medium text-gray-400">units</span></p>
                    </div>
                </div>
                <div class="mt-3 border-t border-gray-100 pt-3">
                    <p class="truncate text-[10px] text-gray-400"><i class="fa-solid fa-truck-fast mr-1"></i> Supplier: PowerTech Lanka</p>
                </div>
                <div class="mt-4 flex gap-2">
                    <a href="edit-part.php?id=10" class="flex h-9 flex-1 items-center justify-center gap-2 rounded-lg border border-orange-200 text-[11px] font-semibold text-[#E89000] transition hover:bg-[#E89000] hover:text-white">
                        <i class="fa-solid fa-pen-to-square"></i> Edit Part
                    </a>
                    <form action="delete-part.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this spare part?');">
                        <input type="hidden" name="part_id" value="10">
                        <button type="submit" aria-label="Delete Radiator Fan" class="flex h-9 w-10 items-center justify-center rounded-lg border border-red-200 text-red-500 transition hover:bg-red-500 hover:text-white">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </article>

            <!-- Part 11 -->
            <article class="group rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-gray-400 hover:shadow-md">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 text-gray-500 transition group-hover:bg-gray-700 group-hover:text-white">
                    <i class="fa-solid fa-car-side text-xl"></i>
                </div>
                <div class="mt-4 flex items-center justify-between">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-500">Suspension</p>
                    <span class="rounded-full bg-red-50 px-2.5 py-1 text-[10px] font-semibold text-red-500">Out of Stock</span>
                </div>
                <h3 class="mt-1 text-sm font-bold text-gray-900">Control Arm</h3>
                <p class="mt-1 text-[10px] font-medium text-gray-400">Part Code: SUS-011</p>
                <p class="mt-2 min-h-10 text-[11px] leading-relaxed text-gray-500">Suspension control arm for stable wheel positioning and handling.</p>
                <div class="mt-4 grid grid-cols-2 gap-3 border-t border-gray-100 pt-3">
                    <div>
                        <p class="text-[10px] text-gray-400">Unit Price</p>
                        <p class="mt-0.5 text-lg font-bold text-gray-900">Rs. 16,000</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] text-gray-400">Available Quantity</p>
                        <p class="mt-0.5 text-lg font-bold text-red-500">0 <span class="text-[10px] font-medium text-gray-400">units</span></p>
                    </div>
                </div>
                <div class="mt-3 border-t border-gray-100 pt-3">
                    <p class="truncate text-[10px] text-gray-400"><i class="fa-solid fa-truck-fast mr-1"></i> Supplier: City Auto Supplies</p>
                </div>
                <div class="mt-4 flex gap-2">
                    <a href="edit-part.php?id=11" class="flex h-9 flex-1 items-center justify-center gap-2 rounded-lg border border-orange-200 text-[11px] font-semibold text-[#E89000] transition hover:bg-[#E89000] hover:text-white">
                        <i class="fa-solid fa-pen-to-square"></i> Edit Part
                    </a>
                    <form action="delete-part.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this spare part?');">
                        <input type="hidden" name="part_id" value="11">
                        <button type="submit" aria-label="Delete Control Arm" class="flex h-9 w-10 items-center justify-center rounded-lg border border-red-200 text-red-500 transition hover:bg-red-500 hover:text-white">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </article>

            <!-- Part 12 -->
            <article class="group rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-gray-400 hover:shadow-md">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 text-gray-500 transition group-hover:bg-gray-700 group-hover:text-white">
                    <i class="fa-solid fa-lightbulb text-xl"></i>
                </div>
                <div class="mt-4 flex items-center justify-between">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-500">Electrical Parts</p>
                    <span class="rounded-full bg-yellow-50 px-2.5 py-1 text-[10px] font-semibold text-yellow-600">Low Stock</span>
                </div>
                <h3 class="mt-1 text-sm font-bold text-gray-900">Headlight Bulb</h3>
                <p class="mt-1 text-[10px] font-medium text-gray-400">Part Code: ELE-012</p>
                <p class="mt-2 min-h-10 text-[11px] leading-relaxed text-gray-500">Automotive headlight bulb for clear visibility during night driving.</p>
                <div class="mt-4 grid grid-cols-2 gap-3 border-t border-gray-100 pt-3">
                    <div>
                        <p class="text-[10px] text-gray-400">Unit Price</p>
                        <p class="mt-0.5 text-lg font-bold text-gray-900">Rs. 1,800</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] text-gray-400">Available Quantity</p>
                        <p class="mt-0.5 text-lg font-bold text-yellow-600">4 <span class="text-[10px] font-medium text-gray-400">units</span></p>
                    </div>
                </div>
                <div class="mt-3 border-t border-gray-100 pt-3">
                    <p class="truncate text-[10px] text-gray-400"><i class="fa-solid fa-truck-fast mr-1"></i> Supplier: PowerTech Lanka</p>
                </div>
                <div class="mt-4 flex gap-2">
                    <a href="edit-part.php?id=12" class="flex h-9 flex-1 items-center justify-center gap-2 rounded-lg border border-orange-200 text-[11px] font-semibold text-[#E89000] transition hover:bg-[#E89000] hover:text-white">
                        <i class="fa-solid fa-pen-to-square"></i> Edit Part
                    </a>
                    <form action="delete-part.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this spare part?');">
                        <input type="hidden" name="part_id" value="12">
                        <button type="submit" aria-label="Delete Headlight Bulb" class="flex h-9 w-10 items-center justify-center rounded-lg border border-red-200 text-red-500 transition hover:bg-red-500 hover:text-white">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </article>

        </div>

        <!-- Pagination -->
        <div class="flex flex-col gap-3 border-t border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-[11px] text-gray-500">
                Showing <span class="font-semibold text-gray-800">1–12</span> of
                <span class="font-semibold text-gray-800">156</span> spare parts
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
                <button class="flex h-8 w-8 items-center justify-center rounded-md border border-gray-200 text-xs font-medium text-gray-600 transition hover:border-orange-300 hover:text-[#E89000]">13</button>
                <button class="flex h-8 items-center gap-1 rounded-md border border-gray-200 px-3 text-[11px] font-medium text-gray-600 transition hover:border-orange-300 hover:text-[#E89000]">
                    Next
                    <i class="fa-solid fa-chevron-right text-[9px]"></i>
                </button>
            </div>
        </div>

    </section>

    <!-- Add New Spare Part -->
    <section id="add-spare-part" class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

        <!-- Form Header -->
        <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-4">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-100 text-[#E89000]">
                <i class="fa-solid fa-plus text-lg"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-gray-900">Add New Spare Part</h2>
                <p class="mt-0.5 text-xs text-gray-500">Register a new spare part in your inventory</p>
            </div>
        </div>

        <form action="add-part.php" method="POST" class="space-y-5 p-5">

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                <!-- Part Name -->
                <div>
                    <label for="part-name" class="mb-1.5 block text-xs font-semibold text-gray-700">Part Name</label>
                    <input
                        id="part-name"
                        name="part_name"
                        type="text"
                        placeholder="Enter spare part name"
                        required
                        class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                </div>

                

                <!-- Category -->
                <div>
                    <label for="part-category" class="mb-1.5 block text-xs font-semibold text-gray-700">Part Category</label>
                    <select
                        id="part-category"
                        name="category"
                        required
                        class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                        <option value="">Select Category</option>
                        <option value="Engine Parts">Engine Parts</option>
                        <option value="Brake System">Brake System</option>
                        <option value="Filters">Filters</option>
                        <option value="Electrical Parts">Electrical Parts</option>
                        <option value="Suspension">Suspension</option>
                        <option value="Lubricants">Lubricants</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <!-- Quantity -->
                <div>
                    <label for="part-quantity" class="mb-1.5 block text-xs font-semibold text-gray-700">Available Quantity</label>
                    <input
                        id="part-quantity"
                        name="quantity"
                        type="number"
                        min="0"
                        placeholder="Enter available quantity"
                        required
                        class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                </div>

                <!-- Unit Price -->
                <div>
                    <label for="part-price" class="mb-1.5 block text-xs font-semibold text-gray-700">Unit Price (Rs.)</label>
                    <input
                        id="part-price"
                        name="unit_price"
                        type="number"
                        min="0"
                        step="0.01"
                        placeholder="Enter unit price"
                        required
                        class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                </div>

                <!-- Reorder Level -->
                <div>
                    <label for="reorder-level" class="mb-1.5 block text-xs font-semibold text-gray-700">Reorder Level</label>
                    <input
                        id="reorder-level"
                        name="reorder_level"
                        type="number"
                        min="0"
                        placeholder="Enter minimum stock level"
                        required
                        class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                </div>

                <!-- Supplier -->
                <div class="md:col-span-2">
                    <label for="part-supplier" class="mb-1.5 block text-xs font-semibold text-gray-700">Supplier Name</label>
                    <input
                        id="part-supplier"
                        name="supplier"
                        type="text"
                        placeholder="Enter supplier or company name"
                        required
                        class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="part-description" class="mb-1.5 block text-xs font-semibold text-gray-700">Part Description</label>
                    <textarea
                        id="part-description"
                        name="description"
                        rows="3"
                        placeholder="Describe the spare part..."
                        class="w-full resize-none rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100"></textarea>
                </div>

            </div>

            <!-- Form Buttons -->
            <div class="flex flex-col-reverse gap-2 border-t border-gray-100 pt-4 sm:flex-row sm:justify-end">
                <button
                    type="reset"
                    class="inline-flex h-10 cursor-pointer items-center justify-center gap-2 rounded-lg border border-gray-200 px-5 text-xs font-semibold text-gray-600 transition hover:bg-gray-50">
                    <i class="fa-solid fa-rotate-left"></i>
                    Clear Form
                </button>

                <button
                    type="submit"
                    class="inline-flex h-10 cursor-pointer items-center justify-center gap-2 rounded-lg bg-[#E89000] px-5 text-xs font-semibold text-white shadow-sm transition hover:bg-orange-600">
                    <i class="fa-solid fa-plus"></i>
                    Add Spare Part
                </button>
            </div>

        </form>
    </section>

</div>

    </main>
</div>
</body>

</html>