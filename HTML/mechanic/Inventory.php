<?php
include "../../PHP/receptionist_inventory.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoPulse | Inventory Management</title>
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
                    <a href="dashboard.php" class="group flex items-center gap-3 justify-between rounded-lg px-4 py-3 text-xs font-medium text-gray-300 transition duration-200 hover:bg-white/10 hover:text-white">
                        <span class="flex items-center gap-3">    
                            <i class="fa-solid fa-table-columns w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>
                            Dashboard
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

                <!-- Inventory - Active -->
                <li>
                    <a href="inventory.php" class="flex items-center justify-between rounded-lg bg-[#E89000] px-4 py-3 text-xs font-semibold text-white shadow-sm">
                        <span class="flex items-center gap-3">
                            <i class="fa-solid fa-boxes-stacked w-4 text-center"></i>
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

                <div>
                    <h2 class="text-lg font-bold text-gray-900 sm:text-xl">Mechanic Dashboard</h2>
                    <p class="mt-0.5 text-[11px] text-gray-500">
                        Welcome back, <span class="font-semibold text-[#E89000]">Receptionist</span>
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
                                <!-- Place existing notifications here -->
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

        <!-- PAGE CONTENT: INVENTORY MANAGEMENT -->
        <div class="space-y-6 p-4 sm:p-6 lg:p-8">

            <!-- Page Heading -->
            <section class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-100 text-[#E89000]">
                        <i class="fa-solid fa-boxes-stacked text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold tracking-tight text-gray-900">Inventory Management</h1>
                        <p class="mt-0.5 text-xs text-gray-500">View and manage spare parts and stock levels</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-[11px] text-gray-500 shadow-sm">
                    <i class="fa-solid fa-house text-[#E89000]"></i>
                    <span>Dashboard</span>
                    <i class="fa-solid fa-chevron-right text-[9px]"></i>
                    <span class="font-semibold text-gray-800">Inventory</span>
                </div>
            </section>

            <!-- Inventory Statistics -->
            <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-50 text-[#E89000]">
                            <i class="fa-solid fa-box-open text-lg"></i>
                        </div>
                        <span class="rounded-full bg-orange-50 px-2.5 py-1 text-[10px] font-semibold text-[#E89000]">All Parts</span>
                    </div>
                    <h3 class="mt-3 text-2xl font-bold text-gray-900"><?php echo $total_parts; ?></h3>
                    <p class="mt-0.5 text-xs font-semibold text-gray-700">Total Items</p>
                    <p class="mt-1 text-[10px] text-gray-400">Distinct parts in system</p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-50 text-green-600">
                            <i class="fa-solid fa-circle-check text-lg"></i>
                        </div>
                        <span class="rounded-full bg-green-50 px-2.5 py-1 text-[10px] font-semibold text-green-600">Healthy</span>
                    </div>
                    <h3 class="mt-3 text-2xl font-bold text-gray-900"><?php echo $in_stock_parts; ?></h3>
                    <p class="mt-0.5 text-xs font-semibold text-gray-700">In Stock</p>
                    <p class="mt-1 text-[10px] text-gray-400">Adequate stock levels</p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-yellow-50 text-yellow-500">
                            <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                        </div>
                        <span class="rounded-full bg-yellow-50 px-2.5 py-1 text-[10px] font-semibold text-yellow-600">Warning</span>
                    </div>
                    <h3 class="mt-3 text-2xl font-bold text-gray-900"><?php echo $low_stock_parts; ?></h3>
                    <p class="mt-0.5 text-xs font-semibold text-gray-700">Low Stock</p>
                    <p class="mt-1 text-[10px] text-gray-400">5 items or fewer remaining</p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-50 text-red-500">
                            <i class="fa-solid fa-ban text-lg"></i>
                        </div>
                        <span class="rounded-full bg-red-50 px-2.5 py-1 text-[10px] font-semibold text-red-500">Depleted</span>
                    </div>
                    <h3 class="mt-3 text-2xl font-bold text-gray-900"><?php echo $out_of_stock_parts; ?></h3>
                    <p class="mt-0.5 text-xs font-semibold text-gray-700">Out of Stock</p>
                    <p class="mt-1 text-[10px] text-gray-400">Currently unavailable items</p>
                </div>

            </section>

            <!-- Inventory List -->
            <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

                <!-- Section Header -->
                <div class="flex flex-col gap-3 border-b border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-100">
                            <i class="fa-solid fa-list text-lg text-[#E89000]"></i>
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="text-base font-bold text-gray-900">Spare Parts Directory</h2>
                                <span class="rounded-full bg-orange-100 px-2 py-0.5 text-[10px] font-semibold text-[#E89000]"><?php echo $total_parts; ?> Items</span>
                            </div>
                            <p class="mt-0.5 text-xs text-gray-500">Browse spare parts, descriptions, and current inventory counts</p>
                        </div>
                    </div>
                </div>

                <!-- Search and Filters -->
                <div class="flex flex-col gap-3 px-5 py-4 lg:flex-row lg:items-center">

                    <div class="relative flex-1">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                        <input
                            id="inventorySearch"
                            type="search"
                            placeholder="Search by part name..."
                            class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 pl-9 pr-3 text-xs text-gray-700 outline-none transition placeholder:text-gray-400 focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                    </div>

                    <div class="flex flex-col gap-2 sm:flex-row">
                        <select class="h-10 rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-600 outline-none transition focus:border-[#E89000] focus:ring-2 focus:ring-orange-100">
                            <option>All Statuses</option>
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

                <!-- Part Cards -->
                <div id="inventoryCards" class="grid grid-cols-1 gap-4 border-t border-gray-100 bg-gray-50/50 p-5 sm:grid-cols-2 xl:grid-cols-3">

                    <?php
                    // Color themes specifically tailored for auto parts/hardware
                    $themes = [
                        ['border' => 'orange', 'bg' => 'bg-orange-50', 'text' => 'text-[#E89000]', 'hover_bg' => 'group-hover:bg-[#E89000]', 'icon' => 'fa-solid fa-gear'],
                        ['border' => 'blue',   'bg' => 'bg-blue-50',   'text' => 'text-blue-600',   'hover_bg' => 'group-hover:bg-blue-600',   'icon' => 'fa-solid fa-battery-full'],
                        ['border' => 'green',  'bg' => 'bg-green-50',  'text' => 'text-green-600',  'hover_bg' => 'group-hover:bg-green-600',  'icon' => 'fa-solid fa-oil-can'],
                        ['border' => 'purple', 'bg' => 'bg-purple-50', 'text' => 'text-purple-600', 'hover_bg' => 'group-hover:bg-purple-600', 'icon' => 'fa-solid fa-plug'],
                        ['border' => 'cyan',   'bg' => 'bg-cyan-50',   'text' => 'text-cyan-600',   'hover_bg' => 'group-hover:bg-cyan-600',   'icon' => 'fa-solid fa-snowflake'],
                        ['border' => 'yellow', 'bg' => 'bg-yellow-50', 'text' => 'text-yellow-600', 'hover_bg' => 'group-hover:bg-yellow-500', 'icon' => 'fa-solid fa-lightbulb'],
                        ['border' => 'indigo', 'bg' => 'bg-indigo-50', 'text' => 'text-indigo-600', 'hover_bg' => 'group-hover:bg-indigo-600', 'icon' => 'fa-solid fa-fan'],
                        ['border' => 'pink',   'bg' => 'bg-pink-50',   'text' => 'text-pink-600',   'hover_bg' => 'group-hover:bg-pink-600',   'icon' => 'fa-solid fa-toolbox'],
                    ];

                    if ($all_parts && $all_parts->num_rows > 0):
                        $index = 0;
                        while ($part = $all_parts->fetch_assoc()):
                            $qty = (int)$part['quantity'];
                            $theme = $themes[$index % count($themes)];
                            $index++;

                            // Determine status UI dynamically based on inventory quantity
                            if ($qty == 0) {
                                // Out of Stock Theme Overlay
                                $border_color = 'red-300';
                                $icon_bg = 'bg-red-50';
                                $icon_text = 'text-red-500';
                                $icon_hover_bg = 'group-hover:bg-red-500';
                                $type_text_color = 'text-red-500';
                                
                                $badge_bg = 'bg-red-50 text-red-500';
                                $badge_dot = 'bg-red-500';
                                $badge_text = 'Out of Stock';
                            } elseif ($qty <= 5) {
                                // Low Stock (Warning) Theme Overlay
                                $border_color = 'yellow-300';
                                $icon_bg = 'bg-yellow-50';
                                $icon_text = 'text-yellow-600';
                                $icon_hover_bg = 'group-hover:bg-yellow-500';
                                $type_text_color = 'text-yellow-600';
                                
                                $badge_bg = 'bg-yellow-50 text-yellow-600';
                                $badge_dot = 'bg-yellow-500';
                                $badge_text = 'Low Stock (' . $qty . ')';
                            } else {
                                // Healthy Stock Theme
                                $border_color = $theme['border'] . '-300';
                                $icon_bg = $theme['bg'];
                                $icon_text = $theme['text'];
                                $icon_hover_bg = $theme['hover_bg'];
                                $type_text_color = $theme['text'];
                                
                                $badge_bg = 'bg-green-50 text-green-600';
                                $badge_dot = 'bg-green-500';
                                $badge_text = 'In Stock';
                            }

                            $part_name = htmlspecialchars($part['part_name']);
                            $description = htmlspecialchars($part['description']);
                            $price = number_format($part['part_price'], 2);
                    ?>
                    <article class="part-card group rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-<?php echo $border_color; ?> hover:shadow-md" data-name="<?php echo strtolower($part_name); ?>">
                        <div class="flex items-start justify-between">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl <?php echo $icon_bg; ?> <?php echo $icon_text; ?> transition <?php echo $icon_hover_bg; ?> group-hover:text-white">
                                <i class="<?php echo $theme['icon']; ?> text-xl"></i>
                            </div>
                            
                            <span class="inline-flex items-center gap-1.5 rounded-full <?php echo $badge_bg; ?> px-2.5 py-1 text-[10px] font-semibold">
                                <span class="h-1.5 w-1.5 rounded-full <?php echo $badge_dot; ?>"></span>
                                <?php echo $badge_text; ?>
                            </span>
                        </div>
                        <div class="mt-4">
                            <p class="text-[10px] font-semibold uppercase tracking-wider <?php echo $type_text_color; ?>">Auto Part</p>
                            <h3 class="mt-1 text-sm font-bold text-gray-900 line-clamp-1"><?php echo $part_name; ?></h3>
                            <p class="mt-2 min-h-10 text-[11px] leading-relaxed text-gray-500 line-clamp-3"><?php echo $description; ?></p>
                        </div>
                        <div class="mt-4 flex items-center justify-between border-t border-gray-100 pt-3">
                            <div>
                                <p class="text-[10px] text-gray-400">Unit Price</p>
                                <p class="mt-0.5 text-lg font-bold text-gray-900">Rs. <?php echo $price; ?></p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-gray-400">Quantity</p>
                                <p class="mt-0.5 text-lg font-semibold <?php echo $qty == 0 ? 'text-red-500' : 'text-gray-700'; ?>"><?php echo $qty; ?> <span class="text-[10px] font-normal text-gray-400">units</span></p>
                            </div>
                        </div>
                    </article>
                    <?php
                        endwhile;
                        $all_parts->free();
                    else:
                    ?>
                    <div class="col-span-full py-12 text-center">
                        <div class="flex h-16 w-16 mx-auto items-center justify-center rounded-full bg-gray-100">
                            <i class="fa-solid fa-box-open text-2xl text-gray-400"></i>
                        </div>
                        <p class="mt-4 text-sm font-semibold text-gray-700">No Parts Found</p>
                        <p class="mt-1 text-xs text-gray-500">There are no spare parts in the inventory yet.</p>
                    </div>
                    <?php endif; ?>

                </div>

                <!-- Pagination (Static Design matching Services) -->
                <div class="flex flex-col gap-3 border-t border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-[11px] text-gray-500">
                        Showing <span class="font-semibold text-gray-800">1–12</span> of <span class="font-semibold text-gray-800"><?php echo $total_parts; ?></span> parts
                    </p>

                    <div class="flex items-center gap-1.5">
                        <button disabled class="flex h-8 items-center gap-1 rounded-md border border-gray-200 px-3 text-[11px] font-medium text-gray-400 disabled:cursor-not-allowed">
                            <i class="fa-solid fa-chevron-left text-[9px]"></i>
                            Previous
                        </button>
                        <button class="flex h-8 w-8 items-center justify-center rounded-md bg-[#E89000] text-xs font-semibold text-white">1</button>
                        <button disabled class="flex h-8 items-center gap-1 rounded-md border border-gray-200 px-3 text-[11px] font-medium text-gray-400 disabled:cursor-not-allowed">
                            Next
                            <i class="fa-solid fa-chevron-right text-[9px]"></i>
                        </button>
                    </div>
                </div>

            </section>

        </div>
    </main>
</div>

<script>
    // Live search filtering by part name
    document.getElementById('inventorySearch').addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        const cards = document.querySelectorAll('#inventoryCards .part-card');
        let visibleCount = 0;

        cards.forEach(function(card) {
            const name = card.getAttribute('data-name') || '';
            if (name.includes(query)) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Show/hide the no-results message
        let noResults = document.getElementById('noSearchResults');
        if (visibleCount === 0 && query !== '') {
            if (!noResults) {
                noResults = document.createElement('div');
                noResults.id = 'noSearchResults';
                noResults.className = 'col-span-full py-12 text-center';
                noResults.innerHTML = '<div class="flex h-16 w-16 mx-auto items-center justify-center rounded-full bg-gray-100"><i class="fa-solid fa-magnifying-glass text-2xl text-gray-400"></i></div><p class="mt-4 text-sm font-semibold text-gray-700">No Matching Parts</p><p class="mt-1 text-xs text-gray-500">No spare parts match your search query.</p>';
                document.getElementById('inventoryCards').appendChild(noResults);
            }
            noResults.style.display = '';
        } else if (noResults) {
            noResults.style.display = 'none';
        }
    });
</script>

</body>
</html>