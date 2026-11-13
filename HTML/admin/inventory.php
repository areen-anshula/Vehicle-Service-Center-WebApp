<?php
$required_role = "admin";
require_once "../../PHP/auth_guard.php";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

function e($value)
{
    return htmlspecialchars((string) ($value ?? ""), ENT_QUOTES, "UTF-8");
}

function money($value)
{
    return number_format((float) $value, 2);
}

try {
    $conn = new mysqli("localhost", "root", "", "autopulse");
    $conn->set_charset("utf8mb4");

    // Inventory statistics. Low stock means quantity 1-5.
    $statsSql = "
        SELECT
            COUNT(*) AS total_parts,
            SUM(CASE WHEN quantity > 5 THEN 1 ELSE 0 END) AS in_stock,
            SUM(CASE WHEN quantity BETWEEN 1 AND 5 THEN 1 ELSE 0 END) AS low_stock,
            SUM(CASE WHEN quantity = 0 THEN 1 ELSE 0 END) AS out_of_stock
        FROM spare_parts
    ";
    $stats = $conn->query($statsSql)->fetch_assoc();

    $totalParts = (int) ($stats["total_parts"] ?? 0);
    $inStock = (int) ($stats["in_stock"] ?? 0);
    $lowStock = (int) ($stats["low_stock"] ?? 0);
    $outOfStock = (int) ($stats["out_of_stock"] ?? 0);

    // Search and stock-status filters.
    $search = trim($_GET["search"] ?? "");
    $stockFilter = $_GET["stock"] ?? "all";
    $allowedFilters = ["all", "in", "low", "out"];
    if (!in_array($stockFilter, $allowedFilters, true)) {
        $stockFilter = "all";
    }

    $where = [];
    $types = "";
    $params = [];

    if ($search !== "") {
        $where[] = "(part_name LIKE ? OR description LIKE ?)";
        $like = "%" . $search . "%";
        $types .= "ss";
        $params[] = $like;
        $params[] = $like;
    }

    if ($stockFilter === "in") {
        $where[] = "quantity > 5";
    } elseif ($stockFilter === "low") {
        $where[] = "quantity BETWEEN 1 AND 5";
    } elseif ($stockFilter === "out") {
        $where[] = "quantity = 0";
    }

    $whereSql = $where ? "WHERE " . implode(" AND ", $where) : "";

    $countSql = "SELECT COUNT(*) AS filtered_count FROM spare_parts $whereSql";
    $countStmt = $conn->prepare($countSql);
    if ($search !== "") {
        $searchLike = "%" . $search . "%";
        $countStmt->bind_param("ss", $searchLike, $searchLike);
    }
    $countStmt->execute();
    $filteredCount = (int) $countStmt->get_result()->fetch_assoc()["filtered_count"];
    $countStmt->close();

    // Pagination: 12 records per page.
    $perPage = 12;
    $totalPages = max(1, (int) ceil($filteredCount / $perPage));
    $currentPage = filter_input(INPUT_GET, "page", FILTER_VALIDATE_INT);
    $currentPage = max(1, min($totalPages, $currentPage ?: 1));
    $offset = ($currentPage - 1) * $perPage;

    $partsSql = "
        SELECT id, part_name, description, part_price, quantity
        FROM spare_parts
        $whereSql
        ORDER BY id DESC
        LIMIT ? OFFSET ?
    ";
    $partsStmt = $conn->prepare($partsSql);
    if ($search !== "") {
        $searchLike = "%" . $search . "%";
        $partsStmt->bind_param("ssii", $searchLike, $searchLike, $perPage, $offset);
    } else {
        $partsStmt->bind_param("ii", $perPage, $offset);
    }
    $partsStmt->execute();
    $partsResult = $partsStmt->get_result();

} catch (mysqli_sql_exception $ex) {
    error_log("Inventory page error: " . $ex->getMessage());
    http_response_code(500);
    exit("Unable to load spare-parts inventory. Please check the database connection and spare_parts table.");
}

$startRecord = $filteredCount > 0 ? $offset + 1 : 0;
$endRecord = min($offset + $perPage, $filteredCount);
$csrfToken = $_SESSION["csrf_token"];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoPulse | Spare Parts Management</title>
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
                    <li><a href="dashboard.php"
                            class="group flex items-center justify-between rounded-lg px-4 py-3 text-xs font-medium text-gray-300 transition hover:bg-white/10 hover:text-white"><span
                                class="flex items-center gap-3"><i
                                    class="fa-solid fa-table-columns w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>Dashboard</span><i
                                class="fa-solid fa-chevron-right text-[9px] text-gray-500"></i></a></li>
                    <li><a href="userManagement.php"
                            class="group flex items-center justify-between rounded-lg px-4 py-3 text-xs font-medium text-gray-300 transition hover:bg-white/10 hover:text-white"><span
                                class="flex items-center gap-3"><i
                                    class="fa-solid fa-users w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>User
                                Management</span><i class="fa-solid fa-chevron-right text-[9px] text-gray-500"></i></a>
                    </li>
                    <li><a href="services.php"
                            class="group flex items-center justify-between rounded-lg px-4 py-3 text-xs font-medium text-gray-300 transition hover:bg-white/10 hover:text-white"><span
                                class="flex items-center gap-3"><i
                                    class="fa-solid fa-screwdriver-wrench w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>Services</span><i
                                class="fa-solid fa-chevron-right text-[9px] text-gray-500"></i></a></li>
                    <li><a href="inventory.php"
                            class="flex items-center justify-between rounded-lg bg-[#E89000] px-4 py-3 text-xs font-semibold text-white shadow-sm"><span
                                class="flex items-center gap-3"><i
                                    class="fa-solid fa-boxes-stacked w-4 text-center"></i>Inventory</span><i
                                class="fa-solid fa-chevron-right text-[9px]"></i></a></li>
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
                <div class="mt-7 border-t border-white/10 pt-5">
                    <p class="mb-3 px-4 text-[10px] font-semibold uppercase tracking-widest text-gray-500">Account</p>
                    <ul class="space-y-1">
                        <li><a href="admin-profile.html"
                                class="group flex items-center gap-3 rounded-lg px-4 py-3 text-xs font-medium text-gray-300 transition hover:bg-white/10 hover:text-white"><i
                                    class="fa-solid fa-user w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>Profile</a>
                        </li>
                        <li><a href="../../PHP/logout.php"
                                class="flex items-center gap-3 rounded-lg px-4 py-3 text-xs font-medium text-red-400 transition hover:bg-red-500/10 hover:text-red-300"><i
                                    class="fa-solid fa-right-from-bracket w-4 text-center"></i>Logout</a></li>
                    </ul>
                </div>
            </nav>
            <div class="border-t border-white/10 p-4">
                <div class="flex items-center gap-2 rounded-lg bg-white/5 p-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-orange-500/20"><i
                            class="fa-solid fa-shield-halved text-sm text-[#E89000]"></i></div>
                    <div>
                        <p class="text-[11px] font-semibold text-gray-200">Admin Panel</p>
                        <p class="text-[10px] text-gray-500">System Management</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="min-w-0 flex-1 lg:ml-64">
            <header class="sticky top-0 z-40 border-b border-gray-200 bg-white shadow-sm">
                <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 sm:text-xl">Admin Dashboard</h2>
                        <p class="mt-0.5 text-[11px] text-gray-500">Welcome back, <span
                                class="font-semibold text-[#E89000]">Admin</span></p>
                    </div>
                    <div class="flex items-center gap-3 sm:gap-5">
                        <details class="group relative">
                            <summary aria-label="Notifications"
                                class="relative flex h-9 w-9 cursor-pointer list-none items-center justify-center rounded-full bg-gray-100 transition hover:bg-orange-50 [&::-webkit-details-marker]:hidden">
                                <i
                                    class="fa-regular fa-bell text-base text-gray-700 transition group-open:text-[#E89000]"></i><span
                                    class="absolute right-0 top-0 flex h-4 w-4 items-center justify-center rounded-full border-2 border-white bg-red-500 text-[9px] font-bold text-white">3</span>
                            </summary>
                            <div
                                class="absolute right-0 top-12 z-50 w-80 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl sm:w-96">
                                <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                                    <div>
                                        <h3 class="text-sm font-bold text-gray-900">Notifications</h3>
                                        <p class="mt-0.5 text-[11px] text-gray-500">You have 3 unread notifications</p>
                                    </div><button type="button"
                                        class="cursor-pointer text-[11px] font-semibold text-[#FFA500] hover:text-[#E89000]">Mark
                                        all as read</button>
                                </div>
                                <div class="max-h-80 overflow-y-auto">
                                    <div class="flex gap-3 border-b border-gray-100 bg-orange-50/40 px-4 py-3">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-100">
                                            <i class="fa-solid fa-clipboard-check text-sm text-blue-600"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs font-semibold text-gray-800">New Job Card Assigned</p>
                                            <p class="mt-1 text-[11px] text-gray-500">A new job card has been assigned
                                                to a mechanic.</p><span class="mt-1.5 block text-[10px] text-gray-400">5
                                                minutes ago</span>
                                        </div>
                                    </div>
                                    <div class="flex gap-3 border-b border-gray-100 bg-orange-50/40 px-4 py-3">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-green-100">
                                            <i class="fa-solid fa-circle-check text-sm text-green-600"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs font-semibold text-gray-800">Service Completed</p>
                                            <p class="mt-1 text-[11px] text-gray-500">A job card has been marked as
                                                completed.</p><span class="mt-1.5 block text-[10px] text-gray-400">30
                                                minutes ago</span>
                                        </div>
                                    </div>
                                    <div class="flex gap-3 px-4 py-3">
                                        <div
                                            class="flex h-9 w-9 items-center justify-center rounded-full bg-purple-100">
                                            <i class="fa-solid fa-user-plus text-sm text-purple-600"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs font-semibold text-gray-800">New Customer Registered</p>
                                            <p class="mt-1 text-[11px] text-gray-500">A new customer has registered on
                                                AutoPulse.</p><span class="mt-1.5 block text-[10px] text-gray-400">1
                                                hour ago</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="border-t border-gray-100 p-3 text-center"><a href="#"
                                        class="text-xs font-semibold text-[#FFA500] hover:text-[#E89000]">View All
                                        Notifications <i class="fa-solid fa-arrow-right ml-1 text-[10px]"></i></a></div>
                            </div>
                        </details>
                        <div class="hidden h-8 w-px bg-gray-200 sm:block"></div>
                        <a href="admin-profile.html" class="flex items-center gap-2 sm:gap-3">
                            <div class="hidden sm:block">
                                <h4 class="text-xs font-semibold text-gray-900">Admin</h4>
                                <p class="mt-0.5 text-[10px] text-gray-500">System Administrator</p>
                            </div><i class="fa-solid fa-chevron-down hidden text-[10px] text-gray-400 sm:block"></i>
                        </a>
                    </div>
                </div>
            </header>

            <div class="space-y-6 p-4 sm:p-6 lg:p-8">
                <!-- Page Heading -->
                <section class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-100 text-[#E89000]">
                            <i class="fa-solid fa-boxes-stacked text-lg"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold tracking-tight text-gray-900">Spare Parts Management</h1>
                            <p class="mt-0.5 text-xs text-gray-500">View and manage spare parts inventory and stock
                                levels</p>
                        </div>
                    </div>
                    <div
                        class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-[11px] text-gray-500 shadow-sm">
                        <i class="fa-solid fa-house text-[#E89000]"></i><span>Dashboard</span><i
                            class="fa-solid fa-chevron-right text-[9px]"></i><span
                            class="font-semibold text-gray-800">Inventory</span>
                    </div>
                </section>

                <!-- Action messages -->
                <?php if (isset($_GET["added"])): ?>
                    <div
                        class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-xs font-medium text-green-700">
                        <i class="fa-solid fa-circle-check mr-2"></i><?php echo e($_GET["added"]); ?>
                    </div>
                <?php elseif (isset($_GET["updated"])): ?>
                    <div
                        class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-xs font-medium text-green-700">
                        <i class="fa-solid fa-circle-check mr-2"></i><?php echo e($_GET["updated"]); ?>
                    </div>
                <?php elseif (isset($_GET["deleted"])): ?>
                    <div
                        class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-xs font-medium text-green-700">
                        <i class="fa-solid fa-circle-check mr-2"></i><?php echo e($_GET["deleted"]); ?>
                    </div>
                <?php elseif (isset($_GET["error"])): ?>
                    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-xs font-medium text-red-700"><i
                            class="fa-solid fa-circle-exclamation mr-2"></i><?php echo e($_GET["error"]); ?></div>
                <?php endif; ?>

                <!-- Inventory Statistics -->
                <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <div
                        class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                        <div class="flex items-center justify-between">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-50 text-[#E89000]">
                                <i class="fa-solid fa-boxes-stacked text-lg"></i>
                            </div><span
                                class="rounded-full bg-orange-50 px-2.5 py-1 text-[10px] font-semibold text-[#E89000]">Inventory</span>
                        </div>
                        <h3 class="mt-3 text-2xl font-bold text-gray-900"><?php echo $totalParts; ?></h3>
                        <p class="mt-0.5 text-xs font-semibold text-gray-700">Total Spare Parts</p>
                        <p class="mt-1 text-[10px] text-gray-400">All spare parts registered in the system</p>
                    </div>
                    <div
                        class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                        <div class="flex items-center justify-between">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-50 text-green-600">
                                <i class="fa-solid fa-circle-check text-lg"></i>
                            </div><span
                                class="rounded-full bg-green-50 px-2.5 py-1 text-[10px] font-semibold text-green-600">Available</span>
                        </div>
                        <h3 class="mt-3 text-2xl font-bold text-gray-900"><?php echo $inStock; ?></h3>
                        <p class="mt-0.5 text-xs font-semibold text-gray-700">In Stock</p>
                        <p class="mt-1 text-[10px] text-gray-400">Parts with more than 5 units available</p>
                    </div>
                    <div
                        class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                        <div class="flex items-center justify-between">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-lg bg-yellow-50 text-yellow-600">
                                <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                            </div><span
                                class="rounded-full bg-yellow-50 px-2.5 py-1 text-[10px] font-semibold text-yellow-600">Attention</span>
                        </div>
                        <h3 class="mt-3 text-2xl font-bold text-gray-900"><?php echo $lowStock; ?></h3>
                        <p class="mt-0.5 text-xs font-semibold text-gray-700">Low Stock</p>
                        <p class="mt-1 text-[10px] text-gray-400">Parts with 1 to 5 units available</p>
                    </div>
                    <div
                        class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                        <div class="flex items-center justify-between">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-50 text-red-500"><i
                                    class="fa-solid fa-ban text-lg"></i></div><span
                                class="rounded-full bg-red-50 px-2.5 py-1 text-[10px] font-semibold text-red-500">Unavailable</span>
                        </div>
                        <h3 class="mt-3 text-2xl font-bold text-gray-900"><?php echo $outOfStock; ?></h3>
                        <p class="mt-0.5 text-xs font-semibold text-gray-700">Out of Stock</p>
                        <p class="mt-1 text-[10px] text-gray-400">Parts currently unavailable</p>
                    </div>
                </section>

                <!-- Stock Alert -->
                <?php if ($lowStock > 0 || $outOfStock > 0): ?>
                    <section
                        class="flex flex-col gap-3 rounded-xl border border-yellow-200 bg-yellow-50 p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-start gap-3">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-yellow-100 text-yellow-600">
                                <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-yellow-800">Inventory Stock Alert</h3>
                                <p class="mt-1 text-xs leading-relaxed text-yellow-700">You have <span
                                        class="font-bold"><?php echo $lowStock; ?> low-stock items</span> and <span
                                        class="font-bold"><?php echo $outOfStock; ?> out-of-stock items</span>. Consider
                                    replenishing your inventory.</p>
                            </div>
                        </div>
                        <a href="#spare-parts-list"
                            class="inline-flex h-9 shrink-0 items-center justify-center gap-2 rounded-lg border border-yellow-300 bg-white px-4 text-xs font-semibold text-yellow-700 transition hover:bg-yellow-100"><i
                                class="fa-solid fa-eye text-[10px]"></i>View Inventory</a>
                    </section>
                <?php endif; ?>

                <!-- Spare Parts List -->
                <section id="spare-parts-list"
                    class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div
                        class="flex flex-col gap-3 border-b border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-100"><i
                                    class="fa-solid fa-box-open text-lg text-[#E89000]"></i></div>
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="text-base font-bold text-gray-900">Spare Parts Inventory</h2><span
                                        class="rounded-full bg-orange-100 px-2 py-0.5 text-[10px] font-semibold text-[#E89000]"><?php echo $filteredCount; ?>
                                        Parts</span>
                                </div>
                                <p class="mt-0.5 text-xs text-gray-500">Browse spare part details, prices and stock
                                    availability</p>
                            </div>
                        </div>
                        <a href="#add-spare-part"
                            class="inline-flex h-9 cursor-pointer items-center justify-center gap-2 rounded-lg bg-[#E89000] px-4 text-xs font-semibold text-white shadow-sm transition hover:bg-orange-600"><i
                                class="fa-solid fa-plus text-[10px]"></i>Add New Spare Part</a>
                    </div>

                    <!-- Search and Filters -->
                    <form method="GET" action="inventory.php"
                        class="flex flex-col gap-3 px-5 py-4 lg:flex-row lg:items-center">
                        <div class="relative flex-1"><i
                                class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i><input
                                type="search" name="search" value="<?php echo e($search); ?>"
                                placeholder="Search by part name or description..."
                                class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 pl-9 pr-3 text-xs text-gray-700 outline-none transition placeholder:text-gray-400 focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                        </div>
                        <div class="flex flex-col gap-2 sm:flex-row">
                            <select name="stock"
                                class="h-10 rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-600 outline-none transition focus:border-[#E89000] focus:ring-2 focus:ring-orange-100">
                                <option value="all" <?php echo $stockFilter === "all" ? "selected" : ""; ?>>All Stock
                                    Statuses</option>
                                <option value="in" <?php echo $stockFilter === "in" ? "selected" : ""; ?>>In Stock
                                </option>
                                <option value="low" <?php echo $stockFilter === "low" ? "selected" : ""; ?>>Low Stock
                                </option>
                                <option value="out" <?php echo $stockFilter === "out" ? "selected" : ""; ?>>Out of Stock
                                </option>
                            </select>
                            <button type="submit"
                                class="inline-flex h-10 cursor-pointer items-center justify-center gap-2 rounded-lg bg-[#FFA500] px-4 text-xs font-semibold text-white transition hover:bg-[#E89000]"><i
                                    class="fa-solid fa-filter text-[10px]"></i>Filter</button>
                            <a href="inventory.php"
                                class="inline-flex h-10 items-center justify-center rounded-lg border border-gray-200 px-4 text-xs font-semibold text-gray-600 transition hover:bg-gray-50">Clear</a>
                        </div>
                    </form>

                    <!-- Dynamic Spare Parts Cards -->
                    <div
                        class="grid grid-cols-1 gap-4 border-t border-gray-100 bg-gray-50/50 p-5 sm:grid-cols-2 xl:grid-cols-3">
                        <?php if ($partsResult->num_rows > 0): ?>
                            <?php while ($part = $partsResult->fetch_assoc()): ?>
                                <?php
                                $qty = (int) $part["quantity"];
                                if ($qty === 0) {
                                    $status = "Out of Stock";
                                    $statusClass = "bg-red-50 text-red-500";
                                    $dotClass = "bg-red-500";
                                    $qtyClass = "text-red-500";
                                } elseif ($qty <= 5) {
                                    $status = "Low Stock";
                                    $statusClass = "bg-yellow-50 text-yellow-600";
                                    $dotClass = "bg-yellow-500";
                                    $qtyClass = "text-yellow-600";
                                } else {
                                    $status = "In Stock";
                                    $statusClass = "bg-green-50 text-green-600";
                                    $dotClass = "bg-green-500";
                                    $qtyClass = "text-green-600";
                                }
                                ?>
                                <article
                                    class="group rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-orange-300 hover:shadow-md">
                                    <div class="flex items-start justify-between">
                                        <div
                                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-50 text-[#E89000] transition group-hover:bg-[#E89000] group-hover:text-white">
                                            <i class="fa-solid fa-box text-xl"></i>
                                        </div>
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[10px] font-semibold <?php echo $statusClass; ?>"><span
                                                class="h-1.5 w-1.5 rounded-full <?php echo $dotClass; ?>"></span><?php echo $status; ?></span>
                                    </div>
                                    <div class="mt-4">
                                        <p class="text-[10px] font-semibold uppercase tracking-wider text-[#E89000]">Spare Part
                                        </p>
                                        <h3 class="mt-1 text-sm font-bold text-gray-900"><?php echo e($part["part_name"]); ?>
                                        </h3>
                                        <p class="mt-2 min-h-10 text-[11px] leading-relaxed text-gray-500">
                                            <?php echo e($part["description"] ?: "No description provided."); ?>
                                        </p>
                                    </div>
                                    <div class="mt-4 grid grid-cols-2 gap-3 border-t border-gray-100 pt-3">
                                        <div>
                                            <p class="text-[10px] text-gray-400">Unit Price</p>
                                            <p class="mt-0.5 text-lg font-bold text-gray-900">Rs.
                                                <?php echo money($part["part_price"]); ?>
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-[10px] text-gray-400">Available Quantity</p>
                                            <p class="mt-0.5 text-lg font-bold <?php echo $qtyClass; ?>"><?php echo $qty; ?>
                                                <span class="text-[10px] font-medium text-gray-400">units</span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex gap-2 border-t border-gray-100 pt-3">
                                        <button type="button" onclick="openEditPartModal(this)"
                                            data-id="<?php echo (int) $part["id"]; ?>"
                                            data-name="<?php echo e($part["part_name"]); ?>"
                                            data-description="<?php echo e($part["description"]); ?>"
                                            data-price="<?php echo e($part["part_price"]); ?>"
                                            data-quantity="<?php echo $qty; ?>"
                                            class="flex h-9 flex-1 cursor-pointer items-center justify-center gap-2 rounded-lg border border-orange-200 text-[11px] font-semibold text-[#E89000] transition hover:bg-[#E89000] hover:text-white"><i
                                                class="fa-solid fa-pen-to-square"></i>Edit Part</button>
                                        <form action="delete-part.php" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this spare part?');">
                                            <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">
                                            <input type="hidden" name="part_id" value="<?php echo (int) $part["id"]; ?>">
                                            <button type="submit" aria-label="Delete <?php echo e($part["part_name"]); ?>"
                                                class="flex h-9 w-10 cursor-pointer items-center justify-center rounded-lg border border-red-200 text-red-500 transition hover:bg-red-500 hover:text-white"><i
                                                    class="fa-solid fa-trash"></i></button>
                                        </form>
                                    </div>
                                </article>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div
                                class="col-span-full rounded-lg border border-dashed border-gray-300 bg-white px-5 py-12 text-center">
                                <i class="fa-solid fa-box-open text-3xl text-gray-300"></i>
                                <h3 class="mt-3 text-sm font-bold text-gray-700">No spare parts found</h3>
                                <p class="mt-1 text-xs text-gray-500">Try another search or add a new spare part to your
                                    inventory.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Dynamic Pagination -->
                    <div
                        class="flex flex-col gap-3 border-t border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-[11px] text-gray-500">Showing <span
                                class="font-semibold text-gray-800"><?php echo $startRecord; ?>–<?php echo $endRecord; ?></span>
                            of <span class="font-semibold text-gray-800"><?php echo $filteredCount; ?></span> spare
                            parts</p>
                        <?php if ($totalPages > 1): ?>
                            <div class="flex flex-wrap items-center gap-1.5">
                                <?php
                                $queryBase = ["search" => $search, "stock" => $stockFilter];
                                $previousUrl = "?" . http_build_query($queryBase + ["page" => max(1, $currentPage - 1)]);
                                $nextUrl = "?" . http_build_query($queryBase + ["page" => min($totalPages, $currentPage + 1)]);
                                ?>
                                <a href="<?php echo e($previousUrl); ?>"
                                    class="flex h-8 items-center gap-1 rounded-md border border-gray-200 px-3 text-[11px] font-medium <?php echo $currentPage <= 1 ? "pointer-events-none text-gray-300" : "text-gray-600 hover:border-orange-300 hover:text-[#E89000]"; ?>"><i
                                        class="fa-solid fa-chevron-left text-[9px]"></i>Previous</a>
                                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                                    <?php if ($p === 1 || $p === $totalPages || abs($p - $currentPage) <= 1): ?>
                                        <a href="<?php echo e("?" . http_build_query($queryBase + ["page" => $p])); ?>"
                                            class="flex h-8 w-8 items-center justify-center rounded-md text-xs font-semibold <?php echo $p === $currentPage ? "bg-[#E89000] text-white" : "border border-gray-200 text-gray-600 hover:border-orange-300 hover:text-[#E89000]"; ?>"><?php echo $p; ?></a>
                                    <?php elseif ($p === 2 && $currentPage > 3 || $p === $totalPages - 1 && $currentPage < $totalPages - 2): ?>
                                        <span class="px-1 text-xs text-gray-400">...</span>
                                    <?php endif; ?>
                                <?php endfor; ?>
                                <a href="<?php echo e($nextUrl); ?>"
                                    class="flex h-8 items-center gap-1 rounded-md border border-gray-200 px-3 text-[11px] font-medium <?php echo $currentPage >= $totalPages ? "pointer-events-none text-gray-300" : "text-gray-600 hover:border-orange-300 hover:text-[#E89000]"; ?>">Next<i
                                        class="fa-solid fa-chevron-right text-[9px]"></i></a>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- Add New Spare Part -->
                <section id="add-spare-part"
                    class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
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
                        <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div><label for="part-name" class="mb-1.5 block text-xs font-semibold text-gray-700">Part
                                    Name</label><input id="part-name" name="part_name" type="text" maxlength="150"
                                    placeholder="Enter spare part name" required
                                    class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                            </div>
                            <div><label for="part-quantity"
                                    class="mb-1.5 block text-xs font-semibold text-gray-700">Available
                                    Quantity</label><input id="part-quantity" name="quantity" type="number" min="0"
                                    step="1" placeholder="Enter available quantity" required
                                    class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                            </div>
                            <div><label for="part-price" class="mb-1.5 block text-xs font-semibold text-gray-700">Unit
                                    Price (Rs.)</label><input id="part-price" name="part_price" type="number" min="0"
                                    step="0.01" placeholder="Enter unit price" required
                                    class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                            </div>
                            <div class="md:col-span-2"><label for="part-description"
                                    class="mb-1.5 block text-xs font-semibold text-gray-700">Part
                                    Description</label><textarea id="part-description" name="description" rows="3"
                                    placeholder="Describe the spare part..."
                                    class="w-full resize-none rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100"></textarea>
                            </div>
                        </div>
                        <div
                            class="flex flex-col-reverse gap-2 border-t border-gray-100 pt-4 sm:flex-row sm:justify-end">
                            <button type="reset"
                                class="inline-flex h-10 cursor-pointer items-center justify-center gap-2 rounded-lg border border-gray-200 px-5 text-xs font-semibold text-gray-600 transition hover:bg-gray-50"><i
                                    class="fa-solid fa-rotate-left"></i>Clear Form</button>
                            <button type="submit"
                                class="inline-flex h-10 cursor-pointer items-center justify-center gap-2 rounded-lg bg-[#E89000] px-5 text-xs font-semibold text-white shadow-sm transition hover:bg-orange-600"><i
                                    class="fa-solid fa-plus"></i>Add Spare Part</button>
                        </div>
                    </form>
                </section>
            </div>
        </main>
    </div>

    <!-- EDIT SPARE PART MODAL -->
    <div id="editPartModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-gray-950/60 p-4">
        <div class="max-h-[90vh] w-full max-w-xl overflow-y-auto rounded-xl bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Edit Spare Part</h2>
                    <p class="mt-1 text-xs text-gray-500">Update spare-part details and stock quantity</p>
                </div><button type="button" onclick="closeEditPartModal()"
                    class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-700"><i
                        class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="edit-part.php" method="POST" class="space-y-4 p-5">
                <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">
                <input type="hidden" name="part_id" id="edit-part-id">
                <div><label for="edit-part-name" class="mb-1.5 block text-xs font-semibold text-gray-700">Part
                        Name</label><input id="edit-part-name" name="part_name" type="text" maxlength="150" required
                        class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs outline-none focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div><label for="edit-part-price" class="mb-1.5 block text-xs font-semibold text-gray-700">Unit
                            Price (Rs.)</label><input id="edit-part-price" name="part_price" type="number" min="0"
                            step="0.01" required
                            class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs outline-none focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                    </div>
                    <div><label for="edit-part-quantity"
                            class="mb-1.5 block text-xs font-semibold text-gray-700">Available Quantity</label><input
                            id="edit-part-quantity" name="quantity" type="number" min="0" step="1" required
                            class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs outline-none focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100">
                    </div>
                </div>
                <div><label for="edit-part-description" class="mb-1.5 block text-xs font-semibold text-gray-700">Part
                        Description</label><textarea id="edit-part-description" name="description" rows="3"
                        class="w-full resize-none rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-xs outline-none focus:border-[#E89000] focus:bg-white focus:ring-2 focus:ring-orange-100"></textarea>
                </div>
                <div class="flex flex-col-reverse gap-2 border-t border-gray-100 pt-4 sm:flex-row sm:justify-end">
                    <button type="button" onclick="closeEditPartModal()"
                        class="h-10 cursor-pointer rounded-lg border border-gray-200 px-5 text-xs font-semibold text-gray-600 hover:bg-gray-50">Cancel</button><button
                        type="submit"
                        class="h-10 cursor-pointer rounded-lg bg-[#E89000] px-5 text-xs font-semibold text-white hover:bg-orange-600"><i
                            class="fa-solid fa-floppy-disk mr-1"></i>Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const editPartModal = document.getElementById("editPartModal");

        function openEditPartModal(button) {
            document.getElementById("edit-part-id").value = button.dataset.id;
            document.getElementById("edit-part-name").value = button.dataset.name;
            document.getElementById("edit-part-price").value = button.dataset.price;
            document.getElementById("edit-part-quantity").value = button.dataset.quantity;
            document.getElementById("edit-part-description").value = button.dataset.description;
            editPartModal.classList.remove("hidden");
            editPartModal.classList.add("flex");
            document.body.classList.add("overflow-hidden");
        }

        function closeEditPartModal() {
            editPartModal.classList.add("hidden");
            editPartModal.classList.remove("flex");
            document.body.classList.remove("overflow-hidden");
        }

        editPartModal.addEventListener("click", function (event) {
            if (event.target === editPartModal) closeEditPartModal();
        });
        document.addEventListener("keydown", function (event) {
            if (event.key === "Escape" && !editPartModal.classList.contains("hidden")) closeEditPartModal();
        });
    </script>
</body>

</html>
<?php
if (isset($partsStmt))
    $partsStmt->close();
if (isset($countStmt))
    $countStmt->close();
if (isset($conn) && $conn instanceof mysqli)
    $conn->close();
?>