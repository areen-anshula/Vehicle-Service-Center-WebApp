
<?php
// Require mechanic login
$required_role = "mechanic";
require_once "../../PHP/auth_guard.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// DATABASE CONNECTION
try {
    $conn = new mysqli("localhost", "root", "", "autopulse");
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    exit("Database connection failed.");
}

// Get the currently logged-in mechanic's ID
$mechanicId = (int) $_SESSION["user_id"];

// Verify that the logged-in user is a mechanic in the database
$roleStmt = $conn->prepare("
    SELECT id
    FROM users
    WHERE id = ?
      AND LOWER(role) = 'mechanic'
    LIMIT 1
");

$roleStmt->bind_param("i", $mechanicId);
$roleStmt->execute();

if (!$roleStmt->get_result()->fetch_assoc()) {
    $roleStmt->close();
    exit("Access denied. Mechanic account required.");
}

$roleStmt->close();

// CSRF TOKEN
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION['csrf_token'];

$flashMessage = "";
$flashType = "success";

// ==========================================
// HANDLE MECHANIC JOB ACTIONS
// ==========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['job_action'])) {

    $jobCardId = filter_input(INPUT_POST, 'job_card_id', FILTER_VALIDATE_INT);
    $action = $_POST['job_action'];
    $submittedToken = $_POST['csrf_token'] ?? '';

    $returnModal = $_POST['return_modal'] ?? 'assigned';

    if (!in_array($returnModal, ['assigned', 'in-progress', 'closed'], true)) {
        $returnModal = 'assigned';
    }

    try {
        if (!$jobCardId || !hash_equals($csrfToken, $submittedToken)) {
            throw new Exception("Invalid request. Please try again.");
        }

        $conn->begin_transaction();

        // Lock and verify this job belongs to the logged-in mechanic
        $checkStmt = $conn->prepare("
            SELECT id, appointment_id, status
            FROM job_cards
            WHERE id = ?
              AND mechanic_id = ?
            FOR UPDATE
        ");

        $checkStmt->bind_param("ii", $jobCardId, $mechanicId);
        $checkStmt->execute();

        $job = $checkStmt->get_result()->fetch_assoc();
        $checkStmt->close();

        if (!$job) {
            throw new Exception("Job card not found or not assigned to your account.");
        }

        $currentStatus = strtolower(trim($job['status']));

        // START JOB: ASSIGNED -> IN PROGRESS
        if ($action === 'start') {

            if (!in_array($currentStatus, ['pending', 'assigned'], true)) {
                throw new Exception("This job cannot be started because its status has changed.");
            }

            $updateStmt = $conn->prepare("
                UPDATE job_cards
                SET status = 'In Progress'
                WHERE id = ?
                  AND mechanic_id = ?
            ");

            $updateStmt->bind_param("ii", $jobCardId, $mechanicId);
            $updateStmt->execute();
            $updateStmt->close();

            $flashMessage = "Job successfully moved to In Progress.";
            $returnModal = 'in-progress';
        }

        // REJECT JOB: REMOVE ASSIGNMENT AND RETURN APPOINTMENT TO APPROVED
        elseif ($action === 'reject') {

            if (!in_array($currentStatus, ['pending', 'assigned'], true)) {
                throw new Exception("Only assigned jobs can be rejected.");
            }

            // Remove this job-card assignment
            $deleteStmt = $conn->prepare("
                DELETE FROM job_cards
                WHERE id = ?
                  AND mechanic_id = ?
            ");

            $deleteStmt->bind_param("ii", $jobCardId, $mechanicId);
            $deleteStmt->execute();
            $deleteStmt->close();

            // Make the appointment available for reassignment
            $appointmentId = (int) $job['appointment_id'];

            $appointmentStmt = $conn->prepare("
                UPDATE appointment
                SET status = 'approved'
                WHERE id = ?
            ");

            $appointmentStmt->bind_param("i", $appointmentId);
            $appointmentStmt->execute();
            $appointmentStmt->close();

            $flashMessage = "Job assignment rejected. It is now available for reassignment.";
            $returnModal = 'assigned';
        }

        // CLOSE JOB: IN PROGRESS -> COMPLETED
        elseif ($action === 'close') {

            if ($currentStatus !== 'in progress') {
                throw new Exception("Only In Progress jobs can be closed.");
            }

            $updateStmt = $conn->prepare("
                UPDATE job_cards
                SET status = 'Completed'
                WHERE id = ?
                  AND mechanic_id = ?
            ");

            $updateStmt->bind_param("ii", $jobCardId, $mechanicId);
            $updateStmt->execute();
            $updateStmt->close();

            $flashMessage = "Job successfully closed.";
            $returnModal = 'closed';
        }

        else {
            throw new Exception("Invalid job action.");
        }

        $conn->commit();

        $_SESSION['job_flash_message'] = $flashMessage;
        $_SESSION['job_flash_type'] = 'success';

    } catch (Throwable $e) {

        $conn->rollback();

        error_log("Mechanic job action error: " . $e->getMessage());

        $_SESSION['job_flash_message'] = $e->getMessage();
        $_SESSION['job_flash_type'] = 'error';
    }

    // Prevent duplicate form submissions
    header("Location: dashboard.php?modal=" . urlencode($returnModal));
    exit;
}

// ==========================================
// FLASH MESSAGE
// ==========================================

if (isset($_SESSION['job_flash_message'])) {
    $flashMessage = $_SESSION['job_flash_message'];
    $flashType = $_SESSION['job_flash_type'] ?? 'success';

    unset($_SESSION['job_flash_message'], $_SESSION['job_flash_type']);
}

// ==========================================
// GET MECHANIC'S ASSIGNED JOBS
// Pending and Assigned are both included
// ==========================================

$assignedAppointments = [];

$assignedStmt = $conn->prepare("
    SELECT
        jc.id AS job_card_id,
        jc.appointment_id,
        jc.status AS job_status,
        a.user_id,
        u.full_name,
        a.vehicle_registration_number,
        a.brand,
        a.model,
        a.manufacture_year,
        a.date,
        a.time,
        COALESCE(sv.requested_services, 'No services listed') AS requested_services
    FROM job_cards jc
    INNER JOIN appointment a
        ON a.id = jc.appointment_id
    INNER JOIN users u
        ON u.id = a.user_id
    LEFT JOIN (
        SELECT
            aps.appointment_id,
            GROUP_CONCAT(s.service_name SEPARATOR ', ') AS requested_services
        FROM appointment_services aps
        INNER JOIN services s
            ON s.id = aps.service_id
        GROUP BY aps.appointment_id
    ) sv ON sv.appointment_id = a.id
    WHERE jc.mechanic_id = ?
      AND LOWER(TRIM(jc.status)) IN ('pending', 'assigned')
    ORDER BY a.date ASC, a.time ASC
");

$assignedStmt->bind_param("i", $mechanicId);
$assignedStmt->execute();
$assignedAppointments = $assignedStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$assignedStmt->close();

// ==========================================
// GET IN-PROGRESS JOBS
// ==========================================

$inProgressAppointments = [];

$inProgressStmt = $conn->prepare("
    SELECT
        jc.id AS job_card_id,
        jc.appointment_id,
        jc.status AS job_status,
        a.user_id,
        u.full_name,
        a.vehicle_registration_number,
        a.brand,
        a.model,
        a.manufacture_year,
        a.date,
        a.time,
        COALESCE(sv.requested_services, 'No services listed') AS requested_services
    FROM job_cards jc
    INNER JOIN appointment a
        ON a.id = jc.appointment_id
    INNER JOIN users u
        ON u.id = a.user_id
    LEFT JOIN (
        SELECT
            aps.appointment_id,
            GROUP_CONCAT(s.service_name SEPARATOR ', ') AS requested_services
        FROM appointment_services aps
        INNER JOIN services s
            ON s.id = aps.service_id
        GROUP BY aps.appointment_id
    ) sv ON sv.appointment_id = a.id
    WHERE jc.mechanic_id = ?
      AND LOWER(TRIM(jc.status)) = 'in progress'
    ORDER BY a.date ASC, a.time ASC
");

$inProgressStmt->bind_param("i", $mechanicId);
$inProgressStmt->execute();
$inProgressAppointments = $inProgressStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$inProgressStmt->close();

// ==========================================
// GET COMPLETED / CLOSED JOBS
// ==========================================

$closedAppointments = [];

$closedStmt = $conn->prepare("
    SELECT
        jc.id AS job_card_id,
        jc.appointment_id,
        jc.status AS job_status,
        a.user_id,
        u.full_name,
        a.vehicle_registration_number,
        a.brand,
        a.model,
        a.manufacture_year,
        a.date,
        a.time,
        COALESCE(sv.requested_services, 'No services listed') AS requested_services
    FROM job_cards jc
    INNER JOIN appointment a
        ON a.id = jc.appointment_id
    INNER JOIN users u
        ON u.id = a.user_id
    LEFT JOIN (
        SELECT
            aps.appointment_id,
            GROUP_CONCAT(s.service_name SEPARATOR ', ') AS requested_services
        FROM appointment_services aps
        INNER JOIN services s
            ON s.id = aps.service_id
        GROUP BY aps.appointment_id
    ) sv ON sv.appointment_id = a.id
    WHERE jc.mechanic_id = ?
      AND LOWER(TRIM(jc.status)) IN ('completed', 'closed')
    ORDER BY a.date DESC, a.time DESC
");

$closedStmt->bind_param("i", $mechanicId);
$closedStmt->execute();
$closedAppointments = $closedStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$closedStmt->close();

// ==========================================
// DASHBOARD COUNTS
// ==========================================

$approve_count = count($assignedAppointments);
$in_progress_count = count($inProgressAppointments);
$close_count = count($closedAppointments);

?>


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
                <!-- User Management -->
                <li>
                    <a href="userManagement.php" class="flex items-center gap-3 rounded-lg bg-[#E89000] px-4 py-3 text-xs font-semibold text-white shadow-sm">
                        <i class="fa-solid fa-table-columns w-4 text-center"></i>
                        Dashboard
                    </a>
                </li>

                <li>
                    <a href="dashboard.php" class="group flex items-center justify-between rounded-lg px-4 py-3 text-xs font-medium text-gray-300 transition duration-200 hover:bg-white/10 hover:text-white">
                        <span class="flex items-center gap-3">
                            <i class="fa-solid fa-users w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>
                            User Management
                        </span>
                        <i class="fa-solid fa-chevron-right text-[9px] text-white-500"></i>
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
                    <a href="inventory.php" class="group flex items-center justify-between rounded-lg px-4 py-3 text-xs font-medium text-gray-300 transition duration-200 hover:bg-white/10 hover:text-white">
                        <span class="flex items-center gap-3">
                            <i class="fa-solid fa-boxes-stacked w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>
                            Inventory
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
                    <h2 class="text-lg font-bold text-gray-900 sm:text-xl">Mechanic Dashboard</h2>
                    <p class="mt-0.5 text-[11px] text-gray-500">
                        Welcome back, <?php echo htmlspecialchars($_SESSION["full_name"]); ?>
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
            <button type="button" class="text-[11px] font-semibold text-[#E89000] hover:text-orange-600">
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
            <a href="#" class="text-xs font-semibold text-[#E89000] transition hover:text-orange-600">
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
            
<!-- Admin Dashboard Statistics -->
<section class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-100 text-[#E89000]">
                    <i class="fa-solid fa-gauge-high text-lg"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-gray-900">Dashboard Overview</h1>
                    <p class="mt-0.5 text-xs text-gray-500">Track your assigned vehicle service jobs and daily progress</p>
                </div>
            </div>

            <div class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-[11px] text-gray-500 shadow-sm">
                <i class="fa-solid fa-house text-[#E89000]"></i>Dashboard
            </div>
        </section>

        <!-- STATISTICS -->
        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">

            <!-- Assigned Jobs -->
            <div class="rounded-xl border border-orange-100 bg-gradient-to-br from-white to-orange-50/70 p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                <div class="flex items-center justify-between">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-100 text-[#E89000]">
                        <i class="fa-solid fa-clipboard-list text-lg"></i>
                    </div>
                    <span class="rounded-full bg-orange-50 px-2.5 py-1 text-[10px] font-semibold text-orange-600">Assigned</span>
                </div>
                <p class="mt-4 text-xs font-medium text-gray-500">Assigned Jobs</p>
                <div class="mt-1 flex items-end justify-between">
                    <h3 class="text-3xl font-bold text-gray-900"><?php echo $approve_count; ?></h3>
                    <span class="text-[10px] text-gray-400">Awaiting work</span>
                </div>
                <div>
                    <button type="button" onclick="openAssignedAppointmentsModal()"
    class="mt-4 inline-flex cursor-pointer items-center gap-2 text-xs font-semibold text-[#E89000] hover:text-orange-700">
    View assigned appointments
    <i class="fa-solid fa-arrow-right text-[10px]"></i>
</button>
                </div>
            </div>

            <!-- In Progress -->
            <div class="rounded-xl border border-blue-100 bg-gradient-to-br from-white to-blue-50/70 p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                <div class="flex items-center justify-between">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100 text-blue-600">
                        <i class="fa-solid fa-gears text-lg"></i>
                    </div>
                    <span class="rounded-full bg-blue-50 px-2.5 py-1 text-[10px] font-semibold text-blue-600">Ongoing</span>
                </div>
                <p class="mt-4 text-xs font-medium text-gray-500">In Progress</p>
                <div class="mt-1 flex items-end justify-between">
                    <h3 class="text-3xl font-bold text-gray-900"><?php echo $in_progress_count; ?></h3>
                    <span class="text-[10px] text-gray-400">Currently working</span>
                </div>
                <div>
                    <button type="button" onclick="openInProgressModal()"
    class="mt-4 inline-flex cursor-pointer items-center gap-2 text-xs font-semibold text-blue-600 hover:text-blue-700">
    View in-progress jobs
    <i class="fa-solid fa-arrow-right text-[10px]"></i>
</button>
                </div>
            </div>

            <!-- Completed Jobs -->
            <div class="rounded-xl border border-green-100 bg-gradient-to-br from-white to-green-50/70 p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg sm:col-span-2 xl:col-span-1">
                <div class="flex items-center justify-between">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-green-100 text-green-600">
                        <i class="fa-solid fa-circle-check text-lg"></i>
                    </div>
                    <span class="rounded-full bg-green-50 px-2.5 py-1 text-[10px] font-semibold text-green-600">Completed</span>
                </div>
                <p class="mt-4 text-xs font-medium text-gray-500">Completed Jobs</p>
                <div class="mt-1 flex items-end justify-between">
                    <h3 class="text-3xl font-bold text-gray-900"><?php echo $close_count; ?></h3>
                    <span class="text-[10px] text-gray-400">Successfully finished</span>
                </div>
                <div>
                    <button type="button" onclick="openClosedJobsModal()"
    class="mt-4 inline-flex cursor-pointer items-center gap-2 text-xs font-semibold text-green-600 hover:text-green-700">
    View closed jobs
    <i class="fa-solid fa-arrow-right text-[10px]"></i>
</button>
                </div>
            </div>
        </section>

        <!-- WORK SUMMARY -->
        <section class="grid grid-cols-1 gap-4 xl:grid-cols-3">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm xl:col-span-2">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-gray-900">Work Summary</h2>
                        <p class="mt-1 text-[11px] text-gray-500">Your current job distribution</p>
                    </div>
                    <span class="w-fit rounded-full bg-gray-100 px-3 py-1 text-[10px] text-gray-600">All assigned jobs</span>
                </div>

                <div class="mt-5 space-y-4">
                    <div>
                        <div class="mb-2 flex justify-between text-[11px]">
                            <span class="font-medium text-gray-600">Assigned</span>
                            <span class="font-semibold text-gray-800">5 jobs</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                            <div class="h-full w-[31%] rounded-full bg-[#E89000]"></div>
                        </div>
                    </div>

                    <div>
                        <div class="mb-2 flex justify-between text-[11px]">
                            <span class="font-medium text-gray-600">In Progress</span>
                            <span class="font-semibold text-gray-800">3 jobs</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                            <div class="h-full w-[19%] rounded-full bg-blue-500"></div>
                        </div>
                    </div>

                    <div>
                        <div class="mb-2 flex justify-between text-[11px]">
                            <span class="font-medium text-gray-600">Completed</span>
                            <span class="font-semibold text-gray-800">8 jobs</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                            <div class="h-full w-[50%] rounded-full bg-green-500"></div>
                        </div>
                    </div>
                </div>
            </div>

        </section>

        </div>
    </main>
</div>


<!-- ASSIGNED JOBS MODAL -->
<div id="assignedAppointmentsModal"
    class="fixed inset-0 z-[100] hidden items-center justify-center bg-gray-950/60 p-3 backdrop-blur-sm sm:p-6"
    role="dialog" aria-modal="true">

    <div class="flex max-h-[90vh] w-full max-w-7xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">

        <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4 sm:px-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-100 text-[#E89000]">
                    <i class="fa-solid fa-clipboard-list"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">Assigned Jobs</h2>
                    <p class="mt-0.5 text-[11px] text-gray-500">Jobs assigned to your mechanic account</p>
                </div>
            </div>

            <button type="button" data-close-modal="assignedAppointmentsModal"
                class="flex h-9 w-9 items-center justify-center rounded-lg text-gray-400 transition hover:bg-red-50 hover:text-red-500">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <div class="overflow-y-auto p-5 sm:p-6">

            <?php if (!empty($assignedAppointments)): ?>

                <div class="overflow-x-auto rounded-xl border border-gray-200">
                    <table class="w-full min-w-[1100px] text-left">

                        <thead class="bg-gray-50">
                            <tr class="text-[10px] font-bold uppercase tracking-wide text-gray-500">
                                <th class="px-4 py-3">Job Card</th>
                                <th class="px-4 py-3">Customer</th>
                                <th class="px-4 py-3">Vehicle</th>
                                <th class="px-4 py-3">Requested Services</th>
                                <th class="px-4 py-3">Appointment</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-center">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">

                            <?php foreach ($assignedAppointments as $appointment): ?>

                                <tr class="transition hover:bg-orange-50/30">

                                    <td class="px-4 py-4 text-xs font-bold text-gray-800">
                                        #<?php echo (int)$appointment['job_card_id']; ?>
                                    </td>

                                    <td class="px-4 py-4">
                                        <p class="text-xs font-semibold text-gray-800">
                                            <?php echo htmlspecialchars($appointment['full_name']); ?>
                                        </p>
                                    </td>

                                    <td class="px-4 py-4">
                                        <p class="text-xs font-semibold text-gray-800">
                                            <?php echo htmlspecialchars($appointment['vehicle_registration_number'] ?? ''); ?>
                                        </p>
                                        <p class="mt-1 text-[10px] text-gray-500">
                                            <?php echo htmlspecialchars(trim(($appointment['brand'] ?? '') . ' ' . ($appointment['model'] ?? '') . ' ' . ($appointment['manufacture_year'] ?? ''))); ?>
                                        </p>
                                    </td>

                                    <td class="max-w-xs px-4 py-4 text-xs leading-relaxed text-gray-600">
                                        <?php echo htmlspecialchars($appointment['requested_services']); ?>
                                    </td>

                                    <td class="px-4 py-4">
                                        <p class="text-xs font-medium text-gray-700">
                                            <?php echo htmlspecialchars($appointment['date']); ?>
                                        </p>
                                        <p class="mt-1 text-[10px] text-gray-500">
                                            <?php echo htmlspecialchars($appointment['time']); ?>
                                        </p>
                                    </td>

                                    <td class="px-4 py-4">
                                        <span class="rounded-full bg-orange-50 px-2.5 py-1 text-[10px] font-semibold text-orange-600">
                                            Assigned
                                        </span>
                                    </td>

                                    <td class="px-4 py-4">
                                        <div class="flex items-center justify-center gap-2">

                                            <form method="POST" action="dashboard.php"
                                                onsubmit="return confirm('Start working on this job?')">

                                                <input type="hidden" name="job_action" value="start">
                                                <input type="hidden" name="job_card_id" value="<?php echo (int)$appointment['job_card_id']; ?>">
                                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                                                <input type="hidden" name="return_modal" value="in-progress">

                                                <button type="submit"
                                                    class="whitespace-nowrap rounded-lg bg-blue-600 px-3 py-2 text-[10px] font-semibold text-white transition hover:bg-blue-700">
                                                    <i class="fa-solid fa-play mr-1"></i>
                                                    In Progress
                                                </button>
                                            </form>

                                            <form method="POST" action="dashboard.php"
                                                onsubmit="return confirm('Reject this job assignment? It will be returned for reassignment.')">

                                                <input type="hidden" name="job_action" value="reject">
                                                <input type="hidden" name="job_card_id" value="<?php echo (int)$appointment['job_card_id']; ?>">
                                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                                                <input type="hidden" name="return_modal" value="assigned">

                                                <button type="submit"
                                                    class="whitespace-nowrap rounded-lg bg-red-600 px-3 py-2 text-[10px] font-semibold text-white transition hover:bg-red-700">
                                                    <i class="fa-solid fa-xmark mr-1"></i>
                                                    Reject
                                                </button>
                                            </form>

                                        </div>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>
                    </table>
                </div>

            <?php else: ?>

                <div class="py-12 text-center">
                    <i class="fa-solid fa-clipboard-check text-3xl text-gray-300"></i>
                    <h3 class="mt-3 text-sm font-bold text-gray-700">No Assigned Jobs</h3>
                    <p class="mt-1 text-xs text-gray-500">You currently have no assigned jobs.</p>
                </div>

            <?php endif; ?>

        </div>

        <div class="flex justify-end border-t border-gray-100 bg-gray-50 px-5 py-4">
            <button type="button" data-close-modal="assignedAppointmentsModal"
                class="rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-xs font-semibold text-gray-600 transition hover:bg-gray-100">
                Close
            </button>
        </div>

    </div>
</div>



<!-- IN PROGRESS JOBS MODAL -->
<div id="inProgressModal"
    class="fixed inset-0 z-[100] hidden items-center justify-center bg-gray-950/60 p-3 backdrop-blur-sm sm:p-6"
    role="dialog" aria-modal="true">

    <div class="flex max-h-[90vh] w-full max-w-7xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">

        <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4 sm:px-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-blue-600">
                    <i class="fa-solid fa-gears"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">In Progress Jobs</h2>
                    <p class="mt-0.5 text-[11px] text-gray-500">Vehicles currently being serviced</p>
                </div>
            </div>

            <button type="button" data-close-modal="inProgressModal"
                class="flex h-9 w-9 items-center justify-center rounded-lg text-gray-400 transition hover:bg-red-50 hover:text-red-500">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <div class="overflow-y-auto p-5 sm:p-6">

            <?php if (!empty($inProgressAppointments)): ?>

                <div class="overflow-x-auto rounded-xl border border-gray-200">
                    <table class="w-full min-w-[1050px] text-left">

                        <thead class="bg-gray-50">
                            <tr class="text-[10px] font-bold uppercase tracking-wide text-gray-500">
                                <th class="px-4 py-3">Job Card</th>
                                <th class="px-4 py-3">Customer</th>
                                <th class="px-4 py-3">Vehicle</th>
                                <th class="px-4 py-3">Requested Services</th>
                                <th class="px-4 py-3">Appointment</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-center">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">

                            <?php foreach ($inProgressAppointments as $appointment): ?>

                                <tr class="transition hover:bg-blue-50/30">

                                    <td class="px-4 py-4 text-xs font-bold text-gray-800">
                                        #<?php echo (int)$appointment['job_card_id']; ?>
                                    </td>

                                    <td class="px-4 py-4 text-xs font-semibold text-gray-800">
                                        <?php echo htmlspecialchars($appointment['full_name']); ?>
                                    </td>

                                    <td class="px-4 py-4">
                                        <p class="text-xs font-semibold text-gray-800">
                                            <?php echo htmlspecialchars($appointment['vehicle_registration_number'] ?? ''); ?>
                                        </p>
                                        <p class="mt-1 text-[10px] text-gray-500">
                                            <?php echo htmlspecialchars(trim(($appointment['brand'] ?? '') . ' ' . ($appointment['model'] ?? '') . ' ' . ($appointment['manufacture_year'] ?? ''))); ?>
                                        </p>
                                    </td>

                                    <td class="max-w-xs px-4 py-4 text-xs leading-relaxed text-gray-600">
                                        <?php echo htmlspecialchars($appointment['requested_services']); ?>
                                    </td>

                                    <td class="px-4 py-4">
                                        <p class="text-xs font-medium text-gray-700">
                                            <?php echo htmlspecialchars($appointment['date']); ?>
                                        </p>
                                        <p class="mt-1 text-[10px] text-gray-500">
                                            <?php echo htmlspecialchars($appointment['time']); ?>
                                        </p>
                                    </td>

                                    <td class="px-4 py-4">
                                        <span class="rounded-full bg-blue-50 px-2.5 py-1 text-[10px] font-semibold text-blue-600">
                                            In Progress
                                        </span>
                                    </td>

                                    <td class="px-4 py-4 text-center">

                                        <form method="POST" action="dashboard.php"
                                            onsubmit="return confirm('Are you sure this job is completed?')">

                                            <input type="hidden" name="job_action" value="close">
                                            <input type="hidden" name="job_card_id" value="<?php echo (int)$appointment['job_card_id']; ?>">
                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                                            <input type="hidden" name="return_modal" value="closed">

                                            <button type="submit"
                                                class="whitespace-nowrap rounded-lg bg-green-600 px-3 py-2 text-[10px] font-semibold text-white transition hover:bg-green-700">
                                                <i class="fa-solid fa-check mr-1"></i>
                                                Close Job
                                            </button>

                                        </form>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>
                    </table>
                </div>

            <?php else: ?>

                <div class="py-12 text-center">
                    <i class="fa-solid fa-gears text-3xl text-gray-300"></i>
                    <h3 class="mt-3 text-sm font-bold text-gray-700">No Jobs In Progress</h3>
                    <p class="mt-1 text-xs text-gray-500">You do not have any ongoing jobs.</p>
                </div>

            <?php endif; ?>

        </div>

        <div class="flex justify-end border-t border-gray-100 bg-gray-50 px-5 py-4">
            <button type="button" data-close-modal="inProgressModal"
                class="rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-xs font-semibold text-gray-600 transition hover:bg-gray-100">
                Close
            </button>
        </div>

    </div>
</div>



<!-- CLOSED JOBS MODAL -->
<div id="closedJobsModal"
    class="fixed inset-0 z-[100] hidden items-center justify-center bg-gray-950/60 p-3 backdrop-blur-sm sm:p-6"
    role="dialog" aria-modal="true">

    <div class="flex max-h-[90vh] w-full max-w-7xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">

        <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4 sm:px-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-green-100 text-green-600">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">Closed Jobs</h2>
                    <p class="mt-0.5 text-[11px] text-gray-500">Successfully completed vehicle service jobs</p>
                </div>
            </div>

            <button type="button" data-close-modal="closedJobsModal"
                class="flex h-9 w-9 items-center justify-center rounded-lg text-gray-400 transition hover:bg-red-50 hover:text-red-500">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <div class="overflow-y-auto p-5 sm:p-6">

            <?php if (!empty($closedAppointments)): ?>

                <div class="overflow-x-auto rounded-xl border border-gray-200">
                    <table class="w-full min-w-[1000px] text-left">

                        <thead class="bg-gray-50">
                            <tr class="text-[10px] font-bold uppercase tracking-wide text-gray-500">
                                <th class="px-4 py-3">Job Card</th>
                                <th class="px-4 py-3">Customer</th>
                                <th class="px-4 py-3">Vehicle</th>
                                <th class="px-4 py-3">Requested Services</th>
                                <th class="px-4 py-3">Appointment</th>
                                <th class="px-4 py-3">Status</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">

                            <?php foreach ($closedAppointments as $appointment): ?>

                                <tr class="transition hover:bg-green-50/30">

                                    <td class="px-4 py-4 text-xs font-bold text-gray-800">
                                        #<?php echo (int)$appointment['job_card_id']; ?>
                                    </td>

                                    <td class="px-4 py-4 text-xs font-semibold text-gray-800">
                                        <?php echo htmlspecialchars($appointment['full_name']); ?>
                                    </td>

                                    <td class="px-4 py-4">
                                        <p class="text-xs font-semibold text-gray-800">
                                            <?php echo htmlspecialchars($appointment['vehicle_registration_number'] ?? ''); ?>
                                        </p>
                                        <p class="mt-1 text-[10px] text-gray-500">
                                            <?php echo htmlspecialchars(trim(($appointment['brand'] ?? '') . ' ' . ($appointment['model'] ?? '') . ' ' . ($appointment['manufacture_year'] ?? ''))); ?>
                                        </p>
                                    </td>

                                    <td class="max-w-xs px-4 py-4 text-xs leading-relaxed text-gray-600">
                                        <?php echo htmlspecialchars($appointment['requested_services']); ?>
                                    </td>

                                    <td class="px-4 py-4">
                                        <p class="text-xs font-medium text-gray-700">
                                            <?php echo htmlspecialchars($appointment['date']); ?>
                                        </p>
                                        <p class="mt-1 text-[10px] text-gray-500">
                                            <?php echo htmlspecialchars($appointment['time']); ?>
                                        </p>
                                    </td>

                                    <td class="px-4 py-4">
                                        <span class="rounded-full bg-green-50 px-2.5 py-1 text-[10px] font-semibold text-green-600">
                                            <i class="fa-solid fa-check mr-1"></i>
                                            Completed
                                        </span>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>
                    </table>
                </div>

            <?php else: ?>

                <div class="py-12 text-center">
                    <i class="fa-solid fa-circle-check text-3xl text-gray-300"></i>
                    <h3 class="mt-3 text-sm font-bold text-gray-700">No Closed Jobs Yet</h3>
                    <p class="mt-1 text-xs text-gray-500">Your completed jobs will appear here.</p>
                </div>

            <?php endif; ?>

        </div>

        <div class="flex justify-end border-t border-gray-100 bg-gray-50 px-5 py-4">
            <button type="button" data-close-modal="closedJobsModal"
                class="rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-xs font-semibold text-gray-600 transition hover:bg-gray-100">
                Close
            </button>
        </div>

    </div>
</div>




<script>

// ==========================================
// MECHANIC DASHBOARD MODALS
// ==========================================

function openModal(modalId) {
    const modal = document.getElementById(modalId);

    if (modal) {
        modal.classList.remove("hidden");
        modal.classList.add("flex");
        document.body.classList.add("overflow-hidden");
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);

    if (modal) {
        modal.classList.add("hidden");
        modal.classList.remove("flex");
        document.body.classList.remove("overflow-hidden");
    }
}

// Open buttons
function openAssignedAppointmentsModal() {
    openModal("assignedAppointmentsModal");
}

function openInProgressModal() {
    openModal("inProgressModal");
}

function openClosedJobsModal() {
    openModal("closedJobsModal");
}

// Close buttons
document.querySelectorAll("[data-close-modal]").forEach(button => {
    button.addEventListener("click", function () {
        closeModal(this.dataset.closeModal);
    });
});

// Close modal when clicking outside
document.querySelectorAll('[role="dialog"]').forEach(modal => {
    modal.addEventListener("click", function (event) {
        if (event.target === modal) {
            closeModal(modal.id);
        }
    });
});

// Close open modal with Escape
document.addEventListener("keydown", function (event) {
    if (event.key === "Escape") {
        document.querySelectorAll('[role="dialog"]').forEach(modal => {
            if (!modal.classList.contains("hidden")) {
                closeModal(modal.id);
            }
        });
    }
});

// Reopen the correct modal after a PHP action and page reload
const urlParams = new URLSearchParams(window.location.search);
const requestedModal = urlParams.get("modal");

if (requestedModal === "assigned") {
    openAssignedAppointmentsModal();
} else if (requestedModal === "in-progress") {
    openInProgressModal();
} else if (requestedModal === "closed") {
    openClosedJobsModal();
}

</script>
</body>

</html>