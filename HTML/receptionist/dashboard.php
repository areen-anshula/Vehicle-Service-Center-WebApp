<?php
$required_role = "receptionist";
require_once "../../PHP/auth_guard.php";

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
$reopenAcceptedModal = false;
$reopenAssignedModal = false;
$reopenInvoicesModal = false;
$reopenPaymentsModal = false;

// Reopen Today's Appointments Modal after an action
$reopenTodayAppointmentsModal = (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['return_to']) &&
    $_POST['return_to'] === 'today_appointments'
);

// Reopen Pending Modal after an action
$reopenPendingModal = (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['return_to']) &&
    $_POST['return_to'] === 'pendingModal'
);

/* Handle form submissions: Approve, reject, assign mechanic, create invoice, or mark as paid. */

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    in_array($_POST['action'] ?? '', [
        'approve_appointment',
        'reject_appointment',
        'assign_mechanic',
        'assign_accepted_appointment',
        'create_invoice',
        'mark_as_paid'
    ], true)
) {
    $postedToken = $_POST['csrf_token'] ?? '';
    $action = $_POST['action'];

    // Reopen the modal that submitted the form, not every modal at once.
    $reopenModal = (
        $action !== 'assign_accepted_appointment' &&
        $action !== 'create_invoice' &&
        $action !== 'mark_as_paid' &&
        !$reopenTodayAppointmentsModal &&
        !$reopenPendingModal
    );

    if (!hash_equals($_SESSION['csrf_token'], $postedToken)) {
        $flashMessage = 'Security validation failed. Please refresh the page and try again.';
        $flashType = 'error';
    } else {

        if ($action === 'create_invoice') {
            
            $jobCardId = filter_input(INPUT_POST, 'job_card_id', FILTER_VALIDATE_INT);
            $totalParts = filter_input(INPUT_POST, 'total_parts', FILTER_VALIDATE_FLOAT) ?: 0.00;
            $totalServices = filter_input(INPUT_POST, 'total_services', FILTER_VALIDATE_FLOAT) ?: 0.00;
            $discount = filter_input(INPUT_POST, 'discount', FILTER_VALIDATE_FLOAT) ?: 0.00;
            $finalAmount = filter_input(INPUT_POST, 'final_amount', FILTER_VALIDATE_FLOAT) ?: 0.00;

            if (!$jobCardId) {
                $flashMessage = 'Invalid job card selected.';
                $flashType = 'error';
            } else {
                try {
                    $subtotal = $totalParts + $totalServices;
                    
                    // Insert the invoice record
                    $invStmt = $conn->prepare("
                        INSERT INTO invoices (job_card_id, total_parts, total_services, subtotal, discount, final_amount, payment_status) 
                        VALUES (?, ?, ?, ?, ?, ?, 'Pending')
                    ");
                    $invStmt->bind_param("iddddd", $jobCardId, $totalParts, $totalServices, $subtotal, $discount, $finalAmount);
                    
                    if ($invStmt->execute()) {
                        $flashMessage = "Invoice for Job Card JC-{$jobCardId} created and saved successfully.";
                        $flashType = "success";
                        $reopenInvoicesModal = true;
                    } else {
                        throw new Exception("Unable to save the invoice.");
                    }
                    $invStmt->close();
                    
                } catch (Throwable $e) {
                    $flashMessage = "Error creating invoice: " . $e->getMessage();
                    $flashType = "error";
                    $reopenInvoicesModal = true;
                }
            }

        } elseif ($action === 'mark_as_paid') {
            
            $invoiceId = filter_input(INPUT_POST, 'invoice_id', FILTER_VALIDATE_INT);
            if (!$invoiceId) {
                $flashMessage = 'Invalid invoice selected.';
                $flashType = 'error';
            } else {
                try {
                    $stmt = $conn->prepare("UPDATE invoices SET payment_status = 'Paid' WHERE id = ?");
                    $stmt->bind_param("i", $invoiceId);
                    
                    if ($stmt->execute()) {
                        $flashMessage = "Invoice INV-{$invoiceId} has been successfully marked as Paid.";
                        $flashType = "success";
                        $reopenPaymentsModal = true;
                    } else {
                        throw new Exception("Unable to update invoice status.");
                    }
                    $stmt->close();
                } catch (Throwable $e) {
                    $flashMessage = "Error updating invoice: " . $e->getMessage();
                    $flashType = "error";
                    $reopenPaymentsModal = true;
                }
            }

        } else {
            // Existing appointment/assignment logic
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

                        $mechanicStmt = $conn->prepare("SELECT id FROM users WHERE id = ? AND LOWER(role) = 'mechanic'");
                        $mechanicStmt->bind_param("i", $mechanicId);
                        $mechanicStmt->execute();
                        $mechanicResult = $mechanicStmt->get_result();
                        $validMechanic = $mechanicResult->num_rows === 1;
                        $mechanicStmt->close();

                        if (!$validMechanic) {
                            throw new Exception('The selected mechanic does not exist.');
                        }

                        $appointmentStmt = $conn->prepare("SELECT id FROM appointment WHERE id = ? AND LOWER(status) = 'pending' FOR UPDATE");
                        $appointmentStmt->bind_param("i", $appointmentId);
                        $appointmentStmt->execute();
                        $appointmentResult = $appointmentStmt->get_result();
                        $validAppointment = $appointmentResult->num_rows === 1;
                        $appointmentStmt->close();

                        if (!$validAppointment) {
                            throw new Exception('This appointment is no longer pending.');
                        }

                        $existingStmt = $conn->prepare("SELECT id FROM job_cards WHERE appointment_id = ? LIMIT 1");
                        $existingStmt->bind_param("i", $appointmentId);
                        $existingStmt->execute();
                        $existingResult = $existingStmt->get_result();
                        $jobCardExists = $existingResult->num_rows > 0;
                        $existingStmt->close();

                        if ($jobCardExists) {
                            throw new Exception('A job card has already been created for this appointment.');
                        }

                        $insertStmt = $conn->prepare("INSERT INTO job_cards (appointment_id, mechanic_id, status) VALUES (?, ?, 'Assigned')");
                        $insertStmt->bind_param("ii", $appointmentId, $mechanicId);
                        $insertStmt->execute();
                        $jobCardId = $conn->insert_id;
                        $insertStmt->close();

                        $copyServices = $conn->prepare("INSERT INTO job_card_services (job_card_id, service_id, service_price) SELECT ?, service_id, service_price FROM appointment_services WHERE appointment_id = ?");
                        $copyServices->bind_param("ii", $jobCardId, $appointmentId);
                        $copyServices->execute();
                        $copyServices->close();

                        $updateStmt = $conn->prepare("UPDATE appointment SET status = 'assigned' WHERE id = ? AND LOWER(status) = 'pending'");
                        $updateStmt->bind_param("i", $appointmentId);
                        $updateStmt->execute();

                        if ($updateStmt->affected_rows !== 1) {
                            throw new Exception('Unable to update the appointment status.');
                        }
                        $updateStmt->close();

                        $conn->commit();

                        $flashMessage = "Appointment APT-" . $appointmentId . " has been assigned to the mechanic successfully and job card created.";
                        $flashType = 'success';
                        $reopenAssignedModal = true;
                        $reopenModal = false;

                    } catch (Throwable $e) {
                        $conn->rollback();
                        $flashMessage = $e instanceof Exception ? $e->getMessage() : 'Unable to assign the mechanic. Please try again.';
                        $flashType = 'error';
                    }
                }

            } elseif ($action === "assign_accepted_appointment") {
                $appointmentId = (int) ($_POST["appointment_id"] ?? 0);
                $mechanicId = (int) ($_POST["mechanic_id"] ?? 0);

                try {
                    if ($appointmentId <= 0 || $mechanicId <= 0) {
                        throw new Exception("Please select a valid appointment and mechanic.");
                    }
                    $conn->begin_transaction();

                    $mechanicStmt = $conn->prepare("SELECT id FROM users WHERE id = ? AND LOWER(role) = 'mechanic'");
                    $mechanicStmt->bind_param("i", $mechanicId);
                    $mechanicStmt->execute();
                    $mechanicResult = $mechanicStmt->get_result();
                    $validMechanic = $mechanicResult->num_rows === 1;
                    $mechanicStmt->close();

                    if (!$validMechanic) throw new Exception("Selected mechanic does not exist.");

                    $appointmentStmt = $conn->prepare("SELECT id, status FROM appointment WHERE id = ? FOR UPDATE");
                    $appointmentStmt->bind_param("i", $appointmentId);
                    $appointmentStmt->execute();
                    $appointment = $appointmentStmt->get_result()->fetch_assoc();
                    $appointmentStmt->close();

                    if (!$appointment || strtolower($appointment["status"]) !== "approved") {
                        throw new Exception("This appointment is not available for assignment.");
                    }

                    $checkStmt = $conn->prepare("SELECT id FROM job_cards WHERE appointment_id = ?");
                    $checkStmt->bind_param("i", $appointmentId);
                    $checkStmt->execute();
                    $existingJobCard = $checkStmt->get_result()->fetch_assoc();
                    $checkStmt->close();

                    if ($existingJobCard) throw new Exception("This appointment has already been assigned.");

                    $insertJobCard = $conn->prepare("INSERT INTO job_cards (appointment_id, mechanic_id, status) VALUES (?, ?, 'Assigned')");
                    $insertJobCard->bind_param("ii", $appointmentId, $mechanicId);
                    $insertJobCard->execute();
                    $jobCardId = $conn->insert_id;
                    $insertJobCard->close();

                    $copyServices = $conn->prepare("INSERT INTO job_card_services (job_card_id, service_id, service_price) SELECT ?, service_id, service_price FROM appointment_services WHERE appointment_id = ?");
                    $copyServices->bind_param("ii", $jobCardId, $appointmentId);
                    $copyServices->execute();
                    $copyServices->close();

                    $updateAppointment = $conn->prepare("UPDATE appointment SET status = 'assigned' WHERE id = ?");
                    $updateAppointment->bind_param("i", $appointmentId);
                    $updateAppointment->execute();

                    if ($updateAppointment->affected_rows !== 1) throw new Exception("Unable to update the appointment status.");
                    $updateAppointment->close();

                    $conn->commit();
                    $flashMessage = "Appointment #{$appointmentId} successfully assigned to mechanic and Job Card JC-{$jobCardId} created.";
                    $flashType = "success";
                    $reopenAcceptedModal = false;
                    $reopenAssignedModal = true;
                } catch (Throwable $e) {
                    $conn->rollback();
                    $flashMessage = $e->getMessage();
                    $flashType = "error";
                    $reopenAcceptedModal = true;
                }
            } else {
                $newStatus = ($action === 'approve_appointment') ? 'approved' : 'rejected';
                $stmt = $conn->prepare("UPDATE appointment SET status = ? WHERE id = ? AND LOWER(status) = 'pending'");
                $stmt->bind_param("si", $newStatus, $appointmentId);
                $stmt->execute();

                if ($stmt->affected_rows === 1) {
                    $flashMessage = ($newStatus === 'approved') ? "Appointment APT-" . $appointmentId . " has been approved successfully." : "Appointment APT-" . $appointmentId . " has been rejected.";
                    $flashType = 'success';
                } else {
                    $flashMessage = 'This appointment is no longer pending, or it could not be found.';
                    $flashType = 'error';
                }
                $stmt->close();
            }
        }
    }
}

/* Load registered mechanics for the assignment dropdown. */
$mechanics = [];
$mechanicResult = $conn->query("SELECT id, full_name FROM users WHERE LOWER(role) = 'mechanic' ORDER BY full_name ASC");
while ($mechanic = $mechanicResult->fetch_assoc()) {
    $mechanics[] = $mechanic;
}
$mechanicResult->free();

/* Load pending appointments */
$pendingRequests = [];
$sql = "
    SELECT a.id, a.user_id, a.vehicle_registration_number, a.brand, a.model, a.manufacture_year, a.date, a.time, a.additional_note, a.status,
        u.full_name, u.phone, u.email, GROUP_CONCAT(DISTINCT s.service_name ORDER BY s.service_name SEPARATOR ', ') AS requested_services
    FROM appointment a
    INNER JOIN users u ON u.id = a.user_id
    LEFT JOIN appointment_services aps ON aps.appointment_id = a.id
    LEFT JOIN services s ON s.id = aps.service_id
    WHERE LOWER(a.status) = 'pending'
    GROUP BY a.id
    ORDER BY a.date ASC, a.time ASC
";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) $pendingRequests[] = $row;
$result->free();
$pendingCount = count($pendingRequests);

/* Load Counts */
$approvedCount = (int) $conn->query("SELECT COUNT(*) AS total FROM appointment WHERE LOWER(status) = 'approved'")->fetch_assoc()['total'];
$rejectedCount = (int) $conn->query("SELECT COUNT(*) AS total FROM appointment WHERE LOWER(status) = 'rejected'")->fetch_assoc()['total'];

/* Load accepted records */
$acceptedRecords = $conn->query("
    SELECT a.id AS appointment_id, u.full_name AS customer_name, a.vehicle_registration_number, a.brand, a.model, a.manufacture_year, a.date, a.time,
        GROUP_CONCAT(DISTINCT s.service_name SEPARATOR ', ') AS service_names
    FROM appointment a
    INNER JOIN users u ON a.user_id = u.id
    LEFT JOIN appointment_services aps ON a.id = aps.appointment_id
    LEFT JOIN services s ON aps.service_id = s.id
    WHERE LOWER(a.status) = 'approved'
    GROUP BY a.id ORDER BY a.date ASC, a.time ASC
")->fetch_all(MYSQLI_ASSOC);

/* Load rejected records */
$rejectedRecords = $conn->query("
    SELECT a.id, a.vehicle_registration_number, a.brand, a.model, a.manufacture_year, a.date, a.time, a.status, u.full_name, u.phone, u.email,
        GROUP_CONCAT(DISTINCT s.service_name ORDER BY s.service_name SEPARATOR ', ') AS requested_services
    FROM appointment a
    INNER JOIN users u ON u.id = a.user_id
    LEFT JOIN appointment_services aps ON aps.appointment_id = a.id
    LEFT JOIN services s ON s.id = aps.service_id
    WHERE LOWER(a.status) = 'rejected'
    GROUP BY a.id ORDER BY a.date DESC
")->fetch_all(MYSQLI_ASSOC);

/* Helper to fetch Job Cards */
function getJobCards($conn, $status) {
    return $conn->query("
        SELECT jc.id AS job_card_id, jc.status AS job_status, jc.created_at AS job_created, a.id AS appointment_id, a.vehicle_registration_number, a.brand, a.model, a.manufacture_year, a.date, a.time,
            u.full_name AS customer_name, u.phone AS customer_phone, m.full_name AS mechanic_name
        FROM job_cards jc
        LEFT JOIN appointment a ON a.id = jc.appointment_id
        LEFT JOIN users u ON u.id = a.user_id
        LEFT JOIN users m ON m.id = jc.mechanic_id
        WHERE LOWER(jc.status) = '$status'
        ORDER BY jc.created_at DESC
    ")->fetch_all(MYSQLI_ASSOC);
}

$assignedRecords = getJobCards($conn, 'assigned');
$assignedCount = count($assignedRecords);
$inProgressRecords = getJobCards($conn, 'in progress');
$inProgressCount = count($inProgressRecords);
$closedRecords = getJobCards($conn, 'completed');
$closedCount = count($closedRecords);

/* 
 * Load PENDING INVOICES: 
 * These are jobs that are "completed" but DO NOT have a record in the 'invoices' table yet. 
 */
$pendingInvoices = [];
try {
    $pendingInvoicesQuery = "
        SELECT 
            jc.id AS job_card_id,
            a.id AS appointment_id,
            u.full_name AS customer_name,
            a.vehicle_registration_number,
            a.brand,
            a.model,
            a.date,
            (SELECT COALESCE(SUM(unit_price * quantity), 0) FROM job_card_parts WHERE job_card_id = jc.id) AS total_parts,
            (SELECT COALESCE(SUM(service_price), 0) FROM job_card_services WHERE job_card_id = jc.id) AS total_services
        FROM job_cards jc
        JOIN appointment a ON jc.appointment_id = a.id
        JOIN users u ON a.user_id = u.id
        LEFT JOIN invoices i ON jc.id = i.job_card_id
        WHERE LOWER(jc.status) = 'completed' AND i.id IS NULL
        ORDER BY jc.created_at DESC
    ";
    $invResult = $conn->query($pendingInvoicesQuery);
    if ($invResult) {
        $pendingInvoices = $invResult->fetch_all(MYSQLI_ASSOC);
        $invResult->free();
    }
} catch (Exception $e) {
    // If the invoices table isn't created yet, handle gracefully
    $pendingInvoices = [];
}
$pendingInvoicesCount = count($pendingInvoices);

/* Load SAVED PENDING INVOICES (Waiting to be marked as Paid) */
$savedPendingInvoices = [];
try {
    $savedInvQuery = "
        SELECT i.id AS invoice_id, i.final_amount, i.created_at,
               jc.id AS job_card_id,
               u.full_name AS customer_name,
               a.vehicle_registration_number, a.brand, a.model
        FROM invoices i
        JOIN job_cards jc ON i.job_card_id = jc.id
        JOIN appointment a ON jc.appointment_id = a.id
        JOIN users u ON a.user_id = u.id
        WHERE LOWER(i.payment_status) = 'pending'
        ORDER BY i.created_at ASC
    ";
    $savedInvResult = $conn->query($savedInvQuery);
    if ($savedInvResult) {
        $savedPendingInvoices = $savedInvResult->fetch_all(MYSQLI_ASSOC);
        $savedInvResult->free();
    }
} catch (Exception $e) {
    $savedPendingInvoices = [];
}
$savedPendingInvoicesCount = count($savedPendingInvoices);

/* Load TODAY's PENDING APPOINTMENTS */
$todayAppointmentsQuery = "
    SELECT a.id, a.user_id, u.full_name AS customer_name, u.phone AS customer_phone, u.email AS customer_email, a.vehicle_registration_number AS vehicle_number,
        CONCAT_WS(' ', a.brand, a.model, a.manufacture_year) AS vehicle_name, a.date AS appointment_date, a.time AS appointment_time, a.additional_note, a.status,
        GROUP_CONCAT(s.service_name SEPARATOR ', ') AS services
    FROM appointment a
    INNER JOIN users u ON a.user_id = u.id
    LEFT JOIN appointment_services aps ON a.id = aps.appointment_id
    LEFT JOIN services s ON aps.service_id = s.id
    WHERE DATE(a.date) = CURDATE() AND LOWER(TRIM(a.status)) = 'pending'
    GROUP BY a.id ORDER BY a.time ASC
";
$todayPendingAppointments = $conn->query($todayAppointmentsQuery)->fetch_all(MYSQLI_ASSOC);
$todayAppointmentsCount = count($todayPendingAppointments);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoPulse | Receptionist Dashboard</title>
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
                    <li>
                        <a href="dashboard.php" class="flex items-center gap-3 rounded-lg bg-[#E89000] px-4 py-3 text-xs font-semibold text-white shadow-sm">
                            <i class="fa-solid fa-table-columns w-4 text-center"></i>
                            Dashboard
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
                                <i class="fa-solid fa-users w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>
                                Inventory
                            </span>
                            <i class="fa-solid fa-chevron-right text-[9px] text-gray-500"></i>
                        </a>
                    </li>
                    <li>
                        <a href="reports.php" class="group flex items-center justify-between rounded-lg px-4 py-3 text-xs font-medium text-gray-300 transition duration-200 hover:bg-white/10 hover:text-white">
                            <span class="flex items-center gap-3">
                                <i class="fa-solid fa-chart-column w-4 text-center text-gray-400 group-hover:text-[#E89000]"></i>
                                Reports
                            </span>
                            <i class="fa-solid fa-chevron-right text-[9px] text-gray-500"></i>
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
                        <h2 class="text-lg font-bold text-gray-900 sm:text-xl">Receptionist Dashboard</h2>
                        <p class="mt-0.5 text-[11px] text-gray-500">
                            Welcome back, <span class="font-semibold text-[#E89000]">Admin</span>
                        </p>
                    </div>

                    <div class="flex items-center gap-3 sm:gap-5">
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
                                    <button type="button" class="text-[11px] font-semibold text-[#FFA500] hover:text-[#E89000] cursor-pointer">Mark all as read</button>
                                </div>
                                <div class="max-h-80 overflow-y-auto">
                                    <div class="flex gap-3 border-b border-gray-100 bg-orange-50/40 px-4 py-3 transition hover:bg-gray-50">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-100">
                                            <i class="fa-solid fa-clipboard-check text-sm text-blue-600"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center justify-between gap-2">
                                                <p class="text-xs font-semibold text-gray-800">New Job Card Assigned</p>
                                                <span class="h-2 w-2 shrink-0 rounded-full bg-[#E89000]"></span>
                                            </div>
                                            <p class="mt-1 text-[11px] leading-relaxed text-gray-500">A new job card has been assigned to a mechanic.</p>
                                            <span class="mt-1.5 block text-[10px] text-gray-400">5 minutes ago</span>
                                        </div>
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
            <div class="space-y-6 p-4 sm:p-6 lg:p-8">

                <!-- Global Notification Banner -->
                <?php if (!empty($flashMessage)): ?>
                    <div class="flex items-center gap-2 rounded-xl p-4 text-xs font-semibold shadow-sm <?= $flashType === 'success' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200' ?>">
                        <i class="fa-solid <?= $flashType === 'success' ? 'fa-circle-check text-green-600' : 'fa-circle-xmark text-red-600' ?> text-base"></i>
                        <span><?= htmlspecialchars($flashMessage) ?></span>
                    </div>
                <?php endif; ?>

                <!-- Status Cards Overview -->
                <section class="mb-6">
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

                        <!-- Today's Appointments -->
                        <div class="group relative overflow-hidden rounded-2xl cursor-pointer transition-all duration-300 hover:-translate-y-1 hover:shadow-lg border border-orange-100 bg-gradient-to-br from-white to-orange-50/70 p-5 shadow-sm sm:col-span-2 xl:col-span-4"
                            onclick="openTodayAppointmentsModal()">
                            <div class="relative z-10 flex flex-col justify-between gap-5 sm:flex-row sm:items-center">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-orange-100">
                                        <i class="fa-solid fa-calendar-days text-2xl text-[#E89000]"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-600">Today's Appointments</p>
                                        <div class="mt-1 flex items-baseline gap-2">
                                            <h3 class="text-4xl font-bold tracking-tight text-gray-900"><?= $todayAppointmentsCount ?></h3>
                                            <span class="text-xs font-medium text-green-600">Scheduled today</span>
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500">Total appointments scheduled for today</p>
                                    </div>
                                </div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <div class="rounded-xl border border-orange-100 bg-white/80 px-4 py-3">
                                        <p class="text-[10px] font-medium text-gray-500">Appointment Status</p>
                                        <p class="mt-1 text-sm font-semibold text-[#E89000]">Daily Overview</p>
                                    </div>
                                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white text-[#E89000] shadow-sm transition-transform duration-300 group-hover:translate-x-1 cursor-pointer">
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="absolute -right-10 -top-12 h-40 w-40 rounded-full bg-orange-100/40"></div>
                        </div>

                        <!-- Pending Requests -->
                        <div class="group cursor-pointer rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-orange-300 hover:shadow-md" onclick="openStatusModal('pendingModal')">
                            <div class="flex items-center justify-between">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-50 text-orange-500">
                                    <i class="fa-solid fa-clock text-lg"></i>
                                </div>
                                <span class="rounded-full bg-orange-50 px-2.5 py-1 text-xs font-medium text-orange-600">Pending</span>
                            </div>
                            <h3 class="mt-3 text-2xl font-bold text-gray-800"><?= $pendingCount ?></h3>
                            <p class="mt-0.5 text-sm font-semibold text-gray-700">Pending Requests</p>
                            <p class="mt-1 text-xs text-gray-500">Requests waiting for approval</p>
                            <span class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-orange-500 transition hover:text-orange-700">
                                View Records <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </span>
                        </div>

                        <!-- Accepted -->
                        <div class="group cursor-pointer rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-green-300 hover:shadow-md" onclick="openStatusModal('acceptedModal')">
                            <div class="flex items-center justify-between">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-50 text-green-500">
                                    <i class="fa-solid fa-check-circle text-lg"></i>
                                </div>
                                <span class="rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-600">Accepted</span>
                            </div>
                            <h3 class="mt-3 text-2xl font-bold text-gray-800"><?= $approvedCount ?></h3>
                            <p class="mt-0.5 text-sm font-semibold text-gray-700">Accepted</p>
                            <p class="mt-1 text-xs text-gray-500">Approved service requests</p>
                            <span class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-green-500 transition hover:text-green-700">
                                View Records <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </span>
                        </div>

                        <!-- Rejected -->
                        <div class="group cursor-pointer rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-red-300 hover:shadow-md" onclick="openStatusModal('rejectedModal')">
                            <div class="flex items-center justify-between">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-50 text-red-500">
                                    <i class="fa-solid fa-circle-xmark text-lg"></i>
                                </div>
                                <span class="rounded-full bg-red-50 px-2.5 py-1 text-xs font-medium text-red-600">Rejected</span>
                            </div>
                            <h3 class="mt-3 text-2xl font-bold text-gray-800"><?= $rejectedCount ?></h3>
                            <p class="mt-0.5 text-sm font-semibold text-gray-700">Rejected</p>
                            <p class="mt-1 text-xs text-gray-500">Declined service requests</p>
                            <span class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-red-500 transition hover:text-red-700">
                                View Records <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </span>
                        </div>

                        <!-- Assigned Jobs -->
                        <div class="group cursor-pointer rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-blue-300 hover:shadow-md" onclick="openStatusModal('assignedModal')">
                            <div class="flex items-center justify-between">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-500">
                                    <i class="fa-solid fa-clipboard-list text-lg"></i>
                                </div>
                                <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-600">Assigned</span>
                            </div>
                            <h3 class="mt-3 text-2xl font-bold text-gray-800"><?= $assignedCount ?></h3>
                            <p class="mt-0.5 text-sm font-semibold text-gray-700">Assigned Jobs</p>
                            <p class="mt-1 text-xs text-gray-500">Jobs assigned to mechanics</p>
                            <span class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-blue-500 transition hover:text-blue-700">
                                View Records <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </span>
                        </div>

                        <!-- In Progress -->
                        <div class="group cursor-pointer rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-purple-300 hover:shadow-md" onclick="openStatusModal('inProgressModal')">
                            <div class="flex items-center justify-between">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-50 text-purple-500">
                                    <i class="fa-solid fa-gears text-lg"></i>
                                </div>
                                <span class="rounded-full bg-purple-50 px-2.5 py-1 text-xs font-medium text-purple-600">Ongoing</span>
                            </div>
                            <h3 class="mt-3 text-2xl font-bold text-gray-800"><?= $inProgressCount ?></h3>
                            <p class="mt-0.5 text-sm font-semibold text-gray-700">In Progress</p>
                            <p class="mt-1 text-xs text-gray-500">Currently active service jobs</p>
                            <span class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-purple-500 transition hover:text-purple-700">
                                View Records <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </span>
                        </div>

                        <!-- Closed Jobs -->
                        <div class="group cursor-pointer rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-gray-400 hover:shadow-md" onclick="openStatusModal('closedModal')">
                            <div class="flex items-center justify-between">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 text-gray-600">
                                    <i class="fa-solid fa-circle-check text-lg"></i>
                                </div>
                                <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">Completed</span>
                            </div>
                            <h3 class="mt-3 text-2xl font-bold text-gray-800"><?= $closedCount ?></h3>
                            <p class="mt-0.5 text-sm font-semibold text-gray-700">Closed Jobs</p>
                            <p class="mt-1 text-xs text-gray-500">Successfully completed jobs</p>
                            <span class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-gray-500 transition hover:text-gray-700">
                                View Records <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </span>
                        </div>

                        <!-- Pending Invoices -->
                        <div class="group cursor-pointer rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-yellow-300 hover:shadow-md" onclick="openStatusModal('invoicesModal')">
                            <div class="flex items-center justify-between">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-yellow-50 text-yellow-600">
                                    <i class="fa-solid fa-file-invoice-dollar text-lg"></i>
                                </div>
                                <span class="rounded-full bg-yellow-50 px-2.5 py-1 text-xs font-medium text-yellow-600">Awaiting</span>
                            </div>
                            <h3 class="mt-3 text-2xl font-bold text-gray-800"><?= $pendingInvoicesCount ?></h3>
                            <p class="mt-0.5 text-sm font-semibold text-gray-700">Pending Invoices</p>
                            <p class="mt-1 text-xs text-gray-500">Ready to create invoice</p>
                            <span class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-yellow-600 transition hover:text-yellow-700">
                                View Records <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </span>
                        </div>

                        <!-- Paid Invoices -->
                        <div class="group cursor-pointer rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-teal-300 hover:shadow-md" onclick="openStatusModal('paymentsModal')">
                            <div class="flex items-center justify-between">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-teal-50 text-teal-600">
                                    <i class="fa-solid fa-money-bill-wave text-lg"></i>
                                </div>
                                <span class="rounded-full bg-teal-50 px-2.5 py-1 text-xs font-medium text-teal-600">Action Needed</span>
                            </div>
                            <h3 class="mt-3 text-2xl font-bold text-gray-800"><?= $savedPendingInvoicesCount ?></h3>
                            <p class="mt-0.5 text-sm font-semibold text-gray-700">Paid Invoices</p>
                            <p class="mt-1 text-xs text-gray-500">Invoices awaiting payment</p>
                            <span class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-teal-600 transition hover:text-teal-700">
                                View Records <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </span>
                        </div>

                    </div>
                </section>

                <!-- Quick Actions -->
                <section class="mt-6">
                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div class="border-b border-gray-100 px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-100">
                                    <i class="fa-solid fa-bolt text-lg text-[#E89000]"></i>
                                </div>
                                <div>
                                    <h2 class="text-base font-bold text-gray-900">Quick Actions</h2>
                                    <p class="mt-0.5 text-xs text-gray-500">Frequently used receptionist actions</p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 p-5 md:grid-cols-2 xl:grid-cols-3">
                            <a href="../register.php" class="group flex flex-col items-center rounded-xl border border-gray-200 bg-white p-6 text-center transition-all duration-300 hover:-translate-y-1 hover:border-[#E89000] hover:shadow-lg">
                                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-orange-50 transition-all duration-300 group-hover:scale-110 group-hover:bg-orange-100">
                                    <i class="fa-solid fa-user-plus text-3xl text-[#E89000]"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 transition-colors group-hover:text-[#E89000]">Register Client</h3>
                                <p class="mt-2 max-w-xs text-sm leading-relaxed text-gray-500">Create a new customer profile and store contact details.</p>
                                <div class="mt-5 flex items-center gap-2 text-sm font-semibold text-[#E89000]">
                                    <span>Get Started</span>
                                    <i class="fa-solid fa-arrow-right text-xs transition-transform duration-300 group-hover:translate-x-1"></i>
                                </div>
                            </a>

                            <button type="button" id="openJobCardModal" class="group flex cursor-pointer w-full flex-col items-center rounded-xl border border-gray-200 bg-white p-6 text-center transition-all duration-300 hover:-translate-y-1 hover:border-[#E89000] hover:shadow-lg">
                                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-orange-50 transition-all duration-300 group-hover:scale-110 group-hover:bg-orange-100">
                                    <i class="fa-solid fa-clipboard-list text-3xl text-[#E89000]"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 transition-colors group-hover:text-[#E89000]">Pending Requests</h3>
                                <p class="mt-2 max-w-xs text-sm leading-relaxed text-gray-500">Review customer appointment requests and approve pending services.</p>
                                <div class="mt-5 flex items-center gap-2 text-sm font-semibold text-[#E89000]">
                                    <span>View Requests</span>
                                    <i class="fa-solid fa-arrow-right text-xs transition-transform duration-300 group-hover:translate-x-1"></i>
                                </div>
                            </button>

                            <!-- Updated Quick Action: Triggers the same pending invoices modal -->
                            <button type="button" onclick="openStatusModal('invoicesModal')" class="group flex w-full cursor-pointer flex-col items-center rounded-xl border border-gray-200 bg-white p-6 text-center transition-all duration-300 hover:-translate-y-1 hover:border-[#E89000] hover:shadow-lg">
                                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-orange-50 transition-all duration-300 group-hover:scale-110 group-hover:bg-orange-100">
                                    <i class="fa-solid fa-file-invoice-dollar text-3xl text-[#E89000]"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 transition-colors group-hover:text-[#E89000]">Create Invoice</h3>
                                <p class="mt-2 max-w-xs text-sm leading-relaxed text-gray-500">Generate a customer invoice for completed vehicle services.</p>
                                <div class="mt-5 flex items-center gap-2 text-sm font-semibold text-[#E89000]">
                                    <span>Get Started</span>
                                    <i class="fa-solid fa-arrow-right text-xs transition-transform duration-300 group-hover:translate-x-1"></i>
                                </div>
                            </button>
                        </div>
                    </div>
                </section>

            </div>
        </main>
    </div>

    <!-- PENDING APPOINTMENT REQUESTS MODAL -->
    <div id="jobCardModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-gray-950/60 p-3 backdrop-blur-sm sm:p-6" role="dialog" aria-modal="true" aria-labelledby="jobCardModalTitle">
        <div class="relative flex max-h-[90vh] w-full max-w-7xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
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
                <button type="button" data-close-job-modal class="flex h-8 w-8 items-center justify-center rounded-full text-gray-400 transition hover:bg-gray-100 hover:text-gray-700 cursor-pointer" aria-label="Close modal">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

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
                                        <td class="px-4 py-4">
                                            <span class="rounded-lg bg-orange-50 px-3 py-1.5 text-xs font-bold text-[#E89000]">
                                                APT-<?= htmlspecialchars($appointment['id']) ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <p class="text-xs font-semibold text-gray-900"><?= htmlspecialchars($appointment['full_name']) ?></p>
                                            <p class="mt-1 text-xs text-gray-500"><i class="fa-solid fa-phone mr-1"></i><?= htmlspecialchars($appointment['phone']) ?></p>
                                            <p class="mt-1 text-xs text-gray-500"><i class="fa-solid fa-envelope mr-1"></i><?= htmlspecialchars($appointment['email']) ?></p>
                                        </td>
                                        <td class="px-4 py-4">
                                            <p class="text-xs font-semibold text-gray-900">
                                                <?= htmlspecialchars($appointment['brand'] . ' ' . $appointment['model']) ?>
                                                <span class="font-normal text-gray-500">(<?= htmlspecialchars($appointment['manufacture_year']) ?>)</span>
                                            </p>
                                            <p class="mt-1 text-xs text-gray-500"><i class="fa-solid fa-hashtag mr-1"></i><?= htmlspecialchars($appointment['vehicle_registration_number']) ?></p>
                                        </td>
                                        <td class="px-4 py-4">
                                            <p class="max-w-[180px] whitespace-normal text-xs text-gray-700"><?= htmlspecialchars($appointment['requested_services'] ?? 'N/A') ?></p>
                                        </td>
                                        <td class="px-4 py-4">
                                            <p class="text-xs font-semibold text-gray-800"><i class="fa-regular fa-calendar mr-1 text-[#E89000]"></i><?= htmlspecialchars($appointment['date']) ?></p>
                                            <p class="mt-1 text-xs text-gray-500"><i class="fa-regular fa-clock mr-1"></i><?= htmlspecialchars($appointment['time']) ?></p>
                                        </td>
                                        <td class="px-4 py-4">
                                            <form method="POST" action="dashboard.php" class="flex min-w-[240px] flex-col gap-2">
                                                <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($appointment['id']) ?>">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                                                <select name="mechanic_id" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs text-gray-700 outline-none transition focus:border-[#E89000]">
                                                    <option value="">Choose Mechanic</option>
                                                    <?php foreach ($mechanics as $mechanic): ?>
                                                        <option value="<?= htmlspecialchars($mechanic['id']) ?>"><?= htmlspecialchars($mechanic['full_name']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>

                                                <div class="flex gap-2">
                                                    <button type="submit" name="action" value="assign_mechanic"
                                                        <?= empty($mechanics) ? 'disabled' : '' ?>
                                                        onclick="return confirm('Assign the selected mechanic and approve this appointment?');"
                                                        class="flex-1 cursor-pointer rounded-lg bg-green-600 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-green-700 active:scale-95 disabled:cursor-not-allowed disabled:opacity-50">
                                                        <i class="fa-solid fa-check mr-1"></i>Approve
                                                    </button>
                                                    <button type="submit" name="action" value="reject_appointment"
                                                        onclick="return confirm('Are you sure you want to reject this appointment?');"
                                                        class="flex-1 cursor-pointer rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-red-700 active:scale-95">
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
    <div id="pendingModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-gray-950/60 p-3 backdrop-blur-sm sm:p-6" role="dialog" aria-modal="true">
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
                <button type="button" onclick="closeStatusModal('pendingModal')" class="flex h-8 w-8 items-center justify-center rounded-full text-gray-400 transition hover:bg-gray-100 hover:text-gray-700 cursor-pointer" aria-label="Close modal">
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
                                <th class="px-4 py-4 text-center">Actions</th>
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
                                        <td class="px-4 py-4">
                                            <p class="max-w-[180px] whitespace-normal text-xs text-gray-700"><?= htmlspecialchars($r['requested_services'] ?? 'N/A') ?></p>
                                        </td>
                                        <td class="px-4 py-4">
                                            <p class="text-xs font-semibold text-gray-800"><i class="fa-regular fa-calendar mr-1 text-orange-500"></i><?= htmlspecialchars($r['date']) ?></p>
                                            <p class="mt-1 text-xs text-gray-500"><i class="fa-regular fa-clock mr-1"></i><?= htmlspecialchars($r['time']) ?></p>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="flex items-center justify-center gap-2">
                                                <form method="POST" action="dashboard.php">
                                                    <input type="hidden" name="action" value="approve_appointment">
                                                    <input type="hidden" name="appointment_id" value="<?= (int) $r['id'] ?>">
                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                                    <input type="hidden" name="return_to" value="pendingModal">
                                                    <button type="submit" onclick="return confirm('Are you sure you want to approve this appointment?')" class="rounded-lg bg-green-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-green-700 cursor-pointer">
                                                        <i class="fa-solid fa-check mr-1"></i> Accept
                                                    </button>
                                                </form>

                                                <form method="POST" action="dashboard.php">
                                                    <input type="hidden" name="action" value="reject_appointment">
                                                    <input type="hidden" name="appointment_id" value="<?= (int) $r['id'] ?>">
                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                                    <input type="hidden" name="return_to" value="pendingModal">
                                                    <button type="submit" onclick="return confirm('Are you sure you want to reject this appointment?')" class="rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-red-700 cursor-pointer">
                                                        <i class="fa-solid fa-xmark mr-1"></i> Reject
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="px-5 py-16 text-center">
                                        <i class="fa-regular fa-calendar-check mb-3 block text-4xl text-gray-300"></i>
                                        <p class="text-xs font-semibold text-gray-700">No Pending Records</p>
                                        <p class="mt-1 text-xs text-gray-500">No pending appointments found.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ACCEPTED RECORDS MODAL (ASSIGN MECHANIC TO APPROVED APPOINTMENT) -->
    <div id="acceptedModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-gray-950/60 p-3 backdrop-blur-sm sm:p-6" role="dialog" aria-modal="true">
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
                <button type="button" onclick="closeStatusModal('acceptedModal')" class="flex h-8 w-8 items-center justify-center rounded-full text-gray-400 transition hover:bg-gray-100 hover:text-gray-700 cursor-pointer" aria-label="Close modal">
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
                                <th class="px-4 py-4">Assign Mechanic</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <?php if (!empty($acceptedRecords)): ?>
                                <?php foreach ($acceptedRecords as $accepted): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4 text-sm font-semibold text-gray-700">
                                            #<?= htmlspecialchars($accepted["appointment_id"]) ?>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-700">
                                            <?= htmlspecialchars($accepted["customer_name"]) ?>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-700">
                                            <div class="font-medium">
                                                <?= htmlspecialchars($accepted["brand"] . " " . $accepted["model"]) ?>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                <?= htmlspecialchars($accepted["vehicle_registration_number"]) ?>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-700">
                                            <?= htmlspecialchars($accepted["service_names"] ?? "No services") ?>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-700">
                                            <?= htmlspecialchars($accepted["date"]) ?>
                                            <div class="text-xs text-gray-500">
                                                <?= htmlspecialchars($accepted["time"]) ?>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <form method="POST" action="dashboard.php" class="flex flex-col gap-2 min-w-[160px]">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                                <input type="hidden" name="action" value="assign_accepted_appointment">
                                                <input type="hidden" name="appointment_id" value="<?= (int) $accepted["appointment_id"] ?>">

                                                <select name="mechanic_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs outline-none focus:border-[#E89000]">
                                                    <option value="">Select Mechanic</option>
                                                    <?php foreach ($mechanics as $mechanic): ?>
                                                        <option value="<?= (int) $mechanic["id"] ?>">
                                                            <?= htmlspecialchars($mechanic["full_name"]) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>

                                                <button type="submit"
                                                    onclick="return confirm('Are you sure you want to assign this appointment and create its job card?')"
                                                    class="rounded-lg bg-[#E89000] px-4 py-2 text-xs font-semibold text-white transition hover:bg-orange-600 cursor-pointer">
                                                    <i class="fa-solid fa-clipboard-check mr-1"></i>
                                                    Assign
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">
                                        No accepted appointments are waiting for assignment.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- REJECTED RECORDS MODAL -->
    <div id="rejectedModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-gray-950/60 p-3 backdrop-blur-sm sm:p-6" role="dialog" aria-modal="true">
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
                <button type="button" onclick="closeStatusModal('rejectedModal')" class="flex h-8 w-8 items-center justify-center rounded-full text-gray-400 transition hover:bg-gray-100 hover:text-gray-700 cursor-pointer" aria-label="Close modal">
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
                                        <td class="px-4 py-4">
                                            <p class="max-w-[180px] whitespace-normal text-xs text-gray-700"><?= htmlspecialchars($r['requested_services'] ?? 'N/A') ?></p>
                                        </td>
                                        <td class="px-4 py-4">
                                            <p class="text-xs font-semibold text-gray-800"><i class="fa-regular fa-calendar mr-1 text-red-500"></i><?= htmlspecialchars($r['date']) ?></p>
                                            <p class="mt-1 text-xs text-gray-500"><i class="fa-regular fa-clock mr-1"></i><?= htmlspecialchars($r['time']) ?></p>
                                        </td>
                                        <td class="px-4 py-4"><span class="rounded-full bg-red-50 px-2.5 py-1 text-xs font-medium text-red-600">Rejected</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="px-5 py-16 text-center">
                                        <i class="fa-regular fa-calendar-check mb-3 block text-4xl text-gray-300"></i>
                                        <p class="text-xs font-semibold text-gray-700">No Rejected Records</p>
                                        <p class="mt-1 text-xs text-gray-500">No rejected appointments found.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ASSIGNED JOBS MODAL -->
    <div id="assignedModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-gray-950/60 p-3 backdrop-blur-sm sm:p-6" role="dialog" aria-modal="true">
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
                <button type="button" onclick="closeStatusModal('assignedModal')" class="flex h-8 w-8 items-center justify-center rounded-full text-gray-400 transition hover:bg-gray-100 hover:text-gray-700 cursor-pointer" aria-label="Close modal">
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
                                                <p class="text-xs font-semibold text-gray-900">
                                                    <?= htmlspecialchars($r['brand'] . ' ' . $r['model']) ?> <span class="font-normal text-gray-500">(<?= htmlspecialchars($r['manufacture_year']) ?>)</span>
                                                </p>
                                                <p class="mt-1 text-xs text-gray-500"><i class="fa-solid fa-hashtag mr-1"></i><?= htmlspecialchars($r['vehicle_registration_number']) ?></p>
                                            <?php else: ?>
                                                <p class="text-xs text-gray-500">N/A</p>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-4">
                                            <p class="text-xs font-semibold text-gray-900"><?= htmlspecialchars($r['mechanic_name'] ?? 'N/A') ?></p>
                                        </td>
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
                                <tr>
                                    <td colspan="7" class="px-5 py-16 text-center">
                                        <i class="fa-solid fa-clipboard-list mb-3 block text-4xl text-gray-300"></i>
                                        <p class="text-xs font-semibold text-gray-700">No Assigned Jobs</p>
                                        <p class="mt-1 text-xs text-gray-500">No assigned job cards found.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- IN PROGRESS JOBS MODAL -->
    <div id="inProgressModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-gray-950/60 p-3 backdrop-blur-sm sm:p-6" role="dialog" aria-modal="true">
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
                <button type="button" onclick="closeStatusModal('inProgressModal')" class="flex h-8 w-8 items-center justify-center rounded-full text-gray-400 transition hover:bg-gray-100 hover:text-gray-700 cursor-pointer" aria-label="Close modal">
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
                                <tr>
                                    <td colspan="7" class="px-5 py-16 text-center">
                                        <i class="fa-solid fa-gears mb-3 block text-4xl text-gray-300"></i>
                                        <p class="text-xs font-semibold text-gray-700">No In-Progress Jobs</p>
                                        <p class="mt-1 text-xs text-gray-500">No jobs currently in progress.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- CLOSED JOBS MODAL -->
    <div id="closedModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-gray-950/60 p-3 backdrop-blur-sm sm:p-6" role="dialog" aria-modal="true">
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
                <button type="button" onclick="closeStatusModal('closedModal')" class="flex h-8 w-8 items-center justify-center rounded-full text-gray-400 transition hover:bg-gray-100 hover:text-gray-700 cursor-pointer" aria-label="Close modal">
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
                                        <td class="px-4 py-4"><p class="text-xs font-semibold text-gray-900"><?= htmlspecialchars($r['customer_name'] ?? 'N/A') ?></p></td>
                                        <td class="px-4 py-4">
                                            <?php if (!empty($r['brand'])): ?>
                                                <p class="text-xs font-semibold text-gray-900"><?= htmlspecialchars($r['brand'] . ' ' . $r['model']) ?> <span class="font-normal text-gray-500">(<?= htmlspecialchars($r['manufacture_year']) ?>)</span></p>
                                            <?php else: ?>
                                                <p class="text-xs text-gray-500">N/A</p>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-4"><p class="text-xs font-semibold text-gray-900"><?= htmlspecialchars($r['mechanic_name'] ?? 'N/A') ?></p></td>
                                        <td class="px-4 py-4">
                                            <?php if (!empty($r['date'])): ?>
                                                <p class="text-xs font-semibold text-gray-800"><i class="fa-regular fa-calendar mr-1 text-gray-500"></i><?= htmlspecialchars($r['date']) ?></p>
                                            <?php else: ?>
                                                <p class="text-xs text-gray-500">N/A</p>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-4"><span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">Completed</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="px-5 py-16 text-center">
                                        <i class="fa-solid fa-circle-check mb-3 block text-4xl text-gray-300"></i>
                                        <p class="text-xs font-semibold text-gray-700">No Closed Jobs</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- PENDING INVOICES MODAL -->
    <div id="invoicesModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-gray-950/60 p-3 backdrop-blur-sm sm:p-6" role="dialog" aria-modal="true">
        <div class="relative flex max-h-[90vh] w-full max-w-5xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="flex shrink-0 items-center justify-between border-b border-gray-100 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-yellow-50">
                        <i class="fa-solid fa-file-invoice-dollar text-lg text-yellow-600"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Pending Invoices</h2>
                        <p class="mt-0.5 text-xs text-gray-500"><?= $pendingInvoicesCount ?> completed job<?= $pendingInvoicesCount !== 1 ? 's' : '' ?> awaiting an invoice</p>
                    </div>
                </div>
                <button type="button" onclick="closeStatusModal('invoicesModal')" class="flex h-8 w-8 items-center justify-center rounded-full text-gray-400 transition hover:bg-gray-100 hover:text-gray-700 cursor-pointer" aria-label="Close modal">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>
            <div class="min-h-0 flex-1 overflow-auto">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[900px] text-left">
                        <thead class="sticky top-0 bg-gray-50 text-xs font-bold uppercase tracking-wider text-gray-600">
                            <tr>
                                <th class="px-4 py-4">Job Card</th>
                                <th class="px-4 py-4">Customer Details</th>
                                <th class="px-4 py-4">Vehicle Details</th>
                                <th class="px-4 py-4 text-right">Parts Cost</th>
                                <th class="px-4 py-4 text-right">Services Cost</th>
                                <th class="px-4 py-4 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <?php if (!empty($pendingInvoices)): ?>
                                <?php foreach ($pendingInvoices as $inv): ?>
                                    <tr class="transition hover:bg-gray-50">
                                        <td class="px-4 py-4"><span class="rounded-lg bg-yellow-50 px-3 py-1.5 text-xs font-bold text-yellow-600">JC-<?= htmlspecialchars($inv['job_card_id']) ?></span></td>
                                        <td class="px-4 py-4">
                                            <p class="text-xs font-semibold text-gray-900"><?= htmlspecialchars($inv['customer_name'] ?? 'N/A') ?></p>
                                        </td>
                                        <td class="px-4 py-4">
                                            <p class="text-xs font-semibold text-gray-900"><?= htmlspecialchars($inv['brand'] . ' ' . $inv['model']) ?></p>
                                            <p class="mt-1 text-[10px] text-gray-500"><?= htmlspecialchars($inv['vehicle_registration_number']) ?></p>
                                        </td>
                                        <td class="px-4 py-4 text-right">
                                            <p class="text-xs font-medium text-gray-700">LKR <?= number_format($inv['total_parts'], 2) ?></p>
                                        </td>
                                        <td class="px-4 py-4 text-right">
                                            <p class="text-xs font-medium text-gray-700">LKR <?= number_format($inv['total_services'], 2) ?></p>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <button type="button" 
                                                onclick="openCreateInvoiceDetailsModal(<?= $inv['job_card_id'] ?>, '<?= addslashes($inv['customer_name']) ?>', <?= $inv['total_parts'] ?>, <?= $inv['total_services'] ?>)" 
                                                class="rounded-lg bg-[#E89000] px-3 py-2 text-xs font-semibold text-white transition hover:bg-orange-600 cursor-pointer shadow-sm">
                                                <i class="fa-solid fa-plus mr-1"></i> Create Invoice
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="px-5 py-16 text-center">
                                        <i class="fa-solid fa-file-invoice-dollar mb-3 block text-4xl text-gray-300"></i>
                                        <p class="text-xs font-semibold text-gray-700">No Pending Invoices</p>
                                        <p class="mt-1 text-xs text-gray-500">All completed jobs have been invoiced.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- CREATE INVOICE DETAILS MODAL -->
    <div id="createInvoiceDetailsModal" class="fixed inset-0 z-[110] hidden items-center justify-center bg-gray-950/60 p-3 backdrop-blur-sm sm:p-6" role="dialog" aria-modal="true">
        <div class="relative flex w-full max-w-lg flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="flex shrink-0 items-center justify-between border-b border-gray-100 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-50">
                        <i class="fa-solid fa-file-invoice text-lg text-orange-500"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Finalize Invoice Details</h2>
                        <p class="mt-0.5 text-xs text-gray-500">Verify amounts and apply discounts before saving</p>
                    </div>
                </div>
                <button type="button" onclick="closeCreateInvoiceDetailsModal()" class="flex h-8 w-8 items-center justify-center rounded-full text-gray-400 transition hover:bg-gray-100 hover:text-gray-700 cursor-pointer" aria-label="Close modal">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>
            
            <form method="POST" action="dashboard.php" class="p-6">
                <input type="hidden" name="action" value="create_invoice">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="job_card_id" id="modal_inv_job_card_id">

                <div class="mb-5 rounded-lg bg-gray-50 p-4 border border-gray-200">
                    <div class="flex justify-between mb-2">
                        <span class="text-xs font-semibold text-gray-600">Job Card:</span>
                        <span class="text-xs font-bold text-gray-900" id="modal_inv_job_text"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-xs font-semibold text-gray-600">Customer:</span>
                        <span class="text-xs font-bold text-gray-900" id="modal_inv_customer"></span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-5">
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 mb-1">Total Parts (LKR)</label>
                        <input type="number" step="0.01" name="total_parts" id="modal_inv_parts" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 outline-none focus:border-[#E89000]" oninput="calcInvoiceTotal()">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 mb-1">Total Services (LKR)</label>
                        <input type="number" step="0.01" name="total_services" id="modal_inv_services" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 outline-none focus:border-[#E89000]" oninput="calcInvoiceTotal()">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 mb-1">Discount (LKR)</label>
                        <input type="number" step="0.01" name="discount" id="modal_inv_discount" value="0.00" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 outline-none focus:border-[#E89000]" oninput="calcInvoiceTotal()">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-900 mb-1">Final Amount (LKR)</label>
                        <input type="number" step="0.01" name="final_amount" id="modal_inv_final" class="w-full rounded-lg border border-gray-300 bg-gray-100 px-3 py-2 text-sm font-bold text-gray-900 outline-none" readonly>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6 border-t border-gray-100 pt-5">
                    <button type="button" onclick="closeCreateInvoiceDetailsModal()" class="rounded-lg bg-gray-100 px-4 py-2 text-xs font-semibold text-gray-700 transition hover:bg-gray-200 cursor-pointer">Cancel</button>
                    <button type="submit" onclick="return confirm('Are you sure you want to save this invoice?')" class="rounded-lg bg-green-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-green-700 cursor-pointer">
                        <i class="fa-solid fa-floppy-disk mr-1"></i> Save Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- PAID INVOICES MODAL (Formerly Payments Today) -->
    <div id="paymentsModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-gray-950/60 p-3 backdrop-blur-sm sm:p-6" role="dialog" aria-modal="true">
        <div class="relative flex max-h-[90vh] w-full max-w-5xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="flex shrink-0 items-center justify-between border-b border-gray-100 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-teal-50">
                        <i class="fa-solid fa-money-bill-wave text-lg text-teal-600"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Mark Invoices as Paid</h2>
                        <p class="mt-0.5 text-xs text-gray-500"><?= $savedPendingInvoicesCount ?> pending invoice<?= $savedPendingInvoicesCount !== 1 ? 's' : '' ?> awaiting payment</p>
                    </div>
                </div>
                <button type="button" onclick="closeStatusModal('paymentsModal')" class="flex h-8 w-8 items-center justify-center rounded-full text-gray-400 transition hover:bg-gray-100 hover:text-gray-700 cursor-pointer" aria-label="Close modal">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>
            <div class="min-h-0 flex-1 overflow-auto">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[900px] text-left">
                        <thead class="sticky top-0 bg-gray-50 text-xs font-bold uppercase tracking-wider text-gray-600">
                            <tr>
                                <th class="px-4 py-4">Invoice ID</th>
                                <th class="px-4 py-4">Customer Details</th>
                                <th class="px-4 py-4">Vehicle Details</th>
                                <th class="px-4 py-4">Date Created</th>
                                <th class="px-4 py-4 text-right">Final Amount</th>
                                <th class="px-4 py-4 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <?php if (!empty($savedPendingInvoices)): ?>
                                <?php foreach ($savedPendingInvoices as $inv): ?>
                                    <tr class="transition hover:bg-gray-50">
                                        <td class="px-4 py-4">
                                            <span class="rounded-lg bg-teal-50 px-3 py-1.5 text-xs font-bold text-teal-600">INV-<?= htmlspecialchars($inv['invoice_id']) ?></span>
                                            <div class="mt-1 text-[10px] text-gray-500">JC-<?= htmlspecialchars($inv['job_card_id']) ?></div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <p class="text-xs font-semibold text-gray-900"><?= htmlspecialchars($inv['customer_name']) ?></p>
                                        </td>
                                        <td class="px-4 py-4">
                                            <p class="text-xs font-semibold text-gray-900"><?= htmlspecialchars($inv['brand'] . ' ' . $inv['model']) ?></p>
                                            <p class="mt-1 text-[10px] text-gray-500"><?= htmlspecialchars($inv['vehicle_registration_number']) ?></p>
                                        </td>
                                        <td class="px-4 py-4">
                                            <p class="text-xs text-gray-700"><?= date('M j, Y', strtotime($inv['created_at'])) ?></p>
                                        </td>
                                        <td class="px-4 py-4 text-right">
                                            <p class="text-xs font-bold text-gray-900">LKR <?= number_format($inv['final_amount'], 2) ?></p>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <form method="POST" action="dashboard.php" class="inline-block">
                                                <input type="hidden" name="action" value="mark_as_paid">
                                                <input type="hidden" name="invoice_id" value="<?= (int)$inv['invoice_id'] ?>">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                                <button type="submit" onclick="return confirm('Confirm payment received for this invoice?')" class="rounded-lg bg-teal-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-teal-700 cursor-pointer shadow-sm">
                                                    <i class="fa-solid fa-check mr-1"></i> Mark as Paid
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="px-5 py-16 text-center">
                                        <i class="fa-solid fa-check-double mb-3 block text-4xl text-gray-300"></i>
                                        <p class="text-xs font-semibold text-gray-700">No Pending Invoices</p>
                                        <p class="mt-1 text-xs text-gray-500">There are no invoices waiting to be marked as paid.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- TODAY'S APPOINTMENTS MODAL -->
    <div id="todayAppointmentsModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 p-4">
        <div class="max-h-[90vh] w-full max-w-7xl overflow-hidden rounded-xl bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-gray-200 p-5">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Today's Appointments</h2>
                    <p class="mt-1 text-xs text-gray-500">View and manage today's pending appointment requests</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-600">
                        <?= $todayAppointmentsCount ?> Pending
                    </span>
                    <button type="button" onclick="closeTodayAppointmentsModal()" class="flex h-9 w-9 items-center justify-center rounded-lg text-gray-500 transition hover:bg-gray-100 hover:text-gray-900">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>

            <div class="max-h-[calc(90vh-90px)] overflow-auto p-5">
                <?php if (!empty($todayPendingAppointments)): ?>
                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="w-full min-w-[1100px] text-left text-xs">
                            <thead class="bg-gray-50 text-gray-600">
                                <tr>
                                    <th class="px-4 py-3 font-semibold">Request</th>
                                    <th class="px-4 py-3 font-semibold">Customer Details</th>
                                    <th class="px-4 py-3 font-semibold">Vehicle Details</th>
                                    <th class="px-4 py-3 font-semibold">Requested Services</th>
                                    <th class="px-4 py-3 font-semibold">Appointment Time</th>
                                    <th class="px-4 py-3 text-center font-semibold">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php foreach ($todayPendingAppointments as $appointment): ?>
                                    <tr class="transition hover:bg-gray-50">
                                        <td class="px-4 py-4">
                                            <span class="font-bold text-gray-900">#<?= (int) $appointment['id'] ?></span>
                                            <p class="mt-1 text-[10px] text-orange-600">Pending</p>
                                        </td>
                                        <td class="px-4 py-4">
                                            <p class="font-semibold text-gray-900"><?= htmlspecialchars($appointment['customer_name'] ?? '') ?></p>
                                            <p class="mt-1 text-gray-500"><?= htmlspecialchars($appointment['customer_phone'] ?? '') ?></p>
                                        </td>
                                        <td class="px-4 py-4">
                                            <p class="font-semibold text-gray-900"><?= htmlspecialchars($appointment['vehicle_name'] ?? '') ?></p>
                                            <p class="mt-1 text-gray-500"><?= htmlspecialchars($appointment['vehicle_number'] ?? '') ?></p>
                                        </td>
                                        <td class="px-4 py-4">
                                            <p class="max-w-[200px] whitespace-normal text-gray-700"><?= htmlspecialchars($appointment['services'] ?? 'No services') ?></p>
                                        </td>
                                        <td class="px-4 py-4">
                                            <p class="font-semibold text-gray-900"><?= htmlspecialchars($appointment['appointment_time'] ?? '') ?></p>
                                            <p class="mt-1 text-gray-500"><?= htmlspecialchars($appointment['appointment_date'] ?? '') ?></p>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="flex items-center justify-center gap-2">
                                                <form method="POST" action="dashboard.php">
                                                    <input type="hidden" name="action" value="approve_appointment">
                                                    <input type="hidden" name="appointment_id" value="<?= (int) $appointment['id'] ?>">
                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                                    <input type="hidden" name="return_to" value="today_appointments">
                                                    <button type="submit" onclick="return confirm('Are you sure you want to accept this appointment?')" class="rounded-lg bg-green-600 px-3 py-2 font-semibold text-white transition hover:bg-green-700 cursor-pointer">
                                                        <i class="fa-solid fa-check mr-1"></i> Accept
                                                    </button>
                                                </form>

                                                <form method="POST" action="dashboard.php">
                                                    <input type="hidden" name="action" value="reject_appointment">
                                                    <input type="hidden" name="appointment_id" value="<?= (int) $appointment['id'] ?>">
                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                                    <input type="hidden" name="return_to" value="today_appointments">
                                                    <button type="submit" onclick="return confirm('Are you sure you want to reject this appointment?')" class="rounded-lg bg-red-600 px-3 py-2 font-semibold text-white transition hover:bg-red-700 cursor-pointer">
                                                        <i class="fa-solid fa-xmark mr-1"></i> Reject
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
                    <div class="flex flex-col items-center justify-center py-14 text-center">
                        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-orange-100">
                            <i class="fa-solid fa-calendar-check text-2xl text-orange-500"></i>
                        </div>
                        <h3 class="text-sm font-bold text-gray-900">No Pending Appointments Today</h3>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
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

        statusModalIds.forEach(id => {
            const modal = document.getElementById(id);
            if (!modal) return;
            modal.addEventListener("click", event => {
                if (event.target === modal) closeStatusModal(id);
            });
        });

        document.addEventListener("keydown", event => {
            if (event.key === "Escape") {
                if (jobCardModal && !jobCardModal.classList.contains("hidden")) {
                    closeModal();
                }
                statusModalIds.forEach(id => {
                    const modal = document.getElementById(id);
                    if (modal && !modal.classList.contains("hidden")) {
                        closeStatusModal(id);
                    }
                });
                if (document.getElementById('createInvoiceDetailsModal') && !document.getElementById('createInvoiceDetailsModal').classList.contains('hidden')) {
                    closeCreateInvoiceDetailsModal();
                }
            }
        });

        function openTodayAppointmentsModal() {
            const modal = document.getElementById('todayAppointmentsModal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.classList.add('overflow-hidden');
            }
        }

        function closeTodayAppointmentsModal() {
            const modal = document.getElementById('todayAppointmentsModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');
            }
        }

        document.getElementById('todayAppointmentsModal')?.addEventListener('click', function (event) {
            if (event.target === this) {
                closeTodayAppointmentsModal();
            }
        });

        // Functions for Create Invoice Modal
        function openCreateInvoiceDetailsModal(jobCardId, customerName, partsTotal, servicesTotal) {
            // Close the pending invoices modal first
            closeStatusModal('invoicesModal');
            
            // Populate Details
            document.getElementById('modal_inv_job_card_id').value = jobCardId;
            document.getElementById('modal_inv_job_text').textContent = 'JC-' + jobCardId;
            document.getElementById('modal_inv_customer').textContent = customerName;
            
            document.getElementById('modal_inv_parts').value = parseFloat(partsTotal).toFixed(2);
            document.getElementById('modal_inv_services').value = parseFloat(servicesTotal).toFixed(2);
            document.getElementById('modal_inv_discount').value = "0.00";
            
            calcInvoiceTotal();

            // Open Modal
            const modal = document.getElementById('createInvoiceDetailsModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function closeCreateInvoiceDetailsModal() {
            const modal = document.getElementById('createInvoiceDetailsModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            
            // Re-open previous modal so they aren't completely kicked out of the list
            openStatusModal('invoicesModal');
        }

        function calcInvoiceTotal() {
            let parts = parseFloat(document.getElementById('modal_inv_parts').value) || 0;
            let services = parseFloat(document.getElementById('modal_inv_services').value) || 0;
            let discount = parseFloat(document.getElementById('modal_inv_discount').value) || 0;
            
            let final = (parts + services) - discount;
            document.getElementById('modal_inv_final').value = final.toFixed(2);
        }

        // Trigger modal re-openings based on PHP server response
        <?php if ($reopenModal): ?>
            openModal();
        <?php endif; ?>

        <?php if (!empty($reopenTodayAppointmentsModal)): ?>
            openTodayAppointmentsModal();
        <?php endif; ?>
        
        <?php if (!empty($reopenPendingModal)): ?>
            openStatusModal('pendingModal');
        <?php endif; ?>

        <?php if ($reopenAcceptedModal): ?>
            openStatusModal('acceptedModal');
        <?php endif; ?>

        <?php if ($reopenAssignedModal): ?>
            openStatusModal('assignedModal');
        <?php endif; ?>
        
        <?php if ($reopenInvoicesModal): ?>
            openStatusModal('invoicesModal');
        <?php endif; ?>
        
        <?php if ($reopenPaymentsModal): ?>
            openStatusModal('paymentsModal');
        <?php endif; ?>
    </script>
</body>

</html>