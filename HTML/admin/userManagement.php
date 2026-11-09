<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli("localhost", "root", "", "autopulse");
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Protect this operation: only an authenticated admin should register staff.


// CSRF token
if (empty($_SESSION['staff_csrf_token'])) {
    $_SESSION['staff_csrf_token'] = bin2hex(random_bytes(32));
}

$registrationMessage = "";
$registrationType = "";
$openModal = "";

// Handle receptionist/mechanic registration
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['register_staff'])) {

    $fullName = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $role = strtolower(trim($_POST['role'] ?? ''));

    $openModal = $role;

    try {
        // Validate CSRF token
        if (
            !isset($_POST['csrf_token']) ||
            !hash_equals($_SESSION['staff_csrf_token'], $_POST['csrf_token'])
        ) {
            throw new Exception("Invalid request. Please refresh the page and try again.");
        }

        // Only these two roles can be registered through this modal
        if (!in_array($role, ['receptionist', 'mechanic'], true)) {
            throw new Exception("Invalid user role.");
        }

        if ($fullName === "" || $phone === "" || $email === "" || $password === "") {
            throw new Exception("Please complete all required fields.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Please enter a valid email address.");
        }

        if (strlen($password) < 8) {
            throw new Exception("Password must contain at least 8 characters.");
        }

        if ($password !== $confirmPassword) {
            throw new Exception("Passwords do not match.");
        }

        // Check whether the email already exists
        $checkEmail = $conn->prepare(
            "SELECT id FROM users WHERE email = ? LIMIT 1"
        );
        $checkEmail->bind_param("s", $email);
        $checkEmail->execute();

        if ($checkEmail->get_result()->num_rows > 0) {
            throw new Exception("An account with this email already exists.");
        }

        $checkEmail->close();

        // Securely hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new staff account with its assigned role
        $insertUser = $conn->prepare(
            "INSERT INTO users (full_name, phone, email, password, role)
             VALUES (?, ?, ?, ?, ?)"
        );

        $insertUser->bind_param(
            "sssss",
            $fullName,
            $phone,
            $email,
            $hashedPassword,
            $role
        );

        $insertUser->execute();
        $insertUser->close();

        $registrationMessage = ucfirst($role) . " account registered successfully!";
        $registrationType = "success";
        $openModal = "";

    } catch (Exception $e) {
        $registrationMessage = $e->getMessage();
        $registrationType = "error";
    }
}
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
                    <a href="userManagement.php" class="flex items-center gap-3 rounded-lg bg-[#E89000] px-4 py-3 text-xs font-semibold text-white shadow-sm">
                        <i class="fa-solid fa-table-columns w-4 text-center"></i>
                        User Management
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
<section class="mb-6">
    <section class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-100 text-[#E89000]">
                        <i class="fa-solid fa-users text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold tracking-tight text-gray-900">User Management</h1>
                        <p class="mt-0.5 text-xs text-gray-500">View and manage system users</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-[11px] text-gray-500 shadow-sm">
                    <i class="fa-solid fa-house text-[#E89000]"></i>
                    <span>Dashboard</span>
                    <i class="fa-solid fa-chevron-right text-[9px]"></i>
                    <span class="font-semibold text-gray-800">User Management</span>
                </div>
            </section>

<!-- Quick Actions -->
<section class="mt-5 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">

    <!-- Header -->
    <div class="mb-7 flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-orange-100">
                <i class="fa-solid fa-bolt text-xl text-[#E89000]"></i>
            </div>

            <div>
                <h2 class="text-xl font-bold text-gray-900">
                    Quick Actions
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Common administrator actions
                </p>
            </div>
        </div>

        <span class="hidden rounded-full bg-orange-50 px-3 py-1 text-xs font-semibold text-[#E89000] sm:inline-flex">
            4 Actions
        </span>
    </div>

    <!-- Action Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

        <!-- Add Receptionist -->
        <button type="button"
            onclick="openStaffModal('receptionist')"
            class="group relative w-full cursor-pointer overflow-hidden rounded-xl border border-gray-200 bg-white p-5 text-left transition-all duration-300 hover:-translate-y-1 hover:border-[#E89000] hover:shadow-lg hover:shadow-orange-100/50">

            <div class="mb-5 flex items-center justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-50 transition-colors duration-300 group-hover:bg-orange-100">
                    <i class="fa-solid fa-user-tie text-xl text-[#E89000]"></i>
                </div>

                <i class="fa-solid fa-arrow-up-right-from-square text-sm text-gray-300 transition-all duration-300 group-hover:translate-x-1 group-hover:-translate-y-1 group-hover:text-[#E89000]"></i>
            </div>

            <h3 class="font-semibold text-gray-900 transition-colors group-hover:text-[#E89000]">
                Add Receptionist
            </h3>

            <p class="mt-2 text-sm leading-relaxed text-gray-500">
                Register a new receptionist account.
            </p>

            <div class="mt-5 flex items-center gap-2 text-xs font-semibold text-[#E89000]">
                <span>Create Account</span>
                <i class="fa-solid fa-arrow-right text-[10px] transition-transform group-hover:translate-x-1"></i>
            </div>
        </button>

        <!-- Add Mechanic -->
        <button type="button"
            onclick="openStaffModal('mechanic')"
            class="group relative w-full cursor-pointer overflow-hidden rounded-xl border border-gray-200 bg-white p-5 text-left transition-all duration-300 hover:-translate-y-1 hover:border-blue-500 hover:shadow-lg hover:shadow-blue-100/50">

            <div class="mb-5 flex items-center justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 transition-colors duration-300 group-hover:bg-blue-100">
                    <i class="fa-solid fa-screwdriver-wrench text-xl text-blue-600"></i>
                </div>

                <i class="fa-solid fa-arrow-up-right-from-square text-sm text-gray-300 transition-all duration-300 group-hover:translate-x-1 group-hover:-translate-y-1 group-hover:text-blue-600"></i>
            </div>

            <h3 class="font-semibold text-gray-900 transition-colors group-hover:text-blue-600">
                Add Mechanic
            </h3>

            <p class="mt-2 text-sm leading-relaxed text-gray-500">
                Create a mechanic user account.
            </p>

            <div class="mt-5 flex items-center gap-2 text-xs font-semibold text-blue-600">
                <span>Create Account</span>
                <i class="fa-solid fa-arrow-right text-[10px] transition-transform group-hover:translate-x-1"></i>
            </div>
        </button>

        <!-- Add Admin -->
        <a href="add-admin.php"
            class="group relative overflow-hidden rounded-xl border border-gray-200 bg-white p-5 transition-all duration-300 hover:-translate-y-1 hover:border-green-500 hover:shadow-lg hover:shadow-green-100/50">

            <div class="mb-5 flex items-center justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-50 transition-colors duration-300 group-hover:bg-green-100">
                    <i class="fa-solid fa-user-shield text-xl text-green-600"></i>
                </div>

                <i class="fa-solid fa-arrow-up-right-from-square text-sm text-gray-300 transition-all duration-300 group-hover:translate-x-1 group-hover:-translate-y-1 group-hover:text-green-600"></i>
            </div>

            <h3 class="font-semibold text-gray-900 transition-colors group-hover:text-green-600">
                Add Admin
            </h3>

            <p class="mt-2 text-sm leading-relaxed text-gray-500">
                Create an administrator account.
            </p>

            <div class="mt-5 flex items-center gap-2 text-xs font-semibold text-green-600">
                <span>Create Account</span>
                <i class="fa-solid fa-arrow-right text-[10px] transition-transform group-hover:translate-x-1"></i>
            </div>
        </a>

        <!-- Manage Users -->
        <a href="users.php"
            class="group relative overflow-hidden rounded-xl border border-gray-200 bg-white p-5 transition-all duration-300 hover:-translate-y-1 hover:border-purple-500 hover:shadow-lg hover:shadow-purple-100/50">

            <div class="mb-5 flex items-center justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-50 transition-colors duration-300 group-hover:bg-purple-100">
                    <i class="fa-solid fa-users text-xl text-purple-600"></i>
                </div>

                <i class="fa-solid fa-arrow-up-right-from-square text-sm text-gray-300 transition-all duration-300 group-hover:translate-x-1 group-hover:-translate-y-1 group-hover:text-purple-600"></i>
            </div>

            <h3 class="font-semibold text-gray-900 transition-colors group-hover:text-purple-600">
                Manage Users
            </h3>

            <p class="mt-2 text-sm leading-relaxed text-gray-500">
                Edit, update or remove user accounts.
            </p>

            <div class="mt-5 flex items-center gap-2 text-xs font-semibold text-purple-600">
                <span>View Users</span>
                <i class="fa-solid fa-arrow-right text-[10px] transition-transform group-hover:translate-x-1"></i>
            </div>
        </a>

    </div>
</section>


   
</section>


        </div>
    </main>
</div>


<!-- ADD STAFF REGISTRATION MODAL -->
<div id="staffModal"
    class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 p-4">

    <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl">

        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5">

            <div class="flex items-center gap-3">
                <div id="staffModalIcon"
                    class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-100 text-[#E89000]">
                    <i class="fa-solid fa-user-plus text-lg"></i>
                </div>

                <div>
                    <h2 id="staffModalTitle" class="text-lg font-bold text-gray-900">
                        Register Staff
                    </h2>
                    <p class="mt-0.5 text-xs text-gray-500">
                        Create a new staff account
                    </p>
                </div>
            </div>

            <button type="button" onclick="closeStaffModal()"
                class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-lg text-gray-400 transition hover:bg-gray-100 hover:text-gray-700">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Registration Form -->
        <form method="POST" action="userManagement.php" class="space-y-4 p-6">

            <input type="hidden" name="register_staff" value="1">
            <input type="hidden" name="role" id="staffRole">

            <input type="hidden" name="csrf_token"
                value="<?php echo htmlspecialchars($_SESSION['staff_csrf_token']); ?>">

            <!-- Full Name -->
            <div>
                <label class="mb-1.5 block text-xs font-semibold text-gray-700">
                    Full Name
                </label>

                <input type="text" name="full_name" required
                    placeholder="Enter full name"
                    class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-[#E89000] focus:ring-2 focus:ring-orange-100">
            </div>

            <!-- Phone -->
            <div>
                <label class="mb-1.5 block text-xs font-semibold text-gray-700">
                    Phone Number
                </label>

                <input type="tel" name="phone" required
                    placeholder="Enter phone number"
                    class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-[#E89000] focus:ring-2 focus:ring-orange-100">
            </div>

            <!-- Email -->
            <div>
                <label class="mb-1.5 block text-xs font-semibold text-gray-700">
                    Email Address
                </label>

                <input type="email" name="email" required
                    placeholder="Enter email address"
                    class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-[#E89000] focus:ring-2 focus:ring-orange-100">
            </div>

            <!-- Password -->
            <div>
                <label class="mb-1.5 block text-xs font-semibold text-gray-700">
                    Password
                </label>

                <input type="password" name="password" required minlength="8"
                    placeholder="Enter password (minimum 8 characters)"
                    class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-[#E89000] focus:ring-2 focus:ring-orange-100">
            </div>

            <!-- Confirm Password -->
            <div>
                <label class="mb-1.5 block text-xs font-semibold text-gray-700">
                    Confirm Password
                </label>

                <input type="password" name="confirm_password" required minlength="8"
                    placeholder="Re-enter password"
                    class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-[#E89000] focus:ring-2 focus:ring-orange-100">
            </div>

            <!-- Modal Buttons -->
            <div class="flex justify-end gap-3 border-t border-gray-100 pt-5">

                <button type="button" onclick="closeStaffModal()"
                    class="rounded-lg border border-gray-200 px-5 py-2.5 text-xs font-semibold text-gray-600 transition hover:bg-gray-100">
                    Cancel
                </button>

                <button type="submit" id="staffSubmitButton"
                    class="rounded-lg bg-[#E89000] px-5 py-2.5 text-xs font-semibold text-white transition hover:bg-orange-600">
                    Register Account
                </button>

            </div>

        </form>
    </div>
</div>
<script>
    
function openStaffModal(role) {
    const modal = document.getElementById('staffModal');
    const title = document.getElementById('staffModalTitle');
    const roleInput = document.getElementById('staffRole');
    const icon = document.getElementById('staffModalIcon');
    const submitButton = document.getElementById('staffSubmitButton');

    roleInput.value = role;

    if (role === 'receptionist') {
        title.textContent = 'Register New Receptionist';
        icon.className = 'flex h-11 w-11 items-center justify-center rounded-xl bg-orange-100 text-[#E89000]';
        icon.innerHTML = '<i class="fa-solid fa-user-tie text-lg"></i>';

        submitButton.className = 'rounded-lg bg-[#E89000] px-5 py-2.5 text-xs font-semibold text-white transition hover:bg-orange-600';
    } else if (role === 'mechanic') {
        title.textContent = 'Register New Mechanic';
        icon.className = 'flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100 text-blue-600';
        icon.innerHTML = '<i class="fa-solid fa-screwdriver-wrench text-lg"></i>';

        submitButton.className = 'rounded-lg bg-blue-600 px-5 py-2.5 text-xs font-semibold text-white transition hover:bg-blue-700';
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('overflow-hidden');
}

function closeStaffModal() {
    const modal = document.getElementById('staffModal');

    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.classList.remove('overflow-hidden');
}

// Close when clicking outside the modal
document.getElementById('staffModal').addEventListener('click', function(event) {
    if (event.target === this) {
        closeStaffModal();
    }
});

// Close using Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeStaffModal();
    }
});

</script>
</body>

</html>