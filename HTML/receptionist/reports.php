<?php
$required_role = "receptionist";
require_once "../../PHP/auth_guard.php";

/*
 * AutoPulse database connection.
 */
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli("localhost", "root", "", "autopulse");
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    exit("Database connection failed. Check the database credentials.");
}

// Fetch all invoices
$releasedInvoices = [];
$paidInvoices = [];
$invoiceIds = [];
$jobCardIds = [];
$invoicesRaw = [];

$sql = "
    SELECT 
        i.*, 
        jc.appointment_id, 
        u.full_name, u.phone, u.email,
        a.vehicle_registration_number, a.brand, a.model
    FROM invoices i
    JOIN job_cards jc ON i.job_card_id = jc.id
    JOIN appointment a ON jc.appointment_id = a.id
    JOIN users u ON a.user_id = u.id
    ORDER BY i.created_at DESC
";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $invoicesRaw[] = $row;
        $invoiceIds[] = $row['id'];
        $jobCardIds[] = $row['job_card_id'];
    }
}

// Fetch parts and services for the fetched job cards to populate PDF details
$partsMap = [];
$servicesMap = [];

if (!empty($jobCardIds)) {
    $jcList = implode(',', $jobCardIds);
    
    // Get parts
    $pRes = $conn->query("
        SELECT jcp.job_card_id, sp.part_name, jcp.quantity, jcp.unit_price 
        FROM job_card_parts jcp 
        JOIN spare_parts sp ON jcp.spare_part_id = sp.id 
        WHERE jcp.job_card_id IN ($jcList)
    ");
    while ($p = $pRes->fetch_assoc()) {
        $partsMap[$p['job_card_id']][] = $p;
    }
    
    // Get services
    $sRes = $conn->query("
        SELECT jcs.job_card_id, s.service_name, jcs.service_price 
        FROM job_card_services jcs 
        JOIN services s ON jcs.service_id = s.id 
        WHERE jcs.job_card_id IN ($jcList)
    ");
    while ($s = $sRes->fetch_assoc()) {
        $servicesMap[$s['job_card_id']][] = $s;
    }
}

// Build map for frontend JS to render PDFs
$invoiceDetailsMap = [];

foreach ($invoicesRaw as $row) {
    $status = strtolower($row['payment_status']);
    
    if ($status === 'pending') {
        $releasedInvoices[] = $row;
    } elseif ($status === 'paid') {
        $paidInvoices[] = $row;
    }

    $invoiceDetailsMap[$row['id']] = [
        'id' => $row['id'],
        'job_card_id' => $row['job_card_id'],
        'date' => date('F j, Y', strtotime($row['created_at'])),
        'customer_name' => $row['full_name'],
        'customer_phone' => $row['phone'],
        'customer_email' => $row['email'],
        'vehicle' => $row['brand'] . ' ' . $row['model'],
        'vehicle_reg' => $row['vehicle_registration_number'],
        'subtotal' => (float)$row['subtotal'],
        'discount' => (float)$row['discount'],
        'final_amount' => (float)$row['final_amount'],
        'status' => ucfirst($row['payment_status']),
        'parts' => $partsMap[$row['job_card_id']] ?? [],
        'services' => $servicesMap[$row['job_card_id']] ?? []
    ];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoPulse | Reports & Invoices</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4.3.3"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css">
    
    <!-- html2pdf for generating real-world PDF structured invoices -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
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

            <div class="px-5 pb-2 pt-6">
                <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-500">Main Menu</p>
            </div>

            <nav class="flex-1 px-3">
                <ul class="space-y-1">
                    <li>
                        <a href="dashboard.php" class="group flex items-center justify-between rounded-lg px-4 py-3 text-xs font-medium text-gray-300 transition duration-200 hover:bg-white/10 hover:text-white">
                            <span class="flex items-center gap-3">
                                <i class="fa-solid fa-table-columns w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>
                                Dashboard
                            </span>
                            <i class="fa-solid fa-chevron-right text-[9px] text-gray-500"></i>
                        </a>
                    </li>
                    <li>
                        <a href="user_management.php" class="group flex items-center justify-between rounded-lg px-4 py-3 text-xs font-medium text-gray-300 transition duration-200 hover:bg-white/10 hover:text-white">
                            <span class="flex items-center gap-3">
                                <i class="fa-solid fa-users w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>
                                User Management
                            </span>
                            <i class="fa-solid fa-chevron-right text-[9px] text-gray-500"></i>
                        </a>
                    </li>
                    <li>
                        <a href="services.php" class="group flex items-center justify-between rounded-lg px-4 py-3 text-xs font-medium text-gray-300 transition duration-200 hover:bg-white/10 hover:text-white">
                            <span class="flex items-center gap-3">
                                <i class="fa-solid fa-screwdriver-wrench w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>
                                Services
                            </span>
                            <i class="fa-solid fa-chevron-right text-[9px] text-gray-500"></i>
                        </a>
                    </li>
                    <li>
                        <a href="inventory.php" class="group flex items-center justify-between rounded-lg px-4 py-3 text-xs font-medium text-gray-300 transition duration-200 hover:bg-white/10 hover:text-white">
                            <span class="flex items-center gap-3">
                                <i class="fa-solid fa-boxes-stacked w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>
                                Inventory
                            </span>
                            <i class="fa-solid fa-chevron-right text-[9px] text-gray-500"></i>
                        </a>
                    </li>
                    <!-- ACTIVE LINK: Reports -->
                    <li>
                        <a href="reports.php" class="flex items-center gap-3 rounded-lg bg-[#E89000] px-4 py-3 text-xs font-semibold text-white shadow-sm">
                            <i class="fa-solid fa-chart-column w-4 text-center"></i>
                            Reports
                        </a>
                    </li>
                </ul>

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
                            <a href="../../PHP/logout.php" class="flex items-center gap-3 rounded-lg px-4 py-3 text-xs font-medium text-red-400 transition hover:bg-red-500/10 hover:text-red-300">
                                <i class="fa-solid fa-right-from-bracket w-4 text-center"></i>
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

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
                        <h2 class="text-lg font-bold text-gray-900 sm:text-xl">Reports & Invoices</h2>
                        <p class="mt-0.5 text-[11px] text-gray-500">
                            Manage billing, download records, and oversee financials
                        </p>
                    </div>

                    <div class="flex items-center gap-3 sm:gap-5">
                        <!-- Notifications (Static mapping from dashboard) -->
                        <details class="group relative">
                            <summary aria-label="Notifications" class="relative flex h-9 w-9 cursor-pointer list-none items-center justify-center rounded-full bg-gray-100 transition hover:bg-orange-50 [&::-webkit-details-marker]:hidden">
                                <i class="fa-regular fa-bell text-base text-gray-700 transition group-open:text-[#E89000]"></i>
                                <span class="absolute right-0 top-0 flex h-4 w-4 items-center justify-center rounded-full border-2 border-white bg-red-500 text-[9px] font-bold text-white">3</span>
                            </summary>
                            <div class="absolute right-0 top-12 z-50 w-80 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl sm:w-96">
                                <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                                    <div>
                                        <h3 class="text-sm font-bold text-gray-900">Notifications</h3>
                                    </div>
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

            <!-- PAGE CONTENT -->
            <div class="space-y-8 p-4 sm:p-6 lg:p-8">

                <!-- 1. RELEASED INVOICES (Pending) -->
                <section>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-100 text-[#E89000]">
                            <i class="fa-solid fa-file-invoice text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Released Invoices</h2>
                            <p class="text-xs text-gray-500">Saved invoices awaiting payment by the client</p>
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-gray-50 text-xs font-bold uppercase tracking-wider text-gray-600">
                                    <tr>
                                        <th class="px-5 py-4">Invoice #</th>
                                        <th class="px-5 py-4">Customer Details</th>
                                        <th class="px-5 py-4">Vehicle</th>
                                        <th class="px-5 py-4">Date Generated</th>
                                        <th class="px-5 py-4 text-right">Amount (LKR)</th>
                                        <th class="px-5 py-4 text-center">Status</th>
                                        <th class="px-5 py-4 text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <?php if (!empty($releasedInvoices)): ?>
                                        <?php foreach ($releasedInvoices as $inv): ?>
                                            <tr class="transition hover:bg-gray-50">
                                                <td class="px-5 py-4">
                                                    <span class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-bold text-gray-700">INV-<?= htmlspecialchars($inv['id']) ?></span>
                                                    <div class="mt-1 text-[10px] text-gray-400">JC-<?= htmlspecialchars($inv['job_card_id']) ?></div>
                                                </td>
                                                <td class="px-5 py-4">
                                                    <p class="text-xs font-semibold text-gray-900"><?= htmlspecialchars($inv['full_name']) ?></p>
                                                    <p class="mt-1 text-[11px] text-gray-500"><?= htmlspecialchars($inv['phone']) ?></p>
                                                </td>
                                                <td class="px-5 py-4">
                                                    <p class="text-xs font-medium text-gray-800"><?= htmlspecialchars($inv['brand'] . ' ' . $inv['model']) ?></p>
                                                    <p class="text-[11px] text-gray-500"><?= htmlspecialchars($inv['vehicle_registration_number']) ?></p>
                                                </td>
                                                <td class="px-5 py-4 text-xs text-gray-700">
                                                    <?= date('M j, Y', strtotime($inv['created_at'])) ?>
                                                </td>
                                                <td class="px-5 py-4 text-right">
                                                    <p class="text-sm font-bold text-gray-900"><?= number_format($inv['final_amount'], 2) ?></p>
                                                </td>
                                                <td class="px-5 py-4 text-center">
                                                    <span class="rounded-full bg-orange-50 px-2.5 py-1 text-xs font-medium text-orange-600 border border-orange-200">Pending</span>
                                                </td>
                                                <td class="px-5 py-4 text-center">
                                                    <button onclick="downloadPdfInvoice(<?= (int)$inv['id'] ?>)" class="rounded-lg bg-[#E89000] px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-orange-600 active:scale-95 cursor-pointer">
                                                        <i class="fa-solid fa-download mr-1"></i> Download
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="px-5 py-12 text-center">
                                                <i class="fa-solid fa-folder-open block text-3xl text-gray-300 mb-2"></i>
                                                <p class="text-sm font-semibold text-gray-600">No Released Invoices</p>
                                                <p class="text-xs text-gray-400">There are no pending invoices to display at the moment.</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- 2. PAID INVOICES -->
                <section>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-green-100 text-green-600">
                            <i class="fa-solid fa-check-double text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Paid Invoices</h2>
                            <p class="text-xs text-gray-500">Historical records of completed transactions</p>
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-gray-50 text-xs font-bold uppercase tracking-wider text-gray-600">
                                    <tr>
                                        <th class="px-5 py-4">Invoice #</th>
                                        <th class="px-5 py-4">Customer Details</th>
                                        <th class="px-5 py-4">Vehicle</th>
                                        <th class="px-5 py-4">Date Generated</th>
                                        <th class="px-5 py-4 text-right">Amount (LKR)</th>
                                        <th class="px-5 py-4 text-center">Status</th>
                                        <th class="px-5 py-4 text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <?php if (!empty($paidInvoices)): ?>
                                        <?php foreach ($paidInvoices as $inv): ?>
                                            <tr class="transition hover:bg-gray-50">
                                                <td class="px-5 py-4">
                                                    <span class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-bold text-gray-700">INV-<?= htmlspecialchars($inv['id']) ?></span>
                                                    <div class="mt-1 text-[10px] text-gray-400">JC-<?= htmlspecialchars($inv['job_card_id']) ?></div>
                                                </td>
                                                <td class="px-5 py-4">
                                                    <p class="text-xs font-semibold text-gray-900"><?= htmlspecialchars($inv['full_name']) ?></p>
                                                    <p class="mt-1 text-[11px] text-gray-500"><?= htmlspecialchars($inv['phone']) ?></p>
                                                </td>
                                                <td class="px-5 py-4">
                                                    <p class="text-xs font-medium text-gray-800"><?= htmlspecialchars($inv['brand'] . ' ' . $inv['model']) ?></p>
                                                    <p class="text-[11px] text-gray-500"><?= htmlspecialchars($inv['vehicle_registration_number']) ?></p>
                                                </td>
                                                <td class="px-5 py-4 text-xs text-gray-700">
                                                    <?= date('M j, Y', strtotime($inv['created_at'])) ?>
                                                </td>
                                                <td class="px-5 py-4 text-right">
                                                    <p class="text-sm font-bold text-gray-900"><?= number_format($inv['final_amount'], 2) ?></p>
                                                </td>
                                                <td class="px-5 py-4 text-center">
                                                    <span class="rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-600 border border-green-200">Paid</span>
                                                </td>
                                                <td class="px-5 py-4 text-center">
                                                    <button onclick="downloadPdfInvoice(<?= (int)$inv['id'] ?>)" class="rounded-lg bg-[#E89000] px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-orange-600 active:scale-95 cursor-pointer">
                                                        <i class="fa-solid fa-download mr-1"></i> Download
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="px-5 py-12 text-center">
                                                <i class="fa-solid fa-folder-open block text-3xl text-gray-300 mb-2"></i>
                                                <p class="text-sm font-semibold text-gray-600">No Paid Invoices</p>
                                                <p class="text-xs text-gray-400">There are no completed payments to display yet.</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

            </div>
        </main>
    </div>

    <!-- 
      HIDDEN INVOICE TEMPLATE FOR PDF GENERATION 
      This is positioned completely off-screen and sized properly to match an A4 page width (800px)
    -->
    <div class="fixed top-0 left-[-9999px] w-[800px] bg-white text-gray-800 font-sans" id="invoice-print-container" style="padding: 40px; box-sizing: border-box;">
        
        <!-- Header -->
        <div class="flex justify-between items-start border-b-2 border-gray-800 pb-6 mb-6">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">Auto<span class="text-[#E89000]">Pulse</span></h1>
                <p class="mt-1 text-xs text-gray-500 uppercase tracking-widest">Vehicle Service Center</p>
                <div class="mt-4 text-sm text-gray-600 leading-relaxed">
                    123 Motorway Avenue<br>
                    Colombo 00100, Sri Lanka<br>
                    Phone: +94 77 123 4567<br>
                    Email: billing@autopulse.lk
                </div>
            </div>
            <div class="text-right">
                <h2 class="text-4xl font-black text-gray-200 uppercase tracking-widest mb-4">INVOICE</h2>
                <div class="flex justify-end gap-6 text-sm">
                    <div class="text-gray-500 font-semibold text-right">
                        <p class="mb-1">Invoice No:</p>
                        <p class="mb-1">Date:</p>
                        <p>Job Card No:</p>
                    </div>
                    <div class="text-gray-900 font-bold text-right">
                        <p class="mb-1" id="pdf_inv_number"></p>
                        <p class="mb-1" id="pdf_inv_date"></p>
                        <p id="pdf_inv_jc"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Meta Info -->
        <div class="flex justify-between mb-8">
            <div class="w-1/2 pr-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2 border-b border-gray-200 pb-1">Bill To</h3>
                <p class="text-base font-bold text-gray-900 mb-1" id="pdf_cus_name"></p>
                <p class="text-sm text-gray-600 mb-1" id="pdf_cus_phone"></p>
                <p class="text-sm text-gray-600" id="pdf_cus_email"></p>
            </div>
            <div class="w-1/2 pl-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2 border-b border-gray-200 pb-1">Vehicle Details</h3>
                <p class="text-base font-bold text-gray-900 mb-1" id="pdf_veh_name"></p>
                <p class="text-sm text-gray-600">Reg No: <span class="font-semibold text-gray-800" id="pdf_veh_reg"></span></p>
                <div class="mt-2 inline-block px-3 py-1 rounded bg-gray-100 text-xs font-bold text-gray-600 uppercase tracking-widest border border-gray-200">
                    Status: <span id="pdf_inv_status" class="text-[#E89000]"></span>
                </div>
            </div>
        </div>

        <!-- Line Items Table -->
        <table class="w-full text-left mb-6 border-collapse">
            <thead>
                <tr class="bg-gray-900 text-white text-xs uppercase tracking-widest">
                    <th class="py-3 px-4 w-12 text-center">#</th>
                    <th class="py-3 px-4">Description</th>
                    <th class="py-3 px-4 text-center">Type</th>
                    <th class="py-3 px-4 text-center">Qty</th>
                    <th class="py-3 px-4 text-right">Unit Price (LKR)</th>
                    <th class="py-3 px-4 text-right">Total (LKR)</th>
                </tr>
            </thead>
            <tbody id="pdf_table_body" class="text-sm text-gray-800">
                <!-- Dynamically populated via JS -->
            </tbody>
        </table>

        <!-- Totals -->
        <div class="flex justify-end">
            <div class="w-1/2">
                <table class="w-full text-right text-sm">
                    <tr>
                        <td class="py-2 px-4 font-semibold text-gray-600 border-b border-gray-100">Subtotal</td>
                        <td class="py-2 px-4 font-medium border-b border-gray-100" id="pdf_subtotal"></td>
                    </tr>
                    <tr>
                        <td class="py-2 px-4 font-semibold text-gray-600 border-b border-gray-100">Discount</td>
                        <td class="py-2 px-4 font-medium border-b border-gray-100 text-red-600" id="pdf_discount"></td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="py-4 px-4 font-bold text-lg text-gray-900">Grand Total</td>
                        <td class="py-4 px-4 font-black text-xl text-[#E89000]" id="pdf_final"></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-16 pt-6 border-t border-gray-200 text-center">
            <h4 class="text-sm font-bold text-gray-800 mb-1">Thank you for your business!</h4>
            <p class="text-xs text-gray-500">If you have any questions concerning this invoice, contact billing@autopulse.lk</p>
        </div>
    </div>


    <script>
        // Inject the PHP Invoice array directly into JS for fast, client-side PDF generation
        const invoiceData = <?= json_encode($invoiceDetailsMap) ?>;

        function downloadPdfInvoice(invoiceId) {
            const data = invoiceData[invoiceId];
            if (!data) {
                alert("Invoice data could not be loaded.");
                return;
            }

            // 1. Populate the hidden HTML template
            document.getElementById('pdf_inv_number').textContent = `INV-${data.id}`;
            document.getElementById('pdf_inv_date').textContent = data.date;
            document.getElementById('pdf_inv_jc').textContent = `JC-${data.job_card_id}`;
            
            document.getElementById('pdf_cus_name').textContent = data.customer_name;
            document.getElementById('pdf_cus_phone').textContent = data.customer_phone;
            document.getElementById('pdf_cus_email').textContent = data.customer_email;
            
            document.getElementById('pdf_veh_name').textContent = data.vehicle;
            document.getElementById('pdf_veh_reg').textContent = data.vehicle_reg;
            
            const statusEl = document.getElementById('pdf_inv_status');
            statusEl.textContent = data.status;
            statusEl.className = data.status.toLowerCase() === 'paid' ? 'text-green-600' : 'text-[#E89000]';

            // Clear table
            const tbody = document.getElementById('pdf_table_body');
            tbody.innerHTML = '';

            let counter = 1;
            
            // Render Spare Parts
            data.parts.forEach(part => {
                const total = (part.quantity * part.unit_price).toFixed(2);
                const tr = document.createElement('tr');
                tr.className = "border-b border-gray-200";
                tr.innerHTML = `
                    <td class="py-3 px-4 text-center text-gray-500">${counter++}</td>
                    <td class="py-3 px-4 font-medium text-gray-900">${part.part_name}</td>
                    <td class="py-3 px-4 text-center"><span class="px-2 py-1 bg-blue-50 text-blue-600 text-[10px] uppercase font-bold rounded">Part</span></td>
                    <td class="py-3 px-4 text-center">${part.quantity}</td>
                    <td class="py-3 px-4 text-right">${parseFloat(part.unit_price).toFixed(2)}</td>
                    <td class="py-3 px-4 text-right font-semibold">${total}</td>
                `;
                tbody.appendChild(tr);
            });

            // Render Services
            data.services.forEach(service => {
                const total = parseFloat(service.service_price).toFixed(2);
                const tr = document.createElement('tr');
                tr.className = "border-b border-gray-200";
                tr.innerHTML = `
                    <td class="py-3 px-4 text-center text-gray-500">${counter++}</td>
                    <td class="py-3 px-4 font-medium text-gray-900">${service.service_name}</td>
                    <td class="py-3 px-4 text-center"><span class="px-2 py-1 bg-purple-50 text-purple-600 text-[10px] uppercase font-bold rounded">Service</span></td>
                    <td class="py-3 px-4 text-center">-</td>
                    <td class="py-3 px-4 text-right">${total}</td>
                    <td class="py-3 px-4 text-right font-semibold">${total}</td>
                `;
                tbody.appendChild(tr);
            });

            // Render Totals
            document.getElementById('pdf_subtotal').textContent = data.subtotal.toFixed(2);
            document.getElementById('pdf_discount').textContent = `- ${data.discount.toFixed(2)}`;
            document.getElementById('pdf_final').textContent = `LKR ${data.final_amount.toFixed(2)}`;

            // 2. Generate PDF with html2pdf
            const element = document.getElementById('invoice-print-container');
            
            // Briefly display it (it is pushed off-screen via CSS so the user doesn't see a layout jump)
            element.classList.remove('hidden');

            const opt = {
                margin:       [0, 0, 0, 0], // margins are handled by inline CSS padding
                filename:     `AutoPulse_Invoice_INV-${data.id}.pdf`,
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true, letterRendering: true },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            // Call library
            html2pdf().set(opt).from(element).save().then(() => {
                // Return to hidden state
                element.classList.add('hidden');
            });
        }
    </script>
</body>

</html>