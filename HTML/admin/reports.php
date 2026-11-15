<?php
$required_role = "admin";
require_once "../../PHP/auth_guard.php";

// Load shared admin backend if available, otherwise establish database connection
if (file_exists("../../PHP/admin_dashboard_backend.php")) {
    include "../../PHP/admin_dashboard_backend.php";
}

if (!isset($conn) || !($conn instanceof mysqli)) {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    try {
        $conn = new mysqli("localhost", "root", "", "autopulse");
        $conn->set_charset("utf8mb4");
    } catch (mysqli_sql_exception $e) {
        http_response_code(500);
        exit("Database connection failed. Please check your database configuration.");
    }
}

/* ==========================================
   1. KPI SUMMARY METRICS
========================================== */
$totalEarned = 0.00;
$monthlyEarned = 0.00;
$pendingPayments = 0.00;
$totalJobsAllTime = 0;
$finishedJobsAllTime = 0;

try {
    $revQuery = "
        SELECT
            COALESCE(SUM(CASE WHEN LOWER(TRIM(payment_status)) = 'paid' THEN final_amount ELSE 0 END), 0) AS total_earned,
            COALESCE(SUM(CASE WHEN LOWER(TRIM(payment_status)) = 'paid' AND YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE()) THEN final_amount ELSE 0 END), 0) AS monthly_earned,
            COALESCE(SUM(CASE WHEN LOWER(TRIM(payment_status)) = 'pending' THEN final_amount ELSE 0 END), 0) AS pending_payments
        FROM invoices
    ";
    $revRes = $conn->query($revQuery);
    if ($revRes && $row = $revRes->fetch_assoc()) {
        $totalEarned = (float) $row['total_earned'];
        $monthlyEarned = (float) $row['monthly_earned'];
        $pendingPayments = (float) $row['pending_payments'];
        $revRes->free();
    }
} catch (Throwable $e) {
    // Graceful fallback if invoices table is empty or unavailable
}

try {
    $jobSummaryQuery = "
        SELECT
            COUNT(*) AS total_jobs,
            SUM(CASE WHEN LOWER(TRIM(status)) IN ('completed', 'closed') THEN 1 ELSE 0 END) AS finished_jobs
        FROM job_cards
    ";
    $jobSummaryRes = $conn->query($jobSummaryQuery);
    if ($jobSummaryRes && $row = $jobSummaryRes->fetch_assoc()) {
        $totalJobsAllTime = (int) $row['total_jobs'];
        $finishedJobsAllTime = (int) $row['finished_jobs'];
        $jobSummaryRes->free();
    }
} catch (Throwable $e) {
    // Graceful fallback
}

$overallCompletionRate = $totalJobsAllTime > 0
    ? round(($finishedJobsAllTime / $totalJobsAllTime) * 100, 1)
    : 0.0;

/* ==========================================
   2. EARNED MONEY DATA (DAYS, WEEKS, MONTHS)
========================================== */

// A. Last 7 Days
$dailyMap = [];
try {
    $dailySql = "
        SELECT DATE(created_at) AS period_key, COALESCE(SUM(final_amount), 0) AS total
        FROM invoices
        WHERE LOWER(TRIM(payment_status)) = 'paid'
          AND DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
        GROUP BY DATE(created_at)
    ";
    $res = $conn->query($dailySql);
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $dailyMap[$row['period_key']] = (float) $row['total'];
        }
        $res->free();
    }
} catch (Throwable $e) {}

$daysLabels = [];
$daysValues = [];
for ($i = 6; $i >= 0; $i--) {
    $dt = new DateTime("-{$i} days");
    $key = $dt->format('Y-m-d');
    $daysLabels[] = $dt->format('d M');
    $daysValues[] = round($dailyMap[$key] ?? 0, 2);
}

// B. Last 8 Weeks (Grouped by Week Start Monday)
$weeklyMap = [];
try {
    $weeklySql = "
        SELECT DATE_SUB(DATE(created_at), INTERVAL WEEKDAY(created_at) DAY) AS week_start,
               COALESCE(SUM(final_amount), 0) AS total
        FROM invoices
        WHERE LOWER(TRIM(payment_status)) = 'paid'
          AND DATE(created_at) >= DATE_SUB(DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY), INTERVAL 7 WEEK)
        GROUP BY week_start
    ";
    $res = $conn->query($weeklySql);
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $weeklyMap[$row['week_start']] = (float) $row['total'];
        }
        $res->free();
    }
} catch (Throwable $e) {}

$weeksLabels = [];
$weeksValues = [];
$currentMonday = new DateTime('monday this week');
for ($i = 7; $i >= 0; $i--) {
    $weekDt = (clone $currentMonday)->modify("-{$i} weeks");
    $key = $weekDt->format('Y-m-d');
    $weeksLabels[] = 'Wk of ' . $weekDt->format('d M');
    $weeksValues[] = round($weeklyMap[$key] ?? 0, 2);
}

// C. Last 12 Months
$monthlyMap = [];
try {
    $monthlySql = "
        SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym,
               COALESCE(SUM(final_amount), 0) AS total
        FROM invoices
        WHERE LOWER(TRIM(payment_status)) = 'paid'
          AND created_at >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 11 MONTH), '%Y-%m-01')
        GROUP BY ym
    ";
    $res = $conn->query($monthlySql);
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $monthlyMap[$row['ym']] = (float) $row['total'];
        }
        $res->free();
    }
} catch (Throwable $e) {}

$monthsLabels = [];
$monthsValues = [];
$firstOfThisMonth = new DateTime('first day of this month');
for ($i = 11; $i >= 0; $i--) {
    $mDt = (clone $firstOfThisMonth)->modify("-{$i} months");
    $key = $mDt->format('Y-m');
    $monthsLabels[] = $mDt->format('M Y');
    $monthsValues[] = round($monthlyMap[$key] ?? 0, 2);
}

$revenueChartData = [
    'days' => [
        'labels' => $daysLabels,
        'values' => $daysValues,
        'subtitle' => 'Daily paid invoice revenue over the last 7 days'
    ],
    'weeks' => [
        'labels' => $weeksLabels,
        'values' => $weeksValues,
        'subtitle' => 'Weekly paid invoice revenue over the last 8 weeks'
    ],
    'months' => [
        'labels' => $monthsLabels,
        'values' => $monthsValues,
        'subtitle' => 'Monthly paid invoice revenue over the last 12 months'
    ]
];

/* ==========================================
   3. FINISHED JOBS PERCENTAGE COMPARING DAYS
========================================== */
$dailyJobsMap = [];
try {
    $dailyJobsSql = "
        SELECT
            DATE(created_at) AS job_date,
            COUNT(*) AS total_jobs,
            SUM(CASE WHEN LOWER(TRIM(status)) IN ('completed', 'closed') THEN 1 ELSE 0 END) AS finished_jobs
        FROM job_cards
        WHERE DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 13 DAY)
        GROUP BY DATE(created_at)
        ORDER BY job_date ASC
    ";
    $res = $conn->query($dailyJobsSql);
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $dailyJobsMap[$row['job_date']] = [
                'total' => (int) $row['total_jobs'],
                'finished' => (int) $row['finished_jobs']
            ];
        }
        $res->free();
    }
} catch (Throwable $e) {}

$jobComp14Labels = [];
$jobComp14Percentages = [];
$jobComp14Finished = [];
$jobComp14Totals = [];
$dailyBreakdownRows = [];

for ($i = 13; $i >= 0; $i--) {
    $dt = new DateTime("-{$i} days");
    $key = $dt->format('Y-m-d');
    $label = $dt->format('d M');

    $total = $dailyJobsMap[$key]['total'] ?? 0;
    $finished = $dailyJobsMap[$key]['finished'] ?? 0;
    $pct = $total > 0 ? round(($finished / $total) * 100, 1) : 0;

    $jobComp14Labels[] = $label;
    $jobComp14Percentages[] = $pct;
    $jobComp14Finished[] = $finished;
    $jobComp14Totals[] = $total;

    $dailyBreakdownRows[] = [
        'date_formatted' => $dt->format('D, d M Y'),
        'total_jobs' => $total,
        'finished_jobs' => $finished,
        'pending_jobs' => max(0, $total - $finished),
        'completion_rate' => $pct,
        'earned' => $dailyMap[$key] ?? 0.00
    ];
}

$jobCompletionChartData = [
    '7d' => [
        'labels' => array_slice($jobComp14Labels, -7),
        'percentages' => array_slice($jobComp14Percentages, -7),
        'finished' => array_slice($jobComp14Finished, -7),
        'totals' => array_slice($jobComp14Totals, -7),
        'subtitle' => 'Daily job completion percentage across the last 7 days'
    ],
    '14d' => [
        'labels' => $jobComp14Labels,
        'percentages' => $jobComp14Percentages,
        'finished' => $jobComp14Finished,
        'totals' => $jobComp14Totals,
        'subtitle' => 'Daily job completion percentage across the last 14 days'
    ]
];

// Reverse breakdown table so most recent day is at the top (last 7 days)
$recent7DaysBreakdown = array_reverse(array_slice($dailyBreakdownRows, -7));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoPulse | Admin Reports</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4.3.3"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
</head>

<body class="min-h-screen bg-[#F5F5F5] font-poppins text-gray-800">
    <div class="flex min-h-screen">

        <!-- SIDEBAR -->
        <aside id="adminSidebar"
            class="fixed left-0 top-0 z-50 hidden h-screen w-64 flex-col overflow-y-auto bg-gray-950 text-white shadow-xl lg:flex">
            <!-- Logo -->
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

            <!-- Navigation Label -->
            <div class="px-5 pb-2 pt-6">
                <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-500">Main Menu</p>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-3">
                <ul class="space-y-1">
                    <!-- Dashboard -->
                    <li>
                        <a href="dashboard.php"
                            class="group flex items-center justify-between rounded-lg px-4 py-3 text-xs font-medium text-gray-300 transition duration-200 hover:bg-white/10 hover:text-white">
                            <span class="flex items-center gap-3">
                                <i class="fa-solid fa-table-columns w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>
                                Dashboard
                            </span>
                            <i class="fa-solid fa-chevron-right text-[9px] text-gray-500"></i>
                        </a>
                    </li>

                    <!-- User Management -->
                    <li>
                        <a href="userManagement.php"
                            class="group flex items-center justify-between rounded-lg px-4 py-3 text-xs font-medium text-gray-300 transition duration-200 hover:bg-white/10 hover:text-white">
                            <span class="flex items-center gap-3">
                                <i class="fa-solid fa-users w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>
                                User Management
                            </span>
                            <i class="fa-solid fa-chevron-right text-[9px] text-gray-500"></i>
                        </a>
                    </li>

                    <!-- Services -->
                    <li>
                        <a href="services.php"
                            class="group flex items-center justify-between rounded-lg px-4 py-3 text-xs font-medium text-gray-300 transition duration-200 hover:bg-white/10 hover:text-white">
                            <span class="flex items-center gap-3">
                                <i class="fa-solid fa-screwdriver-wrench w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>
                                Services
                            </span>
                            <i class="fa-solid fa-chevron-right text-[9px] text-gray-500"></i>
                        </a>
                    </li>

                    <!-- Inventory -->
                    <li>
                        <a href="inventory.php"
                            class="group flex items-center justify-between rounded-lg px-4 py-3 text-xs font-medium text-gray-300 transition duration-200 hover:bg-white/10 hover:text-white">
                            <span class="flex items-center gap-3">
                                <i class="fa-solid fa-boxes-stacked w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>
                                Inventory
                            </span>
                            <i class="fa-solid fa-chevron-right text-[9px] text-gray-500"></i>
                        </a>
                    </li>

                    <!-- Reports (Active) -->
                    <li>
                        <a href="reports.php"
                            class="flex items-center gap-3 rounded-lg bg-[#E89000] px-4 py-3 text-xs font-semibold text-white shadow-sm">
                            <i class="fa-solid fa-chart-column w-4 text-center"></i>
                            Reports
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
                                <i class="fa-solid fa-user w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>
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

                    <!-- Page Title -->
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="toggleMobileSidebar()" aria-label="Toggle navigation"
                            class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 lg:hidden cursor-pointer">
                            <i class="fa-solid fa-bars text-sm"></i>
                        </button>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 sm:text-xl">Admin Reports</h2>
                            <p class="mt-0.5 text-[11px] text-gray-500">
                                Welcome back, <span class="font-semibold text-[#E89000]">
                                    <?php echo htmlspecialchars($_SESSION["full_name"] ?? 'Admin'); ?>
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Right Actions -->
                    <div class="flex items-center gap-3 sm:gap-5">

                        <!-- Notification Dropdown -->
                        <details class="group relative">
                            <summary aria-label="Notifications"
                                class="relative flex h-9 w-9 cursor-pointer list-none items-center justify-center rounded-full bg-gray-100 transition hover:bg-orange-50 [&::-webkit-details-marker]:hidden">
                                <i class="fa-regular fa-bell text-base text-gray-700 transition group-open:text-[#E89000]"></i>
                                <span
                                    class="absolute right-0 top-0 flex h-4 w-4 items-center justify-center rounded-full border-2 border-white bg-red-500 text-[9px] font-bold text-white">
                                    3
                                </span>
                            </summary>

                            <!-- Dropdown Panel -->
                            <div
                                class="absolute right-0 top-12 z-50 w-80 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl sm:w-96">
                                <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                                    <div>
                                        <h3 class="text-sm font-bold text-gray-900">Notifications</h3>
                                        <p class="mt-0.5 text-[11px] text-gray-500">You have 3 unread notifications</p>
                                    </div>
                                    <button type="button"
                                        class="text-[11px] font-semibold text-[#FFA500] hover:text-[#E89000] cursor-pointer">
                                        Mark all as read
                                    </button>
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
                                            <p class="mt-1 text-[11px] leading-relaxed text-gray-500">
                                                A new job card JC-0048 has been assigned to a mechanic.
                                            </p>
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
                                            <p class="mt-1 text-[11px] leading-relaxed text-gray-500">
                                                Job card JC-0046 has been marked as completed.
                                            </p>
                                            <span class="mt-1.5 block text-[10px] text-gray-400">30 minutes ago</span>
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

                <!-- Top Header & KPI Cards -->
                <section>
                    <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Financial &amp; Performance Reports</h2>
                            <p class="mt-0.5 text-xs text-gray-500">Track earned revenue trends and daily job completion rates</p>
                        </div>
                        <span
                            class="inline-flex w-fit items-center gap-1.5 rounded-full bg-orange-50 px-3 py-1.5 text-[10px] font-semibold text-[#E89000]">
                            <i class="fa-regular fa-calendar"></i>
                            Updated: <?php echo date('d M Y'); ?>
                        </span>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

                        <!-- Total Earned Revenue -->
                        <div
                            class="group rounded-xl border border-gray-100 border-l-4 border-l-[#E89000] bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-50 transition group-hover:bg-orange-100">
                                        <i class="fa-solid fa-sack-dollar text-xl text-[#E89000]"></i>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-medium text-gray-500">Total Earned Money</p>
                                        <h3 class="mt-1 text-xl font-bold tracking-tight text-gray-900">
                                            LKR <?php echo number_format($totalEarned, 2); ?>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 border-t border-gray-100 pt-3">
                                <p class="text-[10px] text-gray-500">All paid invoices combined</p>
                            </div>
                        </div>

                        <!-- Current Month Earnings -->
                        <div
                            class="group rounded-xl border border-gray-100 border-l-4 border-l-blue-500 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 transition group-hover:bg-blue-100">
                                        <i class="fa-solid fa-calendar-check text-xl text-blue-500"></i>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-medium text-gray-500">Earned This Month</p>
                                        <h3 class="mt-1 text-xl font-bold tracking-tight text-gray-900">
                                            LKR <?php echo number_format($monthlyEarned, 2); ?>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 border-t border-gray-100 pt-3">
                                <p class="text-[10px] text-gray-500">Revenue collected in <?php echo date('F Y'); ?></p>
                            </div>
                        </div>

                        <!-- Finished Jobs Percentage -->
                        <div
                            class="group rounded-xl border border-gray-100 border-l-4 border-l-green-500 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-50 transition group-hover:bg-green-100">
                                        <i class="fa-solid fa-chart-line text-xl text-green-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-medium text-gray-500">Finished Job Rate</p>
                                        <h3 class="mt-1 text-xl font-bold tracking-tight text-gray-900">
                                            <?php echo number_format($overallCompletionRate, 1); ?>%
                                        </h3>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 border-t border-gray-100 pt-3">
                                <p class="text-[10px] text-gray-500">
                                    <?php echo $finishedJobsAllTime; ?> of <?php echo $totalJobsAllTime; ?> total jobs completed
                                </p>
                            </div>
                        </div>

                        <!-- Pending Invoice Receivables -->
                        <div
                            class="group rounded-xl border border-gray-100 border-l-4 border-l-amber-500 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 transition group-hover:bg-amber-100">
                                        <i class="fa-solid fa-file-invoice-dollar text-xl text-amber-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-medium text-gray-500">Unpaid Invoices</p>
                                        <h3 class="mt-1 text-xl font-bold tracking-tight text-gray-900">
                                            LKR <?php echo number_format($pendingPayments, 2); ?>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 border-t border-gray-100 pt-3">
                                <p class="text-[10px] text-gray-500">Invoices currently in pending status</p>
                            </div>
                        </div>

                    </div>
                </section>

                <!-- ANALYTICS CHARTS GRID -->
                <section class="grid grid-cols-1 gap-6 xl:grid-cols-2">

                    <!-- CHART 1: EARNED MONEY BAR GRAPH -->
                    <div class="flex flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div
                            class="flex flex-col gap-3 border-b border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-100">
                                    <i class="fa-solid fa-chart-column text-lg text-[#E89000]"></i>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-gray-900">Earned Money Overview</h3>
                                    <p id="revenueSubtitle" class="mt-0.5 text-xs text-gray-500">
                                        Daily paid invoice revenue over the last 7 days
                                    </p>
                                </div>
                            </div>

                            <!-- X-Axis Switcher: Days / Weeks / Months -->
                            <div class="inline-flex rounded-lg border border-gray-200 bg-gray-100 p-1" role="group"
                                aria-label="Revenue timeframe">
                                <button type="button" data-rev-range="days"
                                    class="rev-toggle-btn rounded-md bg-[#E89000] px-3 py-1.5 text-[11px] font-semibold text-white shadow-xs transition cursor-pointer">
                                    Days
                                </button>
                                <button type="button" data-rev-range="weeks"
                                    class="rev-toggle-btn rounded-md px-3 py-1.5 text-[11px] font-semibold text-gray-600 transition hover:text-gray-900 cursor-pointer">
                                    Weeks
                                </button>
                                <button type="button" data-rev-range="months"
                                    class="rev-toggle-btn rounded-md px-3 py-1.5 text-[11px] font-semibold text-gray-600 transition hover:text-gray-900 cursor-pointer">
                                    Months
                                </button>
                            </div>
                        </div>

                        <div class="flex-1 p-5">
                            <div class="relative h-80 w-full">
                                <canvas id="earnedMoneyChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- CHART 2: FINISHED JOB PERCENTAGE COMPARING DAYS -->
                    <div class="flex flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div
                            class="flex flex-col gap-3 border-b border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-green-100">
                                    <i class="fa-solid fa-percent text-lg text-green-600"></i>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-gray-900">Finished Jobs Rate by Day</h3>
                                    <p id="completionSubtitle" class="mt-0.5 text-xs text-gray-500">
                                        Daily job completion percentage across the last 7 days
                                    </p>
                                </div>
                            </div>

                            <!-- Day Comparison Switcher: 7 Days / 14 Days -->
                            <div class="inline-flex rounded-lg border border-gray-200 bg-gray-100 p-1" role="group"
                                aria-label="Job comparison range">
                                <button type="button" data-job-range="7d"
                                    class="job-toggle-btn rounded-md bg-green-600 px-3 py-1.5 text-[11px] font-semibold text-white shadow-xs transition cursor-pointer">
                                    7 Days
                                </button>
                                <button type="button" data-job-range="14d"
                                    class="job-toggle-btn rounded-md px-3 py-1.5 text-[11px] font-semibold text-gray-600 transition hover:text-gray-900 cursor-pointer">
                                    14 Days
                                </button>
                            </div>
                        </div>

                        <div class="flex-1 p-5">
                            <div class="relative h-80 w-full">
                                <canvas id="finishedJobsChart"></canvas>
                            </div>
                        </div>
                    </div>

                </section>

                <!-- DAILY SUMMARY BREAKDOWN TABLE -->
                <section class="mt-6">
                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-100">
                                    <i class="fa-solid fa-table-list text-lg text-[#E89000]"></i>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-gray-900">Last 7 Days Performance Summary</h3>
                                    <p class="mt-0.5 text-xs text-gray-500">Daily breakdown of completed jobs and collected payments</p>
                                </div>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[750px] text-left">
                                <thead>
                                    <tr class="border-b border-gray-100 bg-gray-50">
                                        <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">Date</th>
                                        <th class="px-5 py-3 text-center text-[10px] font-bold uppercase tracking-wider text-gray-500">Total Jobs</th>
                                        <th class="px-5 py-3 text-center text-[10px] font-bold uppercase tracking-wider text-gray-500">Finished Jobs</th>
                                        <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">Completion Rate</th>
                                        <th class="px-5 py-3 text-right text-[10px] font-bold uppercase tracking-wider text-gray-500">Earned Money</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-xs text-gray-600">
                                    <?php foreach ($recent7DaysBreakdown as $dayRow): ?>
                                        <tr class="transition hover:bg-orange-50/30">
                                            <td class="px-5 py-3.5 font-semibold text-gray-900">
                                                <?php echo htmlspecialchars($dayRow['date_formatted']); ?>
                                            </td>
                                            <td class="px-5 py-3.5 text-center font-medium text-gray-700">
                                                <?php echo $dayRow['total_jobs']; ?>
                                            </td>
                                            <td class="px-5 py-3.5 text-center font-semibold text-green-600">
                                                <?php echo $dayRow['finished_jobs']; ?>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <div class="flex items-center gap-3">
                                                    <div class="h-2 w-28 overflow-hidden rounded-full bg-gray-100">
                                                        <div class="h-full rounded-full bg-green-500 transition-all duration-300"
                                                            style="width: <?php echo min(100, max(0, $dayRow['completion_rate'])); ?>%"></div>
                                                    </div>
                                                    <span class="font-semibold text-gray-800">
                                                        <?php echo number_format($dayRow['completion_rate'], 1); ?>%
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-5 py-3.5 text-right font-bold text-gray-900">
                                                LKR <?php echo number_format($dayRow['earned'], 2); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

            </div>
        </main>
    </div>

    <script>
        // Mobile sidebar toggle
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            if (!sidebar) return;
            sidebar.classList.toggle('hidden');
            sidebar.classList.toggle('flex');
        }

        // Database-fed chart datasets from PHP
        const revenueDatasets = <?php echo json_encode($revenueChartData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
        const jobCompletionDatasets = <?php echo json_encode($jobCompletionChartData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

        Chart.defaults.font.family = "'Poppins', sans-serif";
        Chart.defaults.color = '#6B7280';

        // ------------------------------------------
        // 1. EARNED MONEY BAR CHART
        // ------------------------------------------
        const earnedCtx = document.getElementById('earnedMoneyChart').getContext('2d');
        const earnedMoneyChart = new Chart(earnedCtx, {
            type: 'bar',
            data: {
                labels: revenueDatasets.days.labels,
                datasets: [{
                    label: 'Earned Money (LKR)',
                    data: revenueDatasets.days.values,
                    backgroundColor: '#E89000',
                    hoverBackgroundColor: '#D97706',
                    borderRadius: 6,
                    borderSkipped: false,
                    maxBarThickness: 42
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#111827',
                        padding: 12,
                        titleFont: { size: 12, weight: '600' },
                        bodyFont: { size: 12 },
                        callbacks: {
                            label: function (context) {
                                const val = Number(context.parsed.y || 0);
                                return ' Earned: LKR ' + val.toLocaleString('en-US', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    },
                    y: {
                        beginAtZero: true,
                        border: { display: false },
                        grid: { color: '#F3F4F6' },
                        ticks: {
                            font: { size: 11 },
                            callback: function (value) {
                                return 'LKR ' + Number(value).toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Switch X-Axis between Days, Weeks, and Months
        const revToggleButtons = document.querySelectorAll('.rev-toggle-btn');
        const revenueSubtitle = document.getElementById('revenueSubtitle');

        revToggleButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const range = btn.getAttribute('data-rev-range');
                const selectedData = revenueDatasets[range];
                if (!selectedData) return;

                // Update active button styles
                revToggleButtons.forEach(b => {
                    b.classList.remove('bg-[#E89000]', 'text-white', 'shadow-xs');
                    b.classList.add('text-gray-600', 'hover:text-gray-900');
                });
                btn.classList.add('bg-[#E89000]', 'text-white', 'shadow-xs');
                btn.classList.remove('text-gray-600', 'hover:text-gray-900');

                // Update chart & subtitle
                revenueSubtitle.textContent = selectedData.subtitle;
                earnedMoneyChart.data.labels = selectedData.labels;
                earnedMoneyChart.data.datasets[0].data = selectedData.values;
                earnedMoneyChart.update();
            });
        });

        // ------------------------------------------
        // 2. FINISHED JOBS PERCENTAGE BY DAY CHART
        // ------------------------------------------
        const jobsCtx = document.getElementById('finishedJobsChart').getContext('2d');
        const greenGradient = jobsCtx.createLinearGradient(0, 0, 0, 300);
        greenGradient.addColorStop(0, 'rgba(22, 163, 74, 0.25)');
        greenGradient.addColorStop(1, 'rgba(22, 163, 74, 0.0)');

        let activeJobRange = '7d';

        const finishedJobsChart = new Chart(jobsCtx, {
            type: 'line',
            data: {
                labels: jobCompletionDatasets['7d'].labels,
                datasets: [{
                    label: 'Finished Jobs (%)',
                    data: jobCompletionDatasets['7d'].percentages,
                    borderColor: '#16A34A',
                    backgroundColor: greenGradient,
                    borderWidth: 2.5,
                    pointBackgroundColor: '#16A34A',
                    pointBorderColor: '#FFFFFF',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.35
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#111827',
                        padding: 12,
                        titleFont: { size: 12, weight: '600' },
                        bodyFont: { size: 12 },
                        callbacks: {
                            label: function (context) {
                                const idx = context.dataIndex;
                                const ds = jobCompletionDatasets[activeJobRange];
                                const pct = ds.percentages[idx] ?? 0;
                                const fin = ds.finished[idx] ?? 0;
                                const tot = ds.totals[idx] ?? 0;
                                return [
                                    ` Completion Rate: ${pct}%`,
                                    ` Finished Jobs: ${fin} of ${tot} jobs`
                                ];
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    },
                    y: {
                        beginAtZero: true,
                        min: 0,
                        max: 100,
                        border: { display: false },
                        grid: { color: '#F3F4F6' },
                        ticks: {
                            stepSize: 20,
                            font: { size: 11 },
                            callback: function (value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });

        // Switch between 7 Days and 14 Days comparison
        const jobToggleButtons = document.querySelectorAll('.job-toggle-btn');
        const completionSubtitle = document.getElementById('completionSubtitle');

        jobToggleButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const range = btn.getAttribute('data-job-range');
                const selectedData = jobCompletionDatasets[range];
                if (!selectedData) return;

                activeJobRange = range;

                jobToggleButtons.forEach(b => {
                    b.classList.remove('bg-green-600', 'text-white', 'shadow-xs');
                    b.classList.add('text-gray-600', 'hover:text-gray-900');
                });
                btn.classList.add('bg-green-600', 'text-white', 'shadow-xs');
                btn.classList.remove('text-gray-600', 'hover:text-gray-900');

                completionSubtitle.textContent = selectedData.subtitle;
                finishedJobsChart.data.labels = selectedData.labels;
                finishedJobsChart.data.datasets[0].data = selectedData.percentages;
                finishedJobsChart.update();
            });
        });
    </script>
</body>

</html>