<?php
session_start();

/*
 * AutoPulse database connection.
 * If your local MySQL username/password differs, update these values.
 */
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli("localhost", "root", "", "autopulse");
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    exit("Database connection failed. Check the database credentials at the top of dashboard.php.");
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['csrf_token'];

$flashMessage = '';
$flashType = 'success';
$reopenModal = false;


/* Approve, reject, or assign a mechanic to a pending appointment. */

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    in_array($_POST['action'] ?? '', [
        'approve_appointment',
        'reject_appointment',
        'assign_mechanic'
    ], true)
) {
    $reopenModal = true;

    $postedToken = $_POST['csrf_token'] ?? '';
    $action = $_POST['action'];

    if (!hash_equals($_SESSION['csrf_token'], $postedToken)) {
        $flashMessage = 'Security validation failed. Please refresh the page and try again.';
        $flashType = 'error';

    } else {

        $appointmentId = filter_input(INPUT_POST, 'appointment_id', FILTER_VALIDATE_INT);

        if (!$appointmentId || $appointmentId < 1) {
            $flashMessage = 'Invalid appointment selected.';
            $flashType = 'error';

        } elseif ($action === 'assign_mechanic') {

            $mechanicId = filter_input(INPUT_POST, 'mechanic_id', FILTER_VALIDATE_INT);

            if (!$mechanicId || $mechanicId < 1) {
                $flashMessage = 'Please select a valid mechanic.';
                $flashType = 'error';

            } else {

                try {
                    $conn->begin_transaction();

                    // Verify that the selected user is a mechanic.
                    $mechanicStmt = $conn->prepare("
                        SELECT id
                        FROM users
                        WHERE id = ? AND LOWER(role) = 'mechanic'
                    ");

                    $mechanicStmt->bind_param("i", $mechanicId);
                    $mechanicStmt->execute();

                    $mechanicResult = $mechanicStmt->get_result();
                    $validMechanic = $mechanicResult->num_rows === 1;

                    $mechanicStmt->close();

                    if (!$validMechanic) {
                        throw new Exception('The selected mechanic does not exist.');
                    }

                    // Lock the appointment and verify it is still pending.
                    $appointmentStmt = $conn->prepare("
                        SELECT id
                        FROM appointment
                        WHERE id = ? AND LOWER(status) = 'pending'
                        FOR UPDATE
                    ");

                    $appointmentStmt->bind_param("i", $appointmentId);
                    $appointmentStmt->execute();

                    $appointmentResult = $appointmentStmt->get_result();
                    $validAppointment = $appointmentResult->num_rows === 1;

                    $appointmentStmt->close();

                    if (!$validAppointment) {
                        throw new Exception('This appointment is no longer pending.');
                    }

                    // Prevent duplicate job cards.
                    $existingStmt = $conn->prepare("
                        SELECT id
                        FROM job_cards
                        WHERE appointment_id = ?
                        LIMIT 1
                    ");

                    $existingStmt->bind_param("i", $appointmentId);
                    $existingStmt->execute();

                    $existingResult = $existingStmt->get_result();
                    $jobCardExists = $existingResult->num_rows > 0;

                    $existingStmt->close();

                    if ($jobCardExists) {
                        throw new Exception('A job card has already been created for this appointment.');
                    }

                    // Create the job card and assign the selected mechanic.
                    $insertStmt = $conn->prepare("
                        INSERT INTO job_cards
                            (appointment_id, mechanic_id, status)
                        VALUES (?, ?, 'Pending')
                    ");

                    $insertStmt->bind_param("ii", $appointmentId, $mechanicId);
                    $insertStmt->execute();
                    $insertStmt->close();

                    // Mark the appointment as assigned after successful mechanic assignment.
                    $updateStmt = $conn->prepare("
                        UPDATE appointment
                        SET status = 'assigned'
                        WHERE id = ? AND LOWER(status) = 'pending'
                    ");

                    $updateStmt->bind_param("i", $appointmentId);
                    $updateStmt->execute();

                    if ($updateStmt->affected_rows !== 1) {
                        throw new Exception('Unable to update the appointment status.');
                    }

                    $updateStmt->close();

                    $conn->commit();

                    $flashMessage = "Appointment APT-" . $appointmentId .
                        " has been assigned to the selected mechanic successfully.";

                    $flashType = 'success';

                } catch (Throwable $e) {

                    $conn->rollback();

                    $flashMessage = $e instanceof Exception
                        ? $e->getMessage()
                        : 'Unable to assign the mechanic. Please try again.';

                    $flashType = 'error';
                }
            }

        } else {

            // Existing approve/reject functionality.
            $newStatus = ($action === 'approve_appointment')
                ? 'approved'
                : 'rejected';

            $stmt = $conn->prepare("
                UPDATE appointment
                SET status = ?
                WHERE id = ? AND LOWER(status) = 'pending'
            ");

            $stmt->bind_param("si", $newStatus, $appointmentId);
            $stmt->execute();

            if ($stmt->affected_rows === 1) {

                $flashMessage = ($newStatus === 'approved')
                    ? "Appointment APT-" . $appointmentId . " has been approved successfully."
                    : "Appointment APT-" . $appointmentId . " has been rejected.";

                $flashType = 'success';

            } else {

                $flashMessage = 'This appointment is no longer pending, or it could not be found.';
                $flashType = 'error';
            }

            $stmt->close();
        }
    }
}


/* Load registered mechanics for the assignment dropdown. */

$mechanics = [];

$mechanicResult = $conn->query("
    SELECT id, full_name
    FROM users
    WHERE LOWER(role) = 'mechanic'
    ORDER BY full_name ASC
");

while ($mechanic = $mechanicResult->fetch_assoc()) {
    $mechanics[] = $mechanic;
}

$mechanicResult->free();


/* Load all pending appointment requests with their customer and service details. */
$pendingRequests = [];
$sql = "
    SELECT
        a.id,
        a.user_id,
        a.vehicle_registration_number,
        a.brand,
        a.model,
        a.manufacture_year,
        a.date,
        a.time,
        a.additional_note,
        a.status,
        u.full_name,
        u.phone,
        u.email,
        GROUP_CONCAT(DISTINCT s.service_name ORDER BY s.service_name SEPARATOR ', ') AS requested_services
    FROM appointment a
    INNER JOIN users u ON u.id = a.user_id
    LEFT JOIN appointment_services aps ON aps.appointment_id = a.id
    LEFT JOIN services s ON s.id = aps.service_id
    WHERE LOWER(a.status) = 'pending'
    GROUP BY
        a.id, a.user_id, a.vehicle_registration_number, a.brand, a.model,
        a.manufacture_year, a.date, a.time, a.additional_note, a.status,
        u.full_name, u.phone, u.email
    ORDER BY a.date ASC, a.time ASC
";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $pendingRequests[] = $row;
}
$result->free();

$pendingCount = count($pendingRequests);
$approvedResult = $conn->query("SELECT COUNT(*) AS total FROM appointment WHERE LOWER(status) IN ('approved', 'assigned')");
$approvedCount = (int) $approvedResult->fetch_assoc()['total'];
$approvedResult->free();
$rejectedResult = $conn->query("SELECT COUNT(*) AS total FROM appointment WHERE LOWER(status) = 'rejected'");
$rejectedCount = (int) $rejectedResult->fetch_assoc()['total'];
$rejectedResult->free();

/* Load accepted (approved/assigned) appointment records for the modal. */
$acceptedRecords = [];
$acceptedSql = "
    SELECT
        a.id, a.vehicle_registration_number, a.brand, a.model,
        a.manufacture_year, a.date, a.time, a.status,
        u.full_name, u.phone, u.email,
        GROUP_CONCAT(DISTINCT s.service_name ORDER BY s.service_name SEPARATOR ', ') AS requested_services
    FROM appointment a
    INNER JOIN users u ON u.id = a.user_id
    LEFT JOIN appointment_services aps ON aps.appointment_id = a.id
    LEFT JOIN services s ON s.id = aps.service_id
    WHERE LOWER(a.status) IN ('approved', 'assigned')
    GROUP BY a.id, a.vehicle_registration_number, a.brand, a.model,
             a.manufacture_year, a.date, a.time, a.status,
             u.full_name, u.phone, u.email
    ORDER BY a.date ASC, a.time ASC
";
$acceptedResult = $conn->query($acceptedSql);
while ($row = $acceptedResult->fetch_assoc()) { $acceptedRecords[] = $row; }
$acceptedResult->free();

/* Load rejected appointment records for the modal. */
$rejectedRecords = [];
$rejectedSql = "
    SELECT
        a.id, a.vehicle_registration_number, a.brand, a.model,
        a.manufacture_year, a.date, a.time, a.status,
        u.full_name, u.phone, u.email,
        GROUP_CONCAT(DISTINCT s.service_name ORDER BY s.service_name SEPARATOR ', ') AS requested_services
    FROM appointment a
    INNER JOIN users u ON u.id = a.user_id
    LEFT JOIN appointment_services aps ON aps.appointment_id = a.id
    LEFT JOIN services s ON s.id = aps.service_id
    WHERE LOWER(a.status) = 'rejected'
    GROUP BY a.id, a.vehicle_registration_number, a.brand, a.model,
             a.manufacture_year, a.date, a.time, a.status,
             u.full_name, u.phone, u.email
    ORDER BY a.date DESC
";
$rejectedResult2 = $conn->query($rejectedSql);
while ($row = $rejectedResult2->fetch_assoc()) { $rejectedRecords[] = $row; }
$rejectedResult2->free();

/* Load assigned job card records for the modal. */
$assignedRecords = [];
$assignedSql = "
    SELECT
        jc.id AS job_card_id, jc.status AS job_status, jc.created_at AS job_created,
        a.id AS appointment_id, a.vehicle_registration_number, a.brand, a.model,
        a.manufacture_year, a.date, a.time,
        u.full_name AS customer_name, u.phone AS customer_phone,
        m.full_name AS mechanic_name
    FROM job_cards jc
    LEFT JOIN appointment a ON a.id = jc.appointment_id
    LEFT JOIN users u ON u.id = a.user_id
    LEFT JOIN users m ON m.id = jc.mechanic_id
    WHERE LOWER(jc.status) = 'assigned'
    ORDER BY jc.created_at DESC
";
$assignedResult = $conn->query($assignedSql);
$assignedCount = 0;
while ($row = $assignedResult->fetch_assoc()) { $assignedRecords[] = $row; $assignedCount++; }
$assignedResult->free();

/* Load in-progress job card records for the modal. */
$inProgressRecords = [];
$inProgressSql = "
    SELECT
        jc.id AS job_card_id, jc.status AS job_status, jc.created_at AS job_created,
        a.id AS appointment_id, a.vehicle_registration_number, a.brand, a.model,
        a.manufacture_year, a.date, a.time,
        u.full_name AS customer_name, u.phone AS customer_phone,
        m.full_name AS mechanic_name
    FROM job_cards jc
    LEFT JOIN appointment a ON a.id = jc.appointment_id
    LEFT JOIN users u ON u.id = a.user_id
    LEFT JOIN users m ON m.id = jc.mechanic_id
    WHERE LOWER(jc.status) = 'in progress'
    ORDER BY jc.created_at DESC
";
$inProgressResult = $conn->query($inProgressSql);
$inProgressCount = 0;
while ($row = $inProgressResult->fetch_assoc()) { $inProgressRecords[] = $row; $inProgressCount++; }
$inProgressResult->free();

/* Load completed job card records for the modal. */
$closedRecords = [];
$closedSql = "
    SELECT
        jc.id AS job_card_id, jc.status AS job_status, jc.created_at AS job_created,
        a.id AS appointment_id, a.vehicle_registration_number, a.brand, a.model,
        a.manufacture_year, a.date, a.time,
        u.full_name AS customer_name, u.phone AS customer_phone,
        m.full_name AS mechanic_name
    FROM job_cards jc
    LEFT JOIN appointment a ON a.id = jc.appointment_id
    LEFT JOIN users u ON u.id = a.user_id
    LEFT JOIN users m ON m.id = jc.mechanic_id
    WHERE LOWER(jc.status) = 'completed'
    ORDER BY jc.created_at DESC
";
$closedResult = $conn->query($closedSql);
$closedCount = 0;
while ($row = $closedResult->fetch_assoc()) { $closedRecords[] = $row; $closedCount++; }
$closedResult->free();
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
        <aside
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
                            class="flex items-center gap-3 rounded-lg bg-[#E89000] px-4 py-3 text-xs font-semibold text-white shadow-sm">
                            <i class="fa-solid fa-table-columns w-4 text-center"></i>
                            Dashboard
                        </a>
                    </li>

                    <!-- User Management -->
                    <li>
                        <a href="user_management.php"
                            class="group flex items-center justify-between rounded-lg px-4 py-3 text-xs font-medium text-gray-300 transition duration-200 hover:bg-white/10 hover:text-white">
                            <span class="flex items-center gap-3">
                                <i
                                    class="fa-solid fa-users w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>
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
                                <i
                                    class="fa-solid fa-screwdriver-wrench w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>
                                Services
                            </span>
                            <i class="fa-solid fa-chevron-right text-[9px] text-gray-500"></i>
                        </a>
                    </li>

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
                            <a href="login.html"
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
                            <summary aria-label="Notifications"
                                class="relative flex h-9 w-9 cursor-pointer list-none items-center justify-center rounded-full bg-gray-100 transition hover:bg-orange-50 [&::-webkit-details-marker]:hidden">
                                <i
                                    class="fa-regular fa-bell text-base text-gray-700 transition group-open:text-[#E89000]"></i>
                                <span
                                    class="absolute right-0 top-0 flex h-4 w-4 items-center justify-center rounded-full border-2 border-white bg-red-500 text-[9px] font-bold text-white">
                                    3
                                </span>
                            </summary>

                            <!-- Dropdown Panel -->
                            <div
                                class="absolute right-0 top-12 z-50 w-80 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl sm:w-96">

                                <!-- Header -->
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

                                <!-- Notification List -->
                                <div class="max-h-80 overflow-y-auto">

                                    <!-- Notification 1 -->
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

                                    <!-- Notification 2 -->
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

                                    <!-- Notification 3 -->
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
                                            <p class="mt-1 text-[11px] leading-relaxed text-gray-500">
                                                A new customer has registered on AutoPulse.
                                            </p>
                                            <span class="mt-1.5 block text-[10px] text-gray-400">1 hour ago</span>
                                        </div>
                                    </div>

                                    <!-- Notification 4 -->
                                    <div class="flex gap-3 px-4 py-3 transition hover:bg-gray-50">
                                        <div
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-orange-100">
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


                <!-- Receptionist Dashboard Statistics -->

                <!-- Reception Dashboard Status Overview -->
                <section class="mb-6">

                    <!-- Page Heading -->
                    <section class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-100 text-[#E89000]">
                                <i class="fa-solid fa-screwdriver-wrench text-lg"></i>
                            </div>
                            <div>
                                <h1 class="text-xl font-bold tracking-tight text-gray-900">Service Management</h1>
                                <p class="mt-0.5 text-xs text-gray-500">View and manage available vehicle services</p>
                            </div>
                        </div>

                        <div
                            class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-[11px] text-gray-500 shadow-sm">
                            <i class="fa-solid fa-house text-[#E89000]"></i>
                            <span>Dashboard</span>
                        </div>
                    </section>

                    <!-- 4 Column Grid -->
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4 mt-5">

                        <!-- Today's Appointments - Full Width -->
                        <div
                            class="group relative overflow-hidden rounded-2xl border border-orange-100 bg-gradient-to-br from-white to-orange-50/70 p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-orange-100/60 sm:col-span-2 xl:col-span-4">

                            <div class="relative z-10 flex flex-col justify-between gap-5 sm:flex-row sm:items-center">

                                <div class="flex items-center gap-4">

                                    <div
                                        class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-orange-100">
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

                                    <div
                                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-white text-[#E89000] shadow-sm transition-transform duration-300 group-hover:translate-x-1 cursor-pointer">
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </div>

                                </div>

                            </div>

                            <div class="absolute -right-10 -top-12 h-40 w-40 rounded-full bg-orange-100/40"></div>
                        </div>

                        <!-- Pending Requests -->
                        <div
                            class="group cursor-pointer rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-orange-300 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-50 text-orange-500">
                                    <i class="fa-solid fa-clock text-lg"></i>
                                </div>
                                <span
                                    class="rounded-full bg-orange-50 px-2.5 py-1 text-xs font-medium text-orange-600">Pending</span>
                            </div>
                            <h3 class="mt-3 text-2xl font-bold text-gray-800"><?= $pendingCount ?></h3>
                            <p class="mt-0.5 text-sm font-semibold text-gray-700">Pending Requests</p>
                            <p class="mt-1 text-xs text-gray-500">Requests waiting for approval</p>
                            <a href="javascript:void(0)" onclick="openStatusModal('pendingModal')"
                                class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-orange-500 transition hover:text-orange-700">
                                View Records <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>

                        <!-- Accepted -->
                        <div
                            class="group cursor-pointer rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-green-300 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-50 text-green-500">
                                    <i class="fa-solid fa-check-circle text-lg"></i>
                                </div>
                                <span
                                    class="rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-600">Accepted</span>
                            </div>
                            <h3 class="mt-3 text-2xl font-bold text-gray-800"><?= $approvedCount ?></h3>
                            <p class="mt-0.5 text-sm font-semibold text-gray-700">Accepted</p>
                            <p class="mt-1 text-xs text-gray-500">Approved service requests</p>
                            <a href="javascript:void(0)" onclick="openStatusModal('acceptedModal')"
                                class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-green-500 transition hover:text-green-700">
                                View Records <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>

                        <!-- Rejected -->
                        <div
                            class="group cursor-pointer rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-red-300 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-50 text-red-500">
                                    <i class="fa-solid fa-circle-xmark text-lg"></i>
                                </div>
                                <span
                                    class="rounded-full bg-red-50 px-2.5 py-1 text-xs font-medium text-red-600">Rejected</span>
                            </div>
                            <h3 class="mt-3 text-2xl font-bold text-gray-800"><?= $rejectedCount ?></h3>
                            <p class="mt-0.5 text-sm font-semibold text-gray-700">Rejected</p>
                            <p class="mt-1 text-xs text-gray-500">Declined service requests</p>
                            <a href="javascript:void(0)" onclick="openStatusModal('rejectedModal')"
                                class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-red-500 transition hover:text-red-700">
                                View Records <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>

                        <!-- Assigned Jobs -->
                        <div
                            class="group cursor-pointer rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-blue-300 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-500">
                                    <i class="fa-solid fa-clipboard-list text-lg"></i>
                                </div>
                                <span
                                    class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-600">Assigned</span>
                            </div>
                            <h3 class="mt-3 text-2xl font-bold text-gray-800"><?= $assignedCount ?></h3>
                            <p class="mt-0.5 text-sm font-semibold text-gray-700">Assigned Jobs</p>
                            <p class="mt-1 text-xs text-gray-500">Jobs assigned to mechanics</p>
                            <a href="javascript:void(0)" onclick="openStatusModal('assignedModal')"
                                class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-blue-500 transition hover:text-blue-700">
                                View Records <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>

                        <!-- In Progress -->
                        <div
                            class="group cursor-pointer rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-purple-300 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-50 text-purple-500">
                                    <i class="fa-solid fa-gears text-lg"></i>
                                </div>
                                <span
                                    class="rounded-full bg-purple-50 px-2.5 py-1 text-xs font-medium text-purple-600">Ongoing</span>
                            </div>
                            <h3 class="mt-3 text-2xl font-bold text-gray-800"><?= $inProgressCount ?></h3>
                            <p class="mt-0.5 text-sm font-semibold text-gray-700">In Progress</p>
                            <p class="mt-1 text-xs text-gray-500">Currently active service jobs</p>
                            <a href="javascript:void(0)" onclick="openStatusModal('inProgressModal')"
                                class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-purple-500 transition hover:text-purple-700">
                                View Records <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>

                        <!-- Closed Jobs -->
                        <div
                            class="group cursor-pointer rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-gray-400 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 text-gray-600">
                                    <i class="fa-solid fa-circle-check text-lg"></i>
                                </div>
                                <span
                                    class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">Completed</span>
                            </div>
                            <h3 class="mt-3 text-2xl font-bold text-gray-800"><?= $closedCount ?></h3>
                            <p class="mt-0.5 text-sm font-semibold text-gray-700">Closed Jobs</p>
                            <p class="mt-1 text-xs text-gray-500">Successfully completed jobs</p>
                            <a href="javascript:void(0)" onclick="openStatusModal('closedModal')"
                                class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-gray-500 transition hover:text-gray-700">
                                View Records <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>

                        <!-- Pending Invoices -->
                        <div
                            class="group cursor-pointer rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-yellow-300 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-yellow-50 text-yellow-600">
                                    <i class="fa-solid fa-file-invoice-dollar text-lg"></i>
                                </div>
                                <span
                                    class="rounded-full bg-yellow-50 px-2.5 py-1 text-xs font-medium text-yellow-600">Awaiting</span>
                            </div>
                            <h3 class="mt-3 text-2xl font-bold text-gray-800">4</h3>
                            <p class="mt-0.5 text-sm font-semibold text-gray-700">Pending Invoices</p>
                            <p class="mt-1 text-xs text-gray-500">Invoices awaiting payment</p>
                            <a href="javascript:void(0)" onclick="openStatusModal('invoicesModal')"
                                class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-yellow-600 transition hover:text-yellow-700">
                                View Records <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>

                        <!-- Payments Today -->
                        <div
                            class="group cursor-pointer rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-teal-300 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-teal-50 text-teal-600">
                                    <i class="fa-solid fa-money-bill-wave text-lg"></i>
                                </div>
                                <span
                                    class="rounded-full bg-teal-50 px-2.5 py-1 text-xs font-medium text-teal-600">Received</span>
                            </div>
                            <h3 class="mt-3 text-2xl font-bold text-gray-800">8</h3>
                            <p class="mt-0.5 text-sm font-semibold text-gray-700">Payments Today</p>
                            <p class="mt-1 text-xs text-gray-500">Payments received today</p>
                            <a href="javascript:void(0)" onclick="openStatusModal('paymentsModal')"
                                class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-teal-600 transition hover:text-teal-700">
                                View Records <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>

                    </div>

                </section>

                <!-- Quick Actions -->
                <section class="mt-6">

                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

                        <!-- Header -->
                        <div class="border-b border-gray-100 px-5 py-4">

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-100">
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

                                <div
                                    class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-orange-50 transition-all duration-300 group-hover:scale-110 group-hover:bg-orange-100">
                                    <i class="fa-solid fa-user-plus text-3xl text-[#E89000]"></i>
                                </div>

                                <h3
                                    class="text-lg font-semibold text-gray-900 transition-colors group-hover:text-[#E89000]">
                                    Register Client
                                </h3>

                                <p class="mt-2 max-w-xs text-sm leading-relaxed text-gray-500">
                                    Create a new customer profile and store contact details.
                                </p>

                                <div class="mt-5 flex items-center gap-2 text-sm font-semibold text-[#E89000]">
                                    <span>Get Started</span>
                                    <i
                                        class="fa-solid fa-arrow-right text-xs transition-transform duration-300 group-hover:translate-x-1"></i>
                                </div>

                            </a>

                            <!-- View Pending Job Card Requests -->
                            <button type="button" id="openJobCardModal"
                                class="group flex cursor-pointer flex-col items-center rounded-xl border border-gray-200 bg-white p-6 text-center transition-all duration-300 hover:-translate-y-1 hover:border-[#E89000] hover:shadow-lg focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#E89000]">
                                <div
                                    class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-orange-50 transition-all duration-300 group-hover:scale-110 group-hover:bg-orange-100">
                                    <i class="fa-solid fa-clipboard-list text-3xl text-[#E89000]"></i>
                                </div>
                                <h3
                                    class="text-lg font-semibold text-gray-900 transition-colors group-hover:text-[#E89000]">
                                    Pending Requests</h3>
                                <p class="mt-2 max-w-xs text-sm leading-relaxed text-gray-500">Review customer
                                    appointment requests and approve pending services.</p>
                                <div class="mt-5 flex items-center gap-2 text-sm font-semibold text-[#E89000]">
                                    <span>View Requests</span>
                                    <i
                                        class="fa-solid fa-arrow-right text-xs transition-transform duration-300 group-hover:translate-x-1"></i>
                                </div>
                            </button>

                            <!-- Create Invoice -->
                            <a href="create-invoice.php"
                                class="group flex flex-col items-center rounded-xl border border-gray-200 bg-white p-6 text-center transition-all duration-300 hover:-translate-y-1 hover:border-[#E89000] hover:shadow-lg focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#E89000]">

                                <div
                                    class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-orange-50 transition-all duration-300 group-hover:scale-110 group-hover:bg-orange-100">
                                    <i class="fa-solid fa-file-invoice-dollar text-3xl text-[#E89000]"></i>
                                </div>

                                <h3
                                    class="text-lg font-semibold text-gray-900 transition-colors group-hover:text-[#E89000]">
                                    Create Invoice
                                </h3>

                                <p class="mt-2 max-w-xs text-sm leading-relaxed text-gray-500">
                                    Generate a customer invoice for completed vehicle services.
                                </p>

                                <div class="mt-5 flex items-center gap-2 text-sm font-semibold text-[#E89000]">
                                    <span>Get Started</span>
                                    <i
                                        class="fa-solid fa-arrow-right text-xs transition-transform duration-300 group-hover:translate-x-1"></i>
                                </div>

                            </a>

                        </div>

                    </div>

                </section>

            </div>
        </main>
    </div>

    <!-- PENDING APPOINTMENT REQUESTS MODAL -->
    <div id="jobCardModal"
        class="fixed inset-0 z-[100] hidden items-center justify-center bg-gray-950/60 p-3 backdrop-blur-sm sm:p-6"
        role="dialog" aria-modal="true" aria-labelledby="jobCardModalTitle">

        <!-- Modal inner container -->
        <div class="relative flex max-h-[90vh] w-full max-w-7xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">

            <!-- Modal Header -->
            <div class="flex shrink-0 items-center justify-between border-b border-gray-100 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-50">
                        <i class="fa-solid fa-clock text-lg text-orange-500"></i>
                    </div>
                    <div>
                        <h2 id="jobCardModalTitle" class="text-base font-bold text-gray-900">Pending Appointment Requests</h2>
                        <p class="mt-0.5 text-xs text-gray-500"><?= $pendingCount ?> request<?= $pendingCount !== 1 ? 's' : '' ?> awaiting review</p>
                    </div>
                </div>
                <button type="button" data-close-job-modal
                    class="flex h-8 w-8 items-center justify-center rounded-full text-gray-400 transition hover:bg-gray-100 hover:text-gray-700"
                    aria-label="Close modal">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <!-- Flash message (shown only after a POST action) -->
            <?php if (!empty($flashMessage)): ?>
            <div class="shrink-0 px-6 pt-4">
                <div class="flex items-center gap-2 rounded-lg px-4 py-3 text-sm font-medium
                    <?= $flashType === 'success'
                        ? 'bg-green-50 text-green-700 border border-green-200'
                        : 'bg-red-50 text-red-700 border border-red-200' ?>">
                    <i class="fa-solid <?= $flashType === 'success' ? 'fa-circle-check' : 'fa-circle-xmark' ?> text-base"></i>
                    <span><?= htmlspecialchars($flashMessage) ?></span>
                </div>
            </div>
            <?php endif; ?>

            <!-- Scrollable table body -->
            <div class="min-h-0 flex-1 overflow-auto">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1150px] text-left">
                        <thead class="sticky top-0 bg-gray-50 text-xs font-bold uppercase tracking-wider text-gray-600">
                            <tr>
                                <th class="px-4 py-4">Request</th>
                                <th class="px-4 py-4">Customer Details</th>
                                <th class="px-4 py-4">Vehicle Details</th>
                                <th class="px-4 py-4">Requested Services</th>
                                <th class="px-4 py-4">Appointment</th>
                                <th class="px-4 py-4">Assign Mechanic &amp; Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 bg-white">
                            <?php if (!empty($pendingRequests)): ?>
                                <?php foreach ($pendingRequests as $appointment): ?>
                                    <tr class="transition hover:bg-gray-50">

                                        <!-- Request ID -->
                                        <td class="px-4 py-4">
                                            <span class="rounded-lg bg-orange-50 px-3 py-1.5 text-xs font-bold text-[#E89000]">
                                                APT-<?= htmlspecialchars($appointment['id']) ?>
                                            </span>
                                        </td>

                                        <!-- Customer Details -->
                                        <td class="px-4 py-4">
                                            <p class="text-xs font-semibold text-gray-900"><?= htmlspecialchars($appointment['full_name']) ?></p>
                                            <p class="mt-1 text-xs text-gray-500">
                                                <i class="fa-solid fa-phone mr-1"></i><?= htmlspecialchars($appointment['phone']) ?>
                                            </p>
                                            <p class="mt-1 text-xs text-gray-500">
                                                <i class="fa-solid fa-envelope mr-1"></i><?= htmlspecialchars($appointment['email']) ?>
                                            </p>
                                        </td>

                                        <!-- Vehicle Details -->
                                        <td class="px-4 py-4">
                                            <p class="text-xs font-semibold text-gray-900">
                                                <?= htmlspecialchars($appointment['brand'] . ' ' . $appointment['model']) ?>
                                                <span class="font-normal text-gray-500">(<?= htmlspecialchars($appointment['manufacture_year']) ?>)</span>
                                            </p>
                                            <p class="mt-1 text-xs text-gray-500">
                                                <i class="fa-solid fa-hashtag mr-1"></i><?= htmlspecialchars($appointment['vehicle_registration_number']) ?>
                                            </p>
                                        </td>

                                        <!-- Requested Services -->
                                        <td class="px-4 py-4">
                                            <p class="max-w-[180px] whitespace-normal text-xs text-gray-700">
                                                <?= htmlspecialchars($appointment['requested_services'] ?? 'N/A') ?>
                                            </p>
                                        </td>

                                        <!-- Appointment -->
                                        <td class="px-4 py-4">
                                            <p class="text-xs font-semibold text-gray-800">
                                                <i class="fa-regular fa-calendar mr-1 text-[#E89000]"></i><?= htmlspecialchars($appointment['date']) ?>
                                            </p>
                                            <p class="mt-1 text-xs text-gray-500">
                                                <i class="fa-regular fa-clock mr-1"></i><?= htmlspecialchars($appointment['time']) ?>
                                            </p>
                                            <?php if (!empty($appointment['additional_note'])): ?>
                                            <p class="mt-1 text-xs text-gray-400 italic max-w-[140px] whitespace-normal">
                                                <?= htmlspecialchars($appointment['additional_note']) ?>
                                            </p>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Assign Mechanic + Actions (single unified form) -->
                                        <td class="px-4 py-4">
                                            <form method="POST" action="dashboard.php" class="flex min-w-[240px] flex-col gap-2">
                                                <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($appointment['id']) ?>">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                                                <!-- Mechanic dropdown (value picked up by Approve) -->
                                                <select
                                                    name="mechanic_id"
                                                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs text-gray-700 outline-none transition focus:border-[#E89000] focus:ring-2 focus:ring-orange-100"
                                                >
                                                    <option value="">Choose Mechanic</option>
                                                    <?php foreach ($mechanics as $mechanic): ?>
                                                        <option value="<?= htmlspecialchars($mechanic['id']) ?>">
                                                            <?= htmlspecialchars($mechanic['full_name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>

                                                <!-- Approve + Reject side by side -->
                                                <div class="flex gap-2">
                                                    <button
                                                        type="submit"
                                                        name="action"
                                                        value="assign_mechanic"
                                                        <?= empty($mechanics) ? 'disabled' : '' ?>
                                                        onclick="return confirm('Assign the selected mechanic and approve this appointment?');"
                                                        class="flex-1 cursor-pointer rounded-lg bg-green-600 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-green-700 active:scale-95 disabled:cursor-not-allowed disabled:opacity-50"
                                                    >
                                                        <i class="fa-solid fa-check mr-1"></i>Approve
                                                    </button>

                                                    <button
                                                        type="submit"
                                                        name="action"
                                                        value="reject_appointment"
                                                        onclick="return confirm('Are you sure you want to reject this appointment?');"
                                                        class="flex-1 cursor-pointer rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-red-700 active:scale-95"
                                                    >
                                                        <i class="fa-solid fa-xmark mr-1"></i>Reject
                                                    </button>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="px-5 py-16 text-center">
                                        <i class="fa-regular fa-calendar-check mb-3 block text-4xl text-gray-300"></i>
                                        <p class="text-xs font-semibold text-gray-700">No Pending Appointments</p>
                                        <p class="mt-1 text-xs text-gray-500">New appointment requests will appear here.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- PENDING RECORDS MODAL -->
    <div id="pendingModal"
        class="fixed inset-0 z-[100] hidden items-center justify-center bg-gray-950/60 p-3 backdrop-blur-sm sm:p-6"
        role="dialog" aria-modal="true">
        <div class="relative flex max-h-[90vh] w-full max-w-5xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="flex shrink-0 items-center justify-between border-b border-gray-100 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-50">
                        <i class="fa-solid fa-clock text-lg text-orange-500"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Pending Appointment Records</h2>
                        <p class="mt-0.5 text-xs text-gray-500"><?= $pendingCount ?> record<?= $pendingCount !== 1 ? 's' : '' ?> found</p>
                    </div>
                </div>
                <button type="button" onclick="closeStatusModal('pendingModal')"
                    class="flex h-8 w-8 items-center justify-center rounded-full text-gray-400 transition hover:bg-gray-100 hover:text-gray-700 cursor-pointer"
                    aria-label="Close modal">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>
            <div class="min-h-0 flex-1 overflow-auto">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[900px] text-left">
                        <thead class="sticky top-0 bg-gray-50 text-xs font-bold uppercase tracking-wider text-gray-600">
                            <tr>
                                <th class="px-4 py-4">ID</th>
                                <th class="px-4 py-4">Customer</th>
                                <th class="px-4 py-4">Vehicle</th>
                                <th class="px-4 py-4">Services</th>
                                <th class="px-4 py-4">Date / Time</th>
                                <th class="px-4 py-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <?php if (!empty($pendingRequests)): ?>
                                <?php foreach ($pendingRequests as $r): ?>
                                    <tr class="transition hover:bg-gray-50">
                                        <td class="px-4 py-4"><span class="rounded-lg bg-orange-50 px-3 py-1.5 text-xs font-bold text-orange-600">APT-<?= htmlspecialchars($r['id']) ?></span></td>
                                        <td class="px-4 py-4">
                                            <p class="text-xs font-semibold text-gray-900"><?= htmlspecialchars($r['full_name']) ?></p>
                                            <p class="mt-1 text-xs text-gray-500"><i class="fa-solid fa-phone mr-1"></i><?= htmlspecialchars($r['phone']) ?></p>
                                        </td>
                                        <td class="px-4 py-4">
                                            <p class="text-xs font-semibold text-gray-900"><?= htmlspecialchars($r['brand'] . ' ' . $r['model']) ?> <span class="font-normal text-gray-500">(<?= htmlspecialchars($r['manufacture_year']) ?>)</span></p>
                                            <p class="mt-1 text-xs text-gray-500"><i class="fa-solid fa-hashtag mr-1"></i><?= htmlspecialchars($r['vehicle_registration_number']) ?></p>
                                        </td>
                                        <td class="px-4 py-4"><p class="max-w-[180px] whitespace-normal text-xs text-gray-700"><?= htmlspecialchars($r['requested_services'] ?? 'N/A') ?></p></td>
                                        <td class="px-4 py-4">
                                            <p class="text-xs font-semibold text-gray-800"><i class="fa-regular fa-calendar mr-1 text-orange-500"></i><?= htmlspecialchars($r['date']) ?></p>
                                            <p class="mt-1 text-xs text-gray-500"><i class="fa-regular fa-clock mr-1"></i><?= htmlspecialchars($r['time']) ?></p>
                                        </td>
                                        <td class="px-4 py-4"><span class="rounded-full bg-orange-50 px-2.5 py-1 text-xs font-medium text-orange-600">Pending</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="px-5 py-16 text-center">
                                    <i class="fa-regular fa-calendar-check mb-3 block text-4xl text-gray-300"></i>
                                    <p class="text-xs font-semibold text-gray-700">No Pending Records</p>
                                    <p class="mt-1 text-xs text-gray-500">No pending appointments found.</p>
                                </td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ACCEPTED RECORDS MODAL -->
    <div id="acceptedModal"
        class="fixed inset-0 z-[100] hidden items-center justify-center bg-gray-950/60 p-3 backdrop-blur-sm sm:p-6"
        role="dialog" aria-modal="true">
        <div class="relative flex max-h-[90vh] w-full max-w-5xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="flex shrink-0 items-center justify-between border-b border-gray-100 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-50">
                        <i class="fa-solid fa-check-circle text-lg text-green-500"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Accepted Appointment Records</h2>
                        <p class="mt-0.5 text-xs text-gray-500"><?= $approvedCount ?> record<?= $approvedCount !== 1 ? 's' : '' ?> found</p>
                    </div>
                </div>
                <button type="button" onclick="closeStatusModal('acceptedModal')"
                    class="flex h-8 w-8 items-center justify-center rounded-full text-gray-400 transition hover:bg-gray-100 hover:text-gray-700 cursor-pointer"
                    aria-label="Close modal">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>
            <div class="min-h-0 flex-1 overflow-auto">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[900px] text-left">
                        <thead class="sticky top-0 bg-gray-50 text-xs font-bold uppercase tracking-wider text-gray-600">
                            <tr>
                                <th class="px-4 py-4">ID</th>
                                <th class="px-4 py-4">Customer</th>
                                <th class="px-4 py-4">Vehicle</th>
                                <th class="px-4 py-4">Services</th>
                                <th class="px-4 py-4">Date / Time</th>
                                <th class="px-4 py-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <?php if (!empty($acceptedRecords)): ?>
                                <?php foreach ($acceptedRecords as $r): ?>
                                    <tr class="transition hover:bg-gray-50">
                                        <td class="px-4 py-4"><span class="rounded-lg bg-green-50 px-3 py-1.5 text-xs font-bold text-green-600">APT-<?= htmlspecialchars($r['id']) ?></span></td>
                                        <td class="px-4 py-4">
                                            <p class="text-xs font-semibold text-gray-900"><?= htmlspecialchars($r['full_name']) ?></p>
                                            <p class="mt-1 text-xs text-gray-500"><i class="fa-solid fa-phone mr-1"></i><?= htmlspecialchars($r['phone']) ?></p>
                                        </td>
                                        <td class="px-4 py-4">
                                            <p class="text-xs font-semibold text-gray-900"><?= htmlspecialchars($r['brand'] . ' ' . $r['model']) ?> <span class="font-normal text-gray-500">(<?= htmlspecialchars($r['manufacture_year']) ?>)</span></p>
                                            <p class="mt-1 text-xs text-gray-500"><i class="fa-solid fa-hashtag mr-1"></i><?= htmlspecialchars($r['vehicle_registration_number']) ?></p>
                                        </td>
                                        <td class="px-4 py-4"><p class="max-w-[180px] whitespace-normal text-xs text-gray-700"><?= htmlspecialchars($r['requested_services'] ?? 'N/A') ?></p></td>
                                        <td class="px-4 py-4">
                                            <p class="text-xs font-semibold text-gray-800"><i class="fa-regular fa-calendar mr-1 text-green-500"></i><?= htmlspecialchars($r['date']) ?></p>
                                            <p class="mt-1 text-xs text-gray-500"><i class="fa-regular fa-clock mr-1"></i><?= htmlspecialchars($r['time']) ?></p>
                                        </td>
                                        <td class="px-4 py-4"><span class="rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-600"><?= ucfirst(htmlspecialchars($r['status'])) ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="px-5 py-16 text-center">
                                    <i class="fa-regular fa-calendar-check mb-3 block text-4xl text-gray-300"></i>
                                    <p class="text-xs font-semibold text-gray-700">No Accepted Records</p>
                                    <p class="mt-1 text-xs text-gray-500">No accepted appointments found.</p>
                                </td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- REJECTED RECORDS MODAL -->
    <div id="rejectedModal"
        class="fixed inset-0 z-[100] hidden items-center justify-center bg-gray-950/60 p-3 backdrop-blur-sm sm:p-6"
        role="dialog" aria-modal="true">
        <div class="relative flex max-h-[90vh] w-full max-w-5xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="flex shrink-0 items-center justify-between border-b border-gray-100 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-50">
                        <i class="fa-solid fa-circle-xmark text-lg text-red-500"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Rejected Appointment Records</h2>
                        <p class="mt-0.5 text-xs text-gray-500"><?= $rejectedCount ?> record<?= $rejectedCount !== 1 ? 's' : '' ?> found</p>
                    </div>
                </div>
                <button type="button" onclick="closeStatusModal('rejectedModal')"
                    class="flex h-8 w-8 items-center justify-center rounded-full text-gray-400 transition hover:bg-gray-100 hover:text-gray-700 cursor-pointer"
                    aria-label="Close modal">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>
            <div class="min-h-0 flex-1 overflow-auto">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[900px] text-left">
                        <thead class="sticky top-0 bg-gray-50 text-xs font-bold uppercase tracking-wider text-gray-600">
                            <tr>
                                <th class="px-4 py-4">ID</th>
                                <th class="px-4 py-4">Customer</th>
                                <th class="px-4 py-4">Vehicle</th>
                                <th class="px-4 py-4">Services</th>
                                <th class="px-4 py-4">Date / Time</th>
                                <th class="px-4 py-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <?php if (!empty($rejectedRecords)): ?>
                                <?php foreach ($rejectedRecords as $r): ?>
                                    <tr class="transition hover:bg-gray-50">
                                        <td class="px-4 py-4"><span class="rounded-lg bg-red-50 px-3 py-1.5 text-xs font-bold text-red-600">APT-<?= htmlspecialchars($r['id']) ?></span></td>
                                        <td class="px-4 py-4">
                                            <p class="text-xs font-semibold text-gray-900"><?= htmlspecialchars($r['full_name']) ?></p>
                                            <p class="mt-1 text-xs text-gray-500"><i class="fa-solid fa-phone mr-1"></i><?= htmlspecialchars($r['phone']) ?></p>
                                        </td>
                                        <td class="px-4 py-4">
                                            <p class="text-xs font-semibold text-gray-900"><?= htmlspecialchars($r['brand'] . ' ' . $r['model']) ?> <span class="font-normal text-gray-500">(<?= htmlspecialchars($r['manufacture_year']) ?>)</span></p>
                                            <p class="mt-1 text-xs text-gray-500"><i class="fa-solid fa-hashtag mr-1"></i><?= htmlspecialchars($r['vehicle_registration_number']) ?></p>
                                        </td>
                                        <td class="px-4 py-4"><p class="max-w-[180px] whitespace-normal text-xs text-gray-700"><?= htmlspecialchars($r['requested_services'] ?? 'N/A') ?></p></td>
                                        <td class="px-4 py-4">
                                            <p class="text-xs font-semibold text-gray-800"><i class="fa-regular fa-calendar mr-1 text-red-500"></i><?= htmlspecialchars($r['date']) ?></p>
                                            <p class="mt-1 text-xs text-gray-500"><i class="fa-regular fa-clock mr-1"></i><?= htmlspecialchars($r['time']) ?></p>
                                        </td>
                                        <td class="px-4 py-4"><span class="rounded-full bg-red-50 px-2.5 py-1 text-xs font-medium text-red-600">Rejected</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="px-5 py-16 text-center">
                                    <i class="fa-regular fa-calendar-check mb-3 block text-4xl text-gray-300"></i>
                                    <p class="text-xs font-semibold text-gray-700">No Rejected Records</p>
                                    <p class="mt-1 text-xs text-gray-500">No rejected appointments found.</p>
                                </td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ASSIGNED JOBS MODAL -->
    <div id="assignedModal"
        class="fixed inset-0 z-[100] hidden items-center justify-center bg-gray-950/60 p-3 backdrop-blur-sm sm:p-6"
        role="dialog" aria-modal="true">
        <div class="relative flex max-h-[90vh] w-full max-w-5xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="flex shrink-0 items-center justify-between border-b border-gray-100 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50">
                        <i class="fa-solid fa-clipboard-list text-lg text-blue-500"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Assigned Job Records</h2>
                        <p class="mt-0.5 text-xs text-gray-500"><?= $assignedCount ?> record<?= $assignedCount !== 1 ? 's' : '' ?> found</p>
                    </div>
                </div>
                <button type="button" onclick="closeStatusModal('assignedModal')"
                    class="flex h-8 w-8 items-center justify-center rounded-full text-gray-400 transition hover:bg-gray-100 hover:text-gray-700 cursor-pointer"
                    aria-label="Close modal">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>
            <div class="min-h-0 flex-1 overflow-auto">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1000px] text-left">
                        <thead class="sticky top-0 bg-gray-50 text-xs font-bold uppercase tracking-wider text-gray-600">
                            <tr>
                                <th class="px-4 py-4">Job Card</th>
                                <th class="px-4 py-4">Appointment</th>
                                <th class="px-4 py-4">Customer</th>
                                <th class="px-4 py-4">Vehicle</th>
                                <th class="px-4 py-4">Mechanic</th>
                                <th class="px-4 py-4">Date / Time</th>
                                <th class="px-4 py-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <?php if (!empty($assignedRecords)): ?>
                                <?php foreach ($assignedRecords as $r): ?>
                                    <tr class="transition hover:bg-gray-50">
                                        <td class="px-4 py-4"><span class="rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-600">JC-<?= htmlspecialchars($r['job_card_id']) ?></span></td>
                                        <td class="px-4 py-4"><span class="text-xs font-medium text-gray-700"><?= $r['appointment_id'] ? 'APT-' . htmlspecialchars($r['appointment_id']) : 'N/A' ?></span></td>
                                        <td class="px-4 py-4">
                                            <p class="text-xs font-semibold text-gray-900"><?= htmlspecialchars($r['customer_name'] ?? 'N/A') ?></p>
                                            <?php if (!empty($r['customer_phone'])): ?>
                                            <p class="mt-1 text-xs text-gray-500"><i class="fa-solid fa-phone mr-1"></i><?= htmlspecialchars($r['customer_phone']) ?></p>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-4">
                                            <?php if (!empty($r['brand'])): ?>
                                            <p class="text-xs font-semibold text-gray-900"><?= htmlspecialchars($r['brand'] . ' ' . $r['model']) ?> <span class="font-normal text-gray-500">(<?= htmlspecialchars($r['manufacture_year']) ?>)</span></p>
                                            <p class="mt-1 text-xs text-gray-500"><i class="fa-solid fa-hashtag mr-1"></i><?= htmlspecialchars($r['vehicle_registration_number']) ?></p>
                                            <?php else: ?>
                                            <p class="text-xs text-gray-500">N/A</p>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-4"><p class="text-xs font-semibold text-gray-900"><?= htmlspecialchars($r['mechanic_name'] ?? 'N/A') ?></p></td>
                                        <td class="px-4 py-4">
                                            <?php if (!empty($r['date'])): ?>
                                            <p class="text-xs font-semibold text-gray-800"><i class="fa-regular fa-calendar mr-1 text-blue-500"></i><?= htmlspecialchars($r['date']) ?></p>
                                            <p class="mt-1 text-xs text-gray-500"><i class="fa-regular fa-clock mr-1"></i><?= htmlspecialchars($r['time']) ?></p>
                                            <?php else: ?>
                                            <p class="text-xs text-gray-500">N/A</p>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-4"><span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-600">Assigned</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="px-5 py-16 text-center">
                                    <i class="fa-solid fa-clipboard-list mb-3 block text-4xl text-gray-300"></i>
                                    <p class="text-xs font-semibold text-gray-700">No Assigned Jobs</p>
                                    <p class="mt-1 text-xs text-gray-500">No assigned job cards found.</p>
                                </td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- IN PROGRESS JOBS MODAL -->
    <div id="inProgressModal"
        class="fixed inset-0 z-[100] hidden items-center justify-center bg-gray-950/60 p-3 backdrop-blur-sm sm:p-6"
        role="dialog" aria-modal="true">
        <div class="relative flex max-h-[90vh] w-full max-w-5xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="flex shrink-0 items-center justify-between border-b border-gray-100 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-50">
                        <i class="fa-solid fa-gears text-lg text-purple-500"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900">In Progress Job Records</h2>
                        <p class="mt-0.5 text-xs text-gray-500"><?= $inProgressCount ?> record<?= $inProgressCount !== 1 ? 's' : '' ?> found</p>
                    </div>
                </div>
                <button type="button" onclick="closeStatusModal('inProgressModal')"
                    class="flex h-8 w-8 items-center justify-center rounded-full text-gray-400 transition hover:bg-gray-100 hover:text-gray-700 cursor-pointer"
                    aria-label="Close modal">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>
            <div class="min-h-0 flex-1 overflow-auto">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1000px] text-left">
                        <thead class="sticky top-0 bg-gray-50 text-xs font-bold uppercase tracking-wider text-gray-600">
                            <tr>
                                <th class="px-4 py-4">Job Card</th>
                                <th class="px-4 py-4">Appointment</th>
                                <th class="px-4 py-4">Customer</th>
                                <th class="px-4 py-4">Vehicle</th>
                                <th class="px-4 py-4">Mechanic</th>
                                <th class="px-4 py-4">Date / Time</th>
                                <th class="px-4 py-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <?php if (!empty($inProgressRecords)): ?>
                                <?php foreach ($inProgressRecords as $r): ?>
                                    <tr class="transition hover:bg-gray-50">
                                        <td class="px-4 py-4"><span class="rounded-lg bg-purple-50 px-3 py-1.5 text-xs font-bold text-purple-600">JC-<?= htmlspecialchars($r['job_card_id']) ?></span></td>
                                        <td class="px-4 py-4"><span class="text-xs font-medium text-gray-700"><?= $r['appointment_id'] ? 'APT-' . htmlspecialchars($r['appointment_id']) : 'N/A' ?></span></td>
                                        <td class="px-4 py-4">
                                            <p class="text-xs font-semibold text-gray-900"><?= htmlspecialchars($r['customer_name'] ?? 'N/A') ?></p>
                                            <?php if (!empty($r['customer_phone'])): ?>
                                            <p class="mt-1 text-xs text-gray-500"><i class="fa-solid fa-phone mr-1"></i><?= htmlspecialchars($r['customer_phone']) ?></p>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-4">
                                            <?php if (!empty($r['brand'])): ?>
                                            <p class="text-xs font-semibold text-gray-900"><?= htmlspecialchars($r['brand'] . ' ' . $r['model']) ?> <span class="font-normal text-gray-500">(<?= htmlspecialchars($r['manufacture_year']) ?>)</span></p>
                                            <p class="mt-1 text-xs text-gray-500"><i class="fa-solid fa-hashtag mr-1"></i><?= htmlspecialchars($r['vehicle_registration_number']) ?></p>
                                            <?php else: ?>
                                            <p class="text-xs text-gray-500">N/A</p>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-4"><p class="text-xs font-semibold text-gray-900"><?= htmlspecialchars($r['mechanic_name'] ?? 'N/A') ?></p></td>
                                        <td class="px-4 py-4">
                                            <?php if (!empty($r['date'])): ?>
                                            <p class="text-xs font-semibold text-gray-800"><i class="fa-regular fa-calendar mr-1 text-purple-500"></i><?= htmlspecialchars($r['date']) ?></p>
                                            <p class="mt-1 text-xs text-gray-500"><i class="fa-regular fa-clock mr-1"></i><?= htmlspecialchars($r['time']) ?></p>
                                            <?php else: ?>
                                            <p class="text-xs text-gray-500">N/A</p>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-4"><span class="rounded-full bg-purple-50 px-2.5 py-1 text-xs font-medium text-purple-600">In Progress</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="px-5 py-16 text-center">
                                    <i class="fa-solid fa-gears mb-3 block text-4xl text-gray-300"></i>
                                    <p class="text-xs font-semibold text-gray-700">No In-Progress Jobs</p>
                                    <p class="mt-1 text-xs text-gray-500">No jobs currently in progress.</p>
                                </td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- CLOSED JOBS MODAL -->
    <div id="closedModal"
        class="fixed inset-0 z-[100] hidden items-center justify-center bg-gray-950/60 p-3 backdrop-blur-sm sm:p-6"
        role="dialog" aria-modal="true">
        <div class="relative flex max-h-[90vh] w-full max-w-5xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="flex shrink-0 items-center justify-between border-b border-gray-100 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100">
                        <i class="fa-solid fa-circle-check text-lg text-gray-600"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Closed Job Records</h2>
                        <p class="mt-0.5 text-xs text-gray-500"><?= $closedCount ?> record<?= $closedCount !== 1 ? 's' : '' ?> found</p>
                    </div>
                </div>
                <button type="button" onclick="closeStatusModal('closedModal')"
                    class="flex h-8 w-8 items-center justify-center rounded-full text-gray-400 transition hover:bg-gray-100 hover:text-gray-700 cursor-pointer"
                    aria-label="Close modal">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>
            <div class="min-h-0 flex-1 overflow-auto">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1000px] text-left">
                        <thead class="sticky top-0 bg-gray-50 text-xs font-bold uppercase tracking-wider text-gray-600">
                            <tr>
                                <th class="px-4 py-4">Job Card</th>
                                <th class="px-4 py-4">Appointment</th>
                                <th class="px-4 py-4">Customer</th>
                                <th class="px-4 py-4">Vehicle</th>
                                <th class="px-4 py-4">Mechanic</th>
                                <th class="px-4 py-4">Date / Time</th>
                                <th class="px-4 py-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <?php if (!empty($closedRecords)): ?>
                                <?php foreach ($closedRecords as $r): ?>
                                    <tr class="transition hover:bg-gray-50">
                                        <td class="px-4 py-4"><span class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-bold text-gray-600">JC-<?= htmlspecialchars($r['job_card_id']) ?></span></td>
                                        <td class="px-4 py-4"><span class="text-xs font-medium text-gray-700"><?= $r['appointment_id'] ? 'APT-' . htmlspecialchars($r['appointment_id']) : 'N/A' ?></span></td>
                                        <td class="px-4 py-4">
                                            <p class="text-xs font-semibold text-gray-900"><?= htmlspecialchars($r['customer_name'] ?? 'N/A') ?></p>
                                            <?php if (!empty($r['customer_phone'])): ?>
                                            <p class="mt-1 text-xs text-gray-500"><i class="fa-solid fa-phone mr-1"></i><?= htmlspecialchars($r['customer_phone']) ?></p>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-4">
                                            <?php if (!empty($r['brand'])): ?>
                                            <p class="text-xs font-semibold text-gray-900"><?= htmlspecialchars($r['brand'] . ' ' . $r['model']) ?> <span class="font-normal text-gray-500">(<?= htmlspecialchars($r['manufacture_year']) ?>)</span></p>
                                            <p class="mt-1 text-xs text-gray-500"><i class="fa-solid fa-hashtag mr-1"></i><?= htmlspecialchars($r['vehicle_registration_number']) ?></p>
                                            <?php else: ?>
                                            <p class="text-xs text-gray-500">N/A</p>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-4"><p class="text-xs font-semibold text-gray-900"><?= htmlspecialchars($r['mechanic_name'] ?? 'N/A') ?></p></td>
                                        <td class="px-4 py-4">
                                            <?php if (!empty($r['date'])): ?>
                                            <p class="text-xs font-semibold text-gray-800"><i class="fa-regular fa-calendar mr-1 text-gray-500"></i><?= htmlspecialchars($r['date']) ?></p>
                                            <p class="mt-1 text-xs text-gray-500"><i class="fa-regular fa-clock mr-1"></i><?= htmlspecialchars($r['time']) ?></p>
                                            <?php else: ?>
                                            <p class="text-xs text-gray-500">N/A</p>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-4"><span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">Completed</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="px-5 py-16 text-center">
                                    <i class="fa-solid fa-circle-check mb-3 block text-4xl text-gray-300"></i>
                                    <p class="text-xs font-semibold text-gray-700">No Closed Jobs</p>
                                    <p class="mt-1 text-xs text-gray-500">No completed jobs found.</p>
                                </td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- PENDING INVOICES MODAL -->
    <div id="invoicesModal"
        class="fixed inset-0 z-[100] hidden items-center justify-center bg-gray-950/60 p-3 backdrop-blur-sm sm:p-6"
        role="dialog" aria-modal="true">
        <div class="relative flex max-h-[90vh] w-full max-w-lg flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="flex shrink-0 items-center justify-between border-b border-gray-100 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-yellow-50">
                        <i class="fa-solid fa-file-invoice-dollar text-lg text-yellow-600"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Pending Invoices</h2>
                        <p class="mt-0.5 text-xs text-gray-500">Invoice records</p>
                    </div>
                </div>
                <button type="button" onclick="closeStatusModal('invoicesModal')"
                    class="flex h-8 w-8 items-center justify-center rounded-full text-gray-400 transition hover:bg-gray-100 hover:text-gray-700 cursor-pointer"
                    aria-label="Close modal">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>
            <div class="px-6 py-16 text-center">
                <i class="fa-solid fa-file-invoice-dollar mb-3 block text-4xl text-gray-300"></i>
                <p class="text-xs font-semibold text-gray-700">No Invoice Data Available</p>
                <p class="mt-1 text-xs text-gray-500">Invoice records will appear here once the invoices module is set up.</p>
            </div>
        </div>
    </div>

    <!-- PAYMENTS TODAY MODAL -->
    <div id="paymentsModal"
        class="fixed inset-0 z-[100] hidden items-center justify-center bg-gray-950/60 p-3 backdrop-blur-sm sm:p-6"
        role="dialog" aria-modal="true">
        <div class="relative flex max-h-[90vh] w-full max-w-lg flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="flex shrink-0 items-center justify-between border-b border-gray-100 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-teal-50">
                        <i class="fa-solid fa-money-bill-wave text-lg text-teal-600"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Payments Today</h2>
                        <p class="mt-0.5 text-xs text-gray-500">Payment records</p>
                    </div>
                </div>
                <button type="button" onclick="closeStatusModal('paymentsModal')"
                    class="flex h-8 w-8 items-center justify-center rounded-full text-gray-400 transition hover:bg-gray-100 hover:text-gray-700 cursor-pointer"
                    aria-label="Close modal">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>
            <div class="px-6 py-16 text-center">
                <i class="fa-solid fa-money-bill-wave mb-3 block text-4xl text-gray-300"></i>
                <p class="text-xs font-semibold text-gray-700">No Payment Data Available</p>
                <p class="mt-1 text-xs text-gray-500">Payment records will appear here once the payments module is set up.</p>
            </div>
        </div>
    </div>

    <script>
        /* ── Existing Pending Appointment Requests (action) Modal ── */
        const jobCardModal = document.getElementById("jobCardModal");
        const openJobCardModal = document.getElementById("openJobCardModal");

        function openModal() {
            if (!jobCardModal) return;
            jobCardModal.classList.remove("hidden");
            jobCardModal.classList.add("flex");
            document.body.classList.add("overflow-hidden");
        }

        function closeModal() {
            if (!jobCardModal) return;
            jobCardModal.classList.add("hidden");
            jobCardModal.classList.remove("flex");
            document.body.classList.remove("overflow-hidden");
        }

        openJobCardModal?.addEventListener("click", openModal);

        document.querySelectorAll("[data-close-job-modal]").forEach(button => {
            button.addEventListener("click", closeModal);
        });

        jobCardModal?.addEventListener("click", event => {
            if (event.target === jobCardModal) closeModal();
        });

        /* ── Generic Status Modals (View Records) ── */
        const statusModalIds = [
            "pendingModal", "acceptedModal", "rejectedModal",
            "assignedModal", "inProgressModal", "closedModal",
            "invoicesModal", "paymentsModal"
        ];

        function openStatusModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;
            modal.classList.remove("hidden");
            modal.classList.add("flex");
            document.body.classList.add("overflow-hidden");
        }

        function closeStatusModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;
            modal.classList.add("hidden");
            modal.classList.remove("flex");
            document.body.classList.remove("overflow-hidden");
        }

        // Close status modals on backdrop click
        statusModalIds.forEach(id => {
            const modal = document.getElementById(id);
            if (!modal) return;
            modal.addEventListener("click", event => {
                if (event.target === modal) closeStatusModal(id);
            });
        });

        // Close any open modal on Escape key
        document.addEventListener("keydown", event => {
            if (event.key === "Escape") {
                // Close the existing job card action modal
                if (jobCardModal && !jobCardModal.classList.contains("hidden")) {
                    closeModal();
                }
                // Close any open status modal
                statusModalIds.forEach(id => {
                    const modal = document.getElementById(id);
                    if (modal && !modal.classList.contains("hidden")) {
                        closeStatusModal(id);
                    }
                });
            }
        });

        // Reopen the requests modal after an approval POST so the receptionist
        // can immediately see the refreshed pending-request list.
        <?php if ($reopenModal): ?>
            openModal();
        <?php endif; ?>
    </script>
</body>

</html>