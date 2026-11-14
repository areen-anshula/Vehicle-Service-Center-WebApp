
<?php
$required_role = "receptionist";
require_once "../../PHP/auth_guard.php";
include "../../PHP/receptionist_user_managent_backend.php";
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
                            class="group flex items-center gap-3 rounded-lg px-4 py-3 text-xs font-medium text-gray-300 transition duration-200 hover:bg-white/10 hover:text-white">
                            <i
                                class="fa-solid fa-table-columns w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>
                            Dashboard
                        </a>
                    </li>

                    <!-- User Management - Active -->
                    <li>
                        <a href="user_management.php"
                            class="flex items-center justify-between rounded-lg bg-[#E89000] px-4 py-3 text-xs font-semibold text-white shadow-sm">
                            <span class="flex items-center gap-3">
                                <i class="fa-solid fa-users w-4 text-center"></i>
                                User Management
                            </span>
                            <i class="fa-solid fa-chevron-right text-[9px]"></i>
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
                    <li>
                        <a href="inventory.php"
                            class="group flex items-center justify-between rounded-lg px-4 py-3 text-xs font-medium text-gray-300 transition duration-200 hover:bg-white/10 hover:text-white">
                            <span class="flex items-center gap-3">
                                <i class="fa-solid fa-users w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>
                                Inventory
                            </span>
                            <i class="fa-solid fa-chevron-right text-[9px] text-gray-500"></i>
                        </a>
                    </li>

                    <!-- Reports -->
                    <li>
                        <a href="reports.php"
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
                        <h2 class="text-lg font-bold text-gray-900 sm:text-xl">Receptionist Dashboard</h2>
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

            <!-- PAGE CONTENT: USER MANAGEMENT -->
            <div class="space-y-6 p-4 sm:p-6 lg:p-8">

                <!-- Page Heading -->
                <section class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-100 text-[#E89000]">
                                <i class="fa-solid fa-users text-lg"></i>
                            </div>
                            <div>
                                <h1 class="text-xl font-bold tracking-tight text-gray-900">User Management</h1>
                                <p class="mt-0.5 text-xs text-gray-500">View and manage registered customers</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-[11px] text-gray-500 shadow-sm">
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
        <h3 class="mt-3 text-2xl font-bold text-gray-900">
            <?php echo $clients_count; ?>
        </h3>
        <p class="mt-0.5 text-xs font-semibold text-gray-700">Total Customers</p>
        <p class="mt-1 text-[10px] text-gray-400">Registered customer accounts</p>
    </div>

    <!-- Active Customers -->
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
        <div class="flex items-center justify-between">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-50 text-green-600">
                <i class="fa-solid fa-user-check text-lg"></i>
            </div>
            <span class="rounded-full bg-green-50 px-2.5 py-1 text-[10px] font-semibold text-green-600">Registered</span>
        </div>
        <h3 class="mt-3 text-2xl font-bold text-gray-900">
            <?php echo $clients_count; ?>
        </h3>
        <p class="mt-0.5 text-xs font-semibold text-gray-700">Registered Accounts</p>
        <p class="mt-1 text-[10px] text-gray-400">Customer accounts in the database</p>
    </div>

    <!-- Customer Information -->
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
        <div class="flex items-center justify-between">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                <i class="fa-solid fa-address-card text-lg"></i>
            </div>
            <span class="rounded-full bg-blue-50 px-2.5 py-1 text-[10px] font-semibold text-blue-600">Accounts</span>
        </div>
        <h3 class="mt-3 text-2xl font-bold text-gray-900">
            <?php echo $clients_count; ?>
        </h3>
        <p class="mt-0.5 text-xs font-semibold text-gray-700">Customer Records</p>
        <p class="mt-1 text-[10px] text-gray-400">Available customer profiles</p>
    </div>

    <!-- Management -->
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
        <div class="flex items-center justify-between">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-50 text-purple-600">
                <i class="fa-solid fa-user-gear text-lg"></i>
            </div>
            <span class="rounded-full bg-purple-50 px-2.5 py-1 text-[10px] font-semibold text-purple-600">Management</span>
        </div>
        <h3 class="mt-3 text-2xl font-bold text-gray-900">Available</h3>
        <p class="mt-0.5 text-xs font-semibold text-gray-700">Customer Management</p>
        <p class="mt-1 text-[10px] text-gray-400">Update and manage customer details</p>
    </div>

</section>

                <!-- Customer Management Table -->
                <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

                    <!-- Table Header -->
                    <div
                        class="flex flex-col gap-3 border-b border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-100">
                                <i class="fa-solid fa-address-book text-lg text-[#E89000]"></i>
                            </div>
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="text-base font-bold text-gray-900">Registered Customers</h2>
                                    <span
                                        class="rounded-full bg-orange-100 px-2 py-0.5 text-[10px] font-semibold text-[#E89000]">
                                        <?php echo $clients_count; ?> Customers
                                    </span>
                                </div>
                                <p class="mt-0.5 text-xs text-gray-500">View customer information and update their
                                    details</p>
                            </div>
                        </div>

                        <span class="inline-flex items-center gap-2 text-[11px] font-medium text-gray-500">
                            <i class="fa-solid fa-shield-halved text-green-600"></i>
                            Customer account management
                        </span>
                    </div>

                    <!-- Feedback Message -->
                    <?php if (!empty($message)): ?>
                        <div class="mx-5 mt-4 rounded-lg border px-4 py-3 text-xs font-medium
            <?php echo $message_type === 'success'
                ? 'border-green-200 bg-green-50 text-green-700'
                : 'border-red-200 bg-red-50 text-red-700'; ?>">
                            <i
                                class="fa-solid <?php echo $message_type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'; ?> mr-2"></i>
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Search and Filters -->
                    <div class="flex flex-col gap-3 px-5 py-4 lg:flex-row lg:items-center">

                        <div class="relative flex-1">
                            <i
                                class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                            <input id="customerSearch" type="search"
                                placeholder="Search by customer name, ID, email or phone..."
                                class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 pl-9 pr-3 text-xs text-gray-700 outline-none transition placeholder:text-gray-400 focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                        </div>

                        <div class="flex flex-col gap-2 sm:flex-row">
                            <select id="customerSort"
                                class="h-10 rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-600 outline-none focus:border-[#E89000] focus:ring-2 focus:ring-orange-100">
                                <option value="newest">Newest First</option>
                                <option value="oldest">Oldest First</option>
                                <option value="az">Name A - Z</option>
                                <option value="za">Name Z - A</option>
                            </select>

                            <button id="resetCustomerSearch" type="button"
                                class="inline-flex h-10 cursor-pointer items-center justify-center gap-2 rounded-lg bg-[#FFA500] px-4 text-xs font-semibold text-white transition hover:bg-[#E89000]">
                                <i class="fa-solid fa-rotate-left text-[10px]"></i>
                                Reset
                            </button>
                        </div>
                    </div>

                    <!-- Customer Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[950px] text-left">

                            <thead>
                                <tr class="border-y border-gray-100 bg-gray-50">
                                    <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">
                                        Customer</th>
                                    <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">
                                        Customer ID</th>
                                    <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">
                                        Email Address</th>
                                    <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">
                                        Phone Number</th>
                                    <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">
                                        Account Type</th>
                                    <th
                                        class="px-5 py-3 text-center text-[10px] font-bold uppercase tracking-wider text-gray-500">
                                        Action</th>
                                </tr>
                            </thead>

                            <tbody id="customerTableBody" class="divide-y divide-gray-100 text-xs text-gray-600">

                                <?php if (!empty($clients)): ?>

                                    <?php foreach ($clients as $index => $client): ?>

                                        <?php
                                        $name = $client['full_name'] ?? '';
                                        $email = $client['email'] ?? '';
                                        $phone = $client['phone'] ?? '';
                                        $id = (int) $client['id'];

                                        $initials = '';
                                        $nameParts = preg_split('/\s+/', trim($name));

                                        foreach ($nameParts as $part) {
                                            if ($part !== '') {
                                                $initials .= strtoupper(substr($part, 0, 1));
                                            }
                                        }

                                        $initials = substr($initials, 0, 2);

                                        $avatarColors = [
                                            'bg-orange-100 text-[#E89000]',
                                            'bg-purple-100 text-purple-600',
                                            'bg-blue-100 text-blue-600',
                                            'bg-teal-100 text-teal-600',
                                            'bg-pink-100 text-pink-600'
                                        ];

                                        $avatarColor = $avatarColors[$index % count($avatarColors)];
                                        ?>

                                        <tr class="customer-row transition hover:bg-orange-50/30" data-id="<?php echo $id; ?>"
                                            data-name="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>"
                                            data-email="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>"
                                            data-phone="<?php echo htmlspecialchars($phone, ENT_QUOTES, 'UTF-8'); ?>">

                                            <td class="px-5 py-3.5">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full <?php echo $avatarColor; ?> text-xs font-bold">
                                                        <?php echo htmlspecialchars($initials); ?>
                                                    </div>

                                                    <div>
                                                        <p class="font-semibold text-gray-800">
                                                            <?php echo htmlspecialchars($name); ?>
                                                        </p>
                                                        <p class="mt-0.5 text-[10px] text-gray-400">Customer</p>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="px-5 py-3.5 font-semibold text-gray-800">
                                                CUS-<?php echo str_pad($id, 4, '0', STR_PAD_LEFT); ?>
                                            </td>

                                            <td class="px-5 py-3.5">
                                                <span class="font-medium text-gray-700">
                                                    <?php echo htmlspecialchars($email); ?>
                                                </span>
                                            </td>

                                            <td class="px-5 py-3.5 text-gray-600">
                                                <?php echo htmlspecialchars($phone ?: 'Not provided'); ?>
                                            </td>

                                            <td class="px-5 py-3.5">
                                                <span
                                                    class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-1 text-[10px] font-semibold text-green-600">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                                    Customer
                                                </span>
                                            </td>

                                            <td class="px-5 py-3.5 text-center">
                                                <button type="button" onclick="openCustomerModal(this)"
                                                    class="inline-flex cursor-pointer items-center gap-1.5 rounded-md border border-orange-200 px-3 py-1.5 text-[11px] font-semibold text-[#E89000] transition hover:border-[#E89000] hover:bg-[#E89000] hover:text-white">

                                                    <i class="fa-solid fa-ellipsis"></i>
                                                    Manage
                                                </button>
                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                <?php else: ?>

                                    <tr>
                                        <td colspan="6" class="px-5 py-12 text-center">
                                            <div class="flex flex-col items-center">
                                                <i class="fa-solid fa-users text-3xl text-gray-300"></i>
                                                <p class="mt-3 text-sm font-semibold text-gray-600">No customers found</p>
                                                <p class="mt-1 text-xs text-gray-400">Registered customer accounts will
                                                    appear here.</p>
                                            </div>
                                        </td>
                                    </tr>

                                <?php endif; ?>

                            </tbody>
                        </table>
                    </div>

                    <!-- Table Footer -->
                    <div
                        class="flex flex-col gap-2 border-t border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <p id="customerCount" class="text-[11px] text-gray-500">
                            Showing <?php echo $clients_count; ?> customers
                        </p>

                        <span class="text-[10px] text-gray-400">
                            All registered customers
                        </span>
                    </div>

                </section>

            </div>
        </main>
    </div>

    <!-- CUSTOMER MANAGEMENT MODAL -->
    <div id="customerModal"
        class="fixed inset-0 z-[100] hidden items-center justify-center bg-gray-950/60 p-4 backdrop-blur-sm">

        <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl">

            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5">

                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-100 text-[#E89000]">
                        <i class="fa-solid fa-user-pen text-lg"></i>
                    </div>

                    <div>
                        <h2 class="text-base font-bold text-gray-900">Manage Customer</h2>
                        <p class="mt-0.5 text-[11px] text-gray-500">Update or delete customer information</p>
                    </div>
                </div>

                <button type="button" onclick="closeCustomerModal()"
                    class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-full bg-gray-100 text-gray-500 transition hover:bg-red-50 hover:text-red-500">
                    <i class="fa-solid fa-xmark"></i>
                </button>

            </div>

            <!-- Modal Body -->
            <div class="space-y-5 p-6">

                <!-- Customer Profile -->
                <div class="flex items-center gap-3 rounded-xl bg-gray-50 p-4">

                    <div id="modalAvatar"
                        class="flex h-12 w-12 items-center justify-center rounded-full bg-orange-100 text-sm font-bold text-[#E89000]">
                        CU
                    </div>

                    <div class="min-w-0">
                        <h3 id="modalCustomerName" class="text-sm font-bold text-gray-900">Customer Name</h3>
                        <p id="modalCustomerID" class="mt-1 text-[11px] text-gray-500">CUS-0000</p>
                    </div>

                    <span class="ml-auto rounded-full bg-green-50 px-2.5 py-1 text-[10px] font-semibold text-green-600">
                        Customer
                    </span>

                </div>

                <!-- Update Customer Form -->
                <form id="updateCustomerForm" action="../../PHP/receptionist_user_managent_backend.php" method="POST"
                    class="space-y-4">

                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

                    <input type="hidden" name="action" value="update">

                    <input type="hidden" id="modalCustomerIDInput" name="customer_id">

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-gray-700">
                            Full Name
                        </label>

                        <div class="relative">
                            <i
                                class="fa-solid fa-user absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>

                            <input type="text" id="modalFullName" name="full_name" required maxlength="150"
                                class="h-11 w-full rounded-lg border border-gray-200 bg-gray-50 pl-9 pr-3 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-gray-700">
                            Email Address
                        </label>

                        <div class="relative">
                            <i
                                class="fa-solid fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>

                            <input type="email" id="modalEmail" name="email" required maxlength="254"
                                class="h-11 w-full rounded-lg border border-gray-200 bg-gray-50 pl-9 pr-3 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-gray-700">
                            Phone Number
                        </label>

                        <div class="relative">
                            <i
                                class="fa-solid fa-phone absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>

                            <input type="tel" id="modalPhone" name="phone" required maxlength="30"
                                class="h-11 w-full rounded-lg border border-gray-200 bg-gray-50 pl-9 pr-3 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                        </div>
                    </div>

                    <!-- Modal Buttons -->
                    <div
                        class="flex flex-col-reverse gap-2 border-t border-gray-100 pt-5 sm:flex-row sm:justify-between">

                        <button type="button" onclick="deleteCustomer()"
                            class="inline-flex h-10 cursor-pointer items-center justify-center gap-2 rounded-lg border border-red-200 px-4 text-xs font-semibold text-red-500 transition hover:bg-red-50">
                            <i class="fa-solid fa-trash-can"></i>
                            Delete Customer
                        </button>

                        <div class="flex gap-2">
                            <button type="button" onclick="closeCustomerModal()"
                                class="h-10 flex-1 cursor-pointer rounded-lg border border-gray-200 px-4 text-xs font-semibold text-gray-600 transition hover:bg-gray-50 sm:flex-none">
                                Cancel
                            </button>

                            <button type="submit"
                                class="h-10 flex-1 cursor-pointer rounded-lg bg-[#E89000] px-5 text-xs font-semibold text-white transition hover:bg-orange-600 sm:flex-none">
                                <i class="fa-solid fa-floppy-disk mr-1"></i>
                                Save Changes
                            </button>
                        </div>

                    </div>

                </form>

                <!-- Separate Delete Form -->
                <form id="deleteCustomerForm" action="../../PHP/receptionist_user_managent_backend.php" method="POST"
                    class="hidden">

                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

                    <input type="hidden" name="action" value="delete">

                    <input type="hidden" id="deleteCustomerID" name="customer_id">

                </form>

            </div>
        </div>
    </div>

    <script>
        // ── Modal ──
        function openCustomerModal(btn) {
            const row = btn.closest('tr.customer-row');
            if (!row) return;

            const id   = row.dataset.id;
            const name = row.dataset.name;
            const email = row.dataset.email;
            const phone = row.dataset.phone;

            const initials = name.split(/\s+/).map(w => w[0]).join('').toUpperCase().substring(0, 2);

            document.getElementById('modalAvatar').textContent         = initials;
            document.getElementById('modalCustomerName').textContent   = name;
            document.getElementById('modalCustomerID').textContent     = 'CUS-' + String(id).padStart(4, '0');

            document.getElementById('modalCustomerIDInput').value = id;
            document.getElementById('deleteCustomerID').value     = id;
            document.getElementById('modalFullName').value        = name;
            document.getElementById('modalEmail').value           = email;
            document.getElementById('modalPhone').value           = phone;

            document.getElementById('customerModal').classList.remove('hidden');
            document.getElementById('customerModal').classList.add('flex');
        }

        function closeCustomerModal() {
            document.getElementById('customerModal').classList.add('hidden');
            document.getElementById('customerModal').classList.remove('flex');
        }

        function deleteCustomer() {
            if (confirm('Are you sure you want to delete this customer? This action cannot be undone.')) {
                document.getElementById('deleteCustomerForm').submit();
            }
        }

        // Close modal on backdrop click
        document.getElementById('customerModal').addEventListener('click', function (e) {
            if (e.target === this) closeCustomerModal();
        });

        // ── Search & Sort ──
        const searchInput = document.getElementById('customerSearch');
        const sortSelect  = document.getElementById('customerSort');
        const tbody       = document.getElementById('customerTableBody');
        const countEl     = document.getElementById('customerCount');

        function filterAndSort() {
            const term = searchInput.value.toLowerCase();
            const rows = Array.from(tbody.querySelectorAll('tr.customer-row'));

            rows.forEach(row => {
                const text = (row.dataset.name + ' ' + row.dataset.email + ' ' + row.dataset.phone + ' CUS-' + String(row.dataset.id).padStart(4, '0')).toLowerCase();
                row.style.display = text.includes(term) ? '' : 'none';
            });

            const visible = rows.filter(r => r.style.display !== 'none');

            visible.sort((a, b) => {
                switch (sortSelect.value) {
                    case 'oldest':  return Number(a.dataset.id) - Number(b.dataset.id);
                    case 'az':      return a.dataset.name.localeCompare(b.dataset.name);
                    case 'za':      return b.dataset.name.localeCompare(a.dataset.name);
                    default:        return Number(b.dataset.id) - Number(a.dataset.id);
                }
            });

            visible.forEach(r => tbody.appendChild(r));
            countEl.textContent = 'Showing ' + visible.length + ' customers';
        }

        searchInput.addEventListener('input', filterAndSort);
        sortSelect.addEventListener('change', filterAndSort);

        document.getElementById('resetCustomerSearch').addEventListener('click', function () {
            searchInput.value = '';
            sortSelect.value  = 'newest';
            filterAndSort();
        });
    </script>
</body>

</html>