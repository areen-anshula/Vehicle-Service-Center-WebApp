<?php
session_start();

$role = strtolower(trim($_SESSION['role'] ?? ''));
if (empty($_SESSION['user_id']) || !in_array($role, ['client', 'customer', 'receptionist'], true)) {
    $_SESSION['homepage_warning'] = 'Please log in as a client or receptionist to book an appointment.';
    header('Location: homepage.php');
    exit;
}

// Include database connection
// Ensure this file initializes a mysqli connection named $conn
require_once '../PHP/database.php';

$message = '';
$messageType = '';

$user_id = (int) $_SESSION['user_id'];

// ---------------------------------------------------------
// 1. HANDLE FORM SUBMISSION (POST)
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and fetch form data
    $registration = trim($_POST['registration'] ?? '');
    $brand = trim($_POST['brand'] ?? '');
    $model = trim($_POST['model'] ?? '');
    $year = trim($_POST['year'] ?? '');
    $date = trim($_POST['appointment_date'] ?? '');
    $time = trim($_POST['appointment_time'] ?? '');
    $note = trim($_POST['additional_note'] ?? '');
    $selected_services = $_POST['services'] ?? []; // Array of service IDs

    if (empty($selected_services)) {
        $message = "Please select at least one service.";
        $messageType = "error";
    } else {
        // Begin transaction to ensure both tables update successfully
        $conn->begin_transaction();

        try {
            // A. Insert into 'appointment' table
            $stmt = $conn->prepare("INSERT INTO appointment (user_id, vehicle_registration_number, brand, model, manufacture_year, date, time, additional_note, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
            $stmt->bind_param("isssssss", $user_id, $registration, $brand, $model, $year, $date, $time, $note);
            $stmt->execute();
            
            // Get the newly created Appointment ID
            $appointment_id = $stmt->insert_id;
            $stmt->close();

            // B. Insert into 'appointment_services' table
            $stmt_service = $conn->prepare("INSERT INTO appointment_services (appointment_id, service_id, service_price) VALUES (?, ?, ?)");
            $price_stmt = $conn->prepare("SELECT price FROM services WHERE id = ?");

            foreach ($selected_services as $service_id) {
                // Fetch the current price of the service directly from the database to prevent HTML tampering
                $price_stmt->bind_param("i", $service_id);
                $price_stmt->execute();
                $price_result = $price_stmt->get_result();
                
                if ($price_row = $price_result->fetch_assoc()) {
                    $service_price = $price_row['price'];
                    
                    // Link service to the appointment
                    $stmt_service->bind_param("iid", $appointment_id, $service_id, $service_price);
                    $stmt_service->execute();
                }
            }
            $price_stmt->close();
            $stmt_service->close();

            // Commit the transaction
            $conn->commit();
            $message = "Appointment booked successfully!";
            $messageType = "success";

        } catch (Exception $e) {
            // Rollback if any database insertion fails
            $conn->rollback();
            $message = "Error booking appointment. Please try again later.";
            $messageType = "error";
        }
    }
}

// ---------------------------------------------------------
// 2. FETCH ACTIVE SERVICES FOR THE HTML GRID
// ---------------------------------------------------------
$services_query = "SELECT id, service_name, description FROM services WHERE is_active = 1";
$services_result = $conn->query($services_query);

// Map service names to their respective FontAwesome icons to retain your UI design
$iconMap = [
    'Engine Service' => 'fa-battery-full',
    'Wheel and Tyre Service' => 'fa-life-ring',
    'Brake Repair' => 'fa-compact-disc',
    'Vehicle Cleaning' => 'fa-car',
    'Battery Service' => 'fa-car-battery',
    'AC Service' => 'fa-snowflake'
];
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
<body class="min-h-screen bg-[#f5f5f5] font-sans text-[#171717]">
    <header class="sticky top-0 z-50 flex min-h-[70px] items-center bg-black px-4 py-0 shadow-md md:px-[6%]">
        <a href="homepage.html" aria-label="AutoPulse Home">
            <img src="../MEDIA/AutoPulse Automotive Service Logo.png" alt="AutoPulse Logo" class="w-[110px] md:w-[125px]">
        </a>
    </header>
    <main class="mx-auto mt-6 w-full max-w-7xl p-3 sm:p-5">
        
        <!-- Display Success or Error Notifications -->
        <?php if ($message): ?>
            <div class="mb-5 rounded-lg p-4 text-sm font-semibold <?php echo $messageType === 'success' ? 'bg-green-100 text-green-700 border-green-500' : 'bg-red-100 text-red-700 border-red-500'; ?> border-l-4">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Form automatically submits to itself using PHP_SELF -->
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="space-y-4">
            
            <!-- VEHICLE INFORMATION -->
            <section class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <h2 class="mb-4 flex items-center gap-2 text-base font-bold">
                    <i class="fa-solid fa-car text-lg text-[#E89000]"></i>
                    Vehicle Information
                </h2>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label for="registration" class="mb-1.5 block text-xs font-semibold">Vehicle Registration Number <span class="text-red-500">*</span></label>
                        <div class="flex h-10 items-center gap-2 rounded-lg border border-gray-300 px-3 focus-within:border-[#E89000]">
                            <i class="fa-solid fa-car-side text-gray-400"></i>
                            <input type="text" id="registration" name="registration" placeholder="Enter registration number" required class="w-full bg-transparent text-xs uppercase outline-none placeholder:normal-case">
                        </div>
                    </div>
                    <div>
                        <label for="brand" class="mb-1.5 block text-xs font-semibold">Brand <span class="text-red-500">*</span></label>
                        <select id="brand" name="brand" required class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-xs outline-none focus:border-[#E89000]">
                            <option value="" disabled selected>Select Brand</option>
                            <option value="BMW">BMW</option>
                            <option value="Mercedes-Benz">Mercedes-Benz</option>
                            <option value="Audi">Audi</option>
                            <option value="Toyota">Toyota</option>
                            <option value="Honda">Honda</option>
                            <option value="Nissan">Nissan</option>
                            <option value="Hyundai">Hyundai</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label for="model" class="mb-1.5 block text-xs font-semibold">Model <span class="text-red-500">*</span></label>
                        <input type="text" id="model" name="model" placeholder="Enter vehicle model" required class="h-10 w-full rounded-lg border border-gray-300 px-3 text-xs outline-none focus:border-[#E89000]">
                    </div>
                    <div>
                        <label for="year" class="mb-1.5 block text-xs font-semibold">Manufacturing Year <span class="text-red-500">*</span></label>
                        <select id="year" name="year" required class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-xs outline-none focus:border-[#E89000]">
                            <option value="" disabled selected>Select Year</option>
                            <?php 
                                $current_year = date("Y");
                                for($y = $current_year; $y >= 2000; $y--) {
                                    echo "<option value='$y'>$y</option>";
                                }
                            ?>
                        </select>
                    </div>
                </div>
            </section>

            <!-- SERVICE SELECTION (DYNAMICALLY FETCHED) -->
            <section class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="mb-1 flex items-center gap-2">
                    <i class="fa-solid fa-screwdriver-wrench text-lg text-[#E89000]"></i>
                    <h2 class="text-base font-bold">Select Service Types</h2>
                </div>
                <p class="mb-4 text-xs text-gray-500">You can select multiple services for your vehicle.</p>
                
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
                    <?php
                    // Loop through database results and generate checkboxes
                    if ($services_result && $services_result->num_rows > 0) {
                        while ($service = $services_result->fetch_assoc()) {
                            $s_id = $service['id'];
                            $s_name = htmlspecialchars($service['service_name']);
                            
                            // Shorten description for UI if it's too long
                            $s_desc = htmlspecialchars($service['description']);
                            if(strlen($s_desc) > 30) $s_desc = substr($s_desc, 0, 27) . '...';

                            // Determine which icon to show (default to wrench if not found)
                            $iconClass = isset($iconMap[$s_name]) ? $iconMap[$s_name] : 'fa-screwdriver-wrench';
                            ?>
                            
                            <label class="relative cursor-pointer">
                                <!-- Send the Database ID of the service inside the services[] array -->
                                <input type="checkbox" name="services[]" value="<?php echo $s_id; ?>" class="peer sr-only">
                                <div class="flex h-[105px] flex-col items-center justify-center rounded-lg border border-gray-200 bg-white px-1 text-center shadow-sm transition-all hover:border-orange-400 peer-checked:border-[#E89000] peer-checked:bg-orange-50">
                                    <i class="fa-solid <?php echo $iconClass; ?> text-2xl text-gray-700 peer-checked:text-orange-500"></i>
                                    <span class="mt-2 text-[13px] font-bold leading-tight"><?php echo $s_name; ?></span>
                                    <span class="mt-1 text-[10px] text-gray-500 leading-tight"><?php echo $s_desc; ?></span>
                                </div>
                                <span class="absolute right-2 top-2 hidden h-5 w-5 items-center justify-center rounded-full bg-[#E89000] text-[10px] text-white peer-checked:flex">
                                    <i class="fa-solid fa-check"></i>
                                </span>
                            </label>

                            <?php
                        }
                    } else {
                        // Fallback if no services are marked is_active = 1 in the DB
                        echo "<p class='col-span-full text-sm text-red-500 font-semibold'>No active services currently available in the database.</p>";
                    }
                    ?>
                </div>
            </section>

            <!-- DATE, TIME AND NOTES -->
            <section class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <!-- DATE -->
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <h2 class="mb-4 flex items-center gap-2 text-base font-bold">
                        <i class="fa-regular fa-calendar-days text-[#E89000]"></i>
                        Choose Date
                    </h2>
                    <label for="appointment_date" class="mb-2 block text-xs font-semibold">Appointment Date <span class="text-red-500">*</span></label>
                    <div class="flex h-10 items-center gap-2 rounded-lg border border-gray-300 px-3 focus-within:border-[#E89000]">
                        <i class="fa-regular fa-calendar text-gray-400"></i>
                        <input type="date" id="appointment_date" name="appointment_date" min="<?php echo date('Y-m-d'); ?>" required class="w-full bg-transparent text-xs outline-none">
                    </div>
                </div>
                <!-- TIME -->
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <h2 class="mb-4 flex items-center gap-2 text-base font-bold">
                        <i class="fa-regular fa-clock text-[#E89000]"></i>
                        Select Time
                    </h2>
                    <p class="mb-3 text-xs text-gray-500">Choose your preferred appointment time.</p>
                    <div class="grid grid-cols-3 gap-2">
                        <?php 
                        // Generate times dynamically
                        $times = ['09:00 AM', '09:30 AM', '10:00 AM', '10:30 AM', '11:00 AM', '11:30 AM', '02:00 PM', '02:30 PM', '03:00 PM'];
                        foreach($times as $timeOption): ?>
                            <label class="cursor-pointer">
                                <input type="radio" name="appointment_time" value="<?php echo $timeOption; ?>" required class="peer sr-only">
                                <span class="flex h-9 items-center justify-center rounded-full border border-gray-200 text-[10px] font-medium transition peer-checked:border-orange-500 peer-checked:bg-[#E89000] peer-checked:text-white hover:border-orange-400"><?php echo $timeOption; ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <!-- ADDITIONAL NOTES -->
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <h2 class="mb-4 flex items-center gap-2 text-base font-bold">
                        <i class="fa-regular fa-rectangle-list text-[#E89000]"></i>
                        Additional Notes
                    </h2>
                    <label for="additional_note" class="mb-2 block text-xs font-semibold">Describe the problem (Optional)</label>
                    <textarea id="additional_note" name="additional_note" maxlength="200" rows="5" placeholder="Describe any problems with your vehicle..." class="w-full resize-none rounded-lg border border-gray-300 p-3 text-xs leading-5 outline-none placeholder:text-gray-400 focus:border-orange-500"></textarea>
                    <p class="mt-1 text-right text-[10px] text-gray-400">Maximum 200 characters</p>
                </div>
            </section>

            <!-- FORM BUTTONS -->
            <div class="flex flex-col-reverse justify-between gap-3 sm:flex-row sm:items-center">
                <a href="homepage.html" class="flex h-11 items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-6 text-xs font-semibold text-gray-600 shadow-sm transition hover:bg-gray-100">
                    <i class="fa-solid fa-arrow-left"></i>Cancel                       
                </a>
                <button type="submit" class="flex h-11 items-center justify-center gap-3 rounded-lg bg-[#FFA500] px-4 text-xs font-bold text-white shadow-sm transition hover:bg-[#E89000] sm:min-w-[200px] cursor-pointer">
                    <i class="fa-solid fa-arrow-up-from-bracket" style="color: rgb(255, 255, 255);"></i>
                    Submit
                </button>
            </div>
        </form>
    </main>
</body>
</html>