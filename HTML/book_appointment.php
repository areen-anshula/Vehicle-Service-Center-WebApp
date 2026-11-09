
<?php
session_start();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
date_default_timezone_set('Asia/Colombo');

/* =========================================
   1. CLIENT LOGIN PROTECTION
========================================= */

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'client' ||
    (int)$_SESSION['user_id'] <= 0
) {
    header("Location: login.html");
    exit();
}

/* =========================================
   2. DATABASE CONNECTION
========================================= */

try {
    $conn = new mysqli("localhost", "root", "", "autopulse");
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    exit("Database connection failed. Please try again later.");
}

/* =========================================
   3. HELPER FUNCTION
========================================= */

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function postText($key) {
    $value = $_POST[$key] ?? '';
    return is_string($value) ? trim($value) : '';
}

/* =========================================
   4. INITIALIZE FORM VARIABLES
========================================= */

$errorMessage = "";

$bookingSuccess = (
    isset($_GET['booking']) &&
    $_GET['booking'] === 'success'
);

$registration = '';
$brand = '';
$model = '';
$year = '';
$appointmentDate = '';
$appointmentTime = '';
$additionalNote = '';

$selectedServices = [];

/* =========================================
   5. GENERATE CSRF TOKEN
========================================= */

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/* =========================================
   6. HANDLE APPOINTMENT SUBMISSION
========================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $transactionStarted = false;

    // Preserve entered values if validation fails
    $registration = postText('vehicle_registration_number');
    $brand = postText('brand');
    $model = postText('model');
    $year = postText('manufacture_year');

    $appointmentDate = postText('date');
    $appointmentTime = postText('time');
    $additionalNote = postText('additional_note');

    $selectedServices = $_POST['services'] ?? [];

    try {

        /* -------------------------------------
           A. VERIFY CSRF TOKEN
        ------------------------------------- */

        $submittedToken = $_POST['csrf_token'] ?? '';

        if (
            !is_string($submittedToken) ||
            !hash_equals($_SESSION['csrf_token'], $submittedToken)
        ) {
            throw new Exception(
                "Invalid form submission. Please refresh the page."
            );
        }

        /* -------------------------------------
           B. GET LOGGED-IN CLIENT
        ------------------------------------- */

        $user_id = (int)$_SESSION['user_id'];

        /* -------------------------------------
           C. VALIDATE VEHICLE INFORMATION
        ------------------------------------- */

        if (
            $registration === '' ||
            $brand === '' ||
            $model === '' ||
            $year === ''
        ) {
            throw new Exception(
                "Please complete all vehicle information."
            );
        }

        if (
            strlen($registration) > 30 ||
            strlen($brand) > 100 ||
            strlen($model) > 100
        ) {
            throw new Exception(
                "Vehicle information exceeds the allowed length."
            );
        }

        $manufacture_year = filter_var(
            $year,
            FILTER_VALIDATE_INT
        );

        if (
            $manufacture_year === false ||
            $manufacture_year < 1886 ||
            $manufacture_year > (int)date('Y')
        ) {
            throw new Exception(
                "Please select a valid manufacturing year."
            );
        }

        /* -------------------------------------
           D. VALIDATE APPOINTMENT DATE
        ------------------------------------- */

        $dateObject = DateTime::createFromFormat(
            '!Y-m-d',
            $appointmentDate
        );

        if (
            !$dateObject ||
            $dateObject->format('Y-m-d') !== $appointmentDate
        ) {
            throw new Exception(
                "Please select a valid appointment date."
            );
        }

        if ($appointmentDate < date('Y-m-d')) {
            throw new Exception(
                "Appointment date cannot be in the past."
            );
        }

        /* -------------------------------------
           E. VALIDATE APPOINTMENT TIME
        ------------------------------------- */

        $allowedTimes = [
            '09:00',
            '09:30',
            '10:00',
            '10:30',
            '11:00',
            '11:30',
            '14:00',
            '14:30',
            '15:00'
        ];

        if (!in_array($appointmentTime, $allowedTimes, true)) {
            throw new Exception(
                "Please select a valid appointment time."
            );
        }

        /* -------------------------------------
           F. VALIDATE ADDITIONAL NOTES
        ------------------------------------- */

        if (strlen($additionalNote) > 200) {
            throw new Exception(
                "Additional notes cannot exceed 200 characters."
            );
        }

        /* -------------------------------------
           G. VALIDATE SELECTED SERVICES
        ------------------------------------- */

        if (
            !is_array($selectedServices) ||
            empty($selectedServices)
        ) {
            throw new Exception(
                "Please select at least one service."
            );
        }

        $serviceIds = [];

        foreach ($selectedServices as $serviceId) {

            $validatedId = filter_var(
                $serviceId,
                FILTER_VALIDATE_INT
            );

            if (
                $validatedId === false ||
                $validatedId <= 0
            ) {
                throw new Exception(
                    "Invalid service selection."
                );
            }

            $serviceIds[] = $validatedId;
        }

        // Remove duplicate service selections
        $serviceIds = array_values(array_unique($serviceIds));

        /* =====================================
           7. START DATABASE TRANSACTION
        ===================================== */

        $conn->begin_transaction();
        $transactionStarted = true;

        /* -------------------------------------
           A. VERIFY SELECTED SERVICES
           B. GET ACTUAL DATABASE PRICES
        ------------------------------------- */

        $verifiedServices = [];

        $serviceStmt = $conn->prepare("
            SELECT id, price
            FROM services
            WHERE id = ?
            AND is_active = 1
        ");

        foreach ($serviceIds as $serviceId) {

            $serviceStmt->bind_param("i", $serviceId);
            $serviceStmt->execute();

            $result = $serviceStmt->get_result();

            if ($result->num_rows !== 1) {
                throw new Exception(
                    "One or more selected services are unavailable."
                );
            }

            $service = $result->fetch_assoc();

            $verifiedServices[] = [
                'id' => (int)$service['id'],
                'price' => (float)$service['price']
            ];
        }

        $serviceStmt->close();

        /* =====================================
           8. INSERT APPOINTMENT
        ===================================== */

        $status = "Pending";

        $appointmentStmt = $conn->prepare("
            INSERT INTO appointment (
                user_id,
                vehicle_registration_number,
                brand,
                model,
                manufacture_year,
                `date`,
                `time`,
                additional_note,
                status
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $appointmentStmt->bind_param(
            "isssissss",
            $user_id,
            $registration,
            $brand,
            $model,
            $manufacture_year,
            $appointmentDate,
            $appointmentTime,
            $additionalNote,
            $status
        );

        $appointmentStmt->execute();

        // Get newly inserted appointment ID
        $appointment_id = $conn->insert_id;

        $appointmentStmt->close();

        /* =====================================
           9. INSERT APPOINTMENT SERVICES
        ===================================== */

        $appointmentServiceStmt = $conn->prepare("
            INSERT INTO appointment_services (
                appointment_id,
                service_id,
                service_price
            )
            VALUES (?, ?, ?)
        ");

        foreach ($verifiedServices as $service) {

            $service_id = $service['id'];
            $service_price = $service['price'];

            $appointmentServiceStmt->bind_param(
                "iid",
                $appointment_id,
                $service_id,
                $service_price
            );

            $appointmentServiceStmt->execute();
        }

        $appointmentServiceStmt->close();

        /* =====================================
           10. COMMIT TRANSACTION
        ===================================== */

        $conn->commit();
        $transactionStarted = false;

        // Generate new CSRF token
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        // Redirect after successful booking
        header("Location: book_appointment.php?booking=success");
        exit();

    } catch (Throwable $e) {

    if ($transactionStarted) {
        try {
            $conn->rollback();
        } catch (Throwable $rollbackError) {
            error_log($rollbackError->getMessage());
        }
    }

    // TEMPORARY DEBUGGING
    $errorMessage = $e->getMessage();

    error_log("AutoPulse Appointment Error: " . $e->getMessage());
}
}

/* =========================================
   11. LOAD ACTIVE SERVICES FROM DATABASE
========================================= */

$availableServices = [];
$serviceLoadError = false;

try {

    $serviceQuery = $conn->query("
        SELECT
            id,
            service_name,
            service_type,
            description,
            price,
            estimate_duration
        FROM services
        WHERE is_active = 1
        ORDER BY service_name ASC
    ");

    while ($service = $serviceQuery->fetch_assoc()) {
        $availableServices[] = $service;
    }

} catch (mysqli_sql_exception $e) {

    $serviceLoadError = true;

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>AutoPulse | Book Appointment</title>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4.3.3"></script>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css"
    >
</head>

<body class="min-h-screen bg-[#f5f5f5] font-sans text-[#171717]">

    <!-- HEADER -->

    <header class="sticky top-0 z-50 flex min-h-[70px] items-center bg-black px-4 py-0 shadow-md md:px-[6%]">

        <a href="homepage.php" aria-label="AutoPulse Home">

            <img
                src="../MEDIA/AutoPulse Automotive Service Logo.png"
                alt="AutoPulse Logo"
                class="w-[110px] md:w-[125px]"
            >

        </a>

    </header>

    <main class="mx-auto mt-6 w-full max-w-7xl p-3 sm:p-5">

        <!-- SUCCESS MESSAGE -->

        <?php if ($bookingSuccess): ?>

            <div class="mb-4 flex items-start gap-3 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-700">

                <i class="fa-solid fa-circle-check mt-0.5 text-lg"></i>

                <p>
                    Your appointment has been successfully submitted!
                </p>

            </div>

        <?php endif; ?>

        <!-- ERROR MESSAGE -->

        <?php if ($errorMessage !== ""): ?>

            <div class="mb-4 flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">

                <i class="fa-solid fa-circle-exclamation mt-0.5 text-lg"></i>

                <p><?php echo e($errorMessage); ?></p>

            </div>

        <?php endif; ?>

        <!-- APPOINTMENT BOOKING FORM -->

        <form
            action="book_appointment.php"
            method="POST"
            class="space-y-4"
        >

            <input
                type="hidden"
                name="csrf_token"
                value="<?php echo e($_SESSION['csrf_token']); ?>"
            >

            <!-- VEHICLE INFORMATION -->

            <section class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">

                <h2 class="mb-4 flex items-center gap-2 text-base font-bold">

                    <i class="fa-solid fa-car text-lg text-[#E89000]"></i>

                    Vehicle Information

                </h2>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

                    <!-- REGISTRATION NUMBER -->

                    <div>

                        <label for="registration" class="mb-1.5 block text-xs font-semibold">

                            Vehicle Registration Number
                            <span class="text-red-500">*</span>

                        </label>

                        <div class="flex h-10 items-center gap-2 rounded-lg border border-gray-300 px-3 focus-within:border-[#E89000]">

                            <i class="fa-solid fa-car-side text-gray-400"></i>

                            <input
                                type="text"
                                id="registration"
                                name="vehicle_registration_number"
                                value="<?php echo e($registration); ?>"
                                placeholder="Enter registration number"
                                maxlength="30"
                                required
                                class="w-full bg-transparent text-xs uppercase outline-none placeholder:normal-case"
                            >

                        </div>

                    </div>

                    <!-- BRAND -->

                    <div>

                        <label for="brand" class="mb-1.5 block text-xs font-semibold">

                            Brand
                            <span class="text-red-500">*</span>

                        </label>

                        <select
                            id="brand"
                            name="brand"
                            required
                            class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-xs outline-none focus:border-[#E89000]"
                        >

                            <option value="" disabled <?php echo $brand === '' ? 'selected' : ''; ?>>
                                Select Brand
                            </option>

                            <?php
                            $brands = [
                                "BMW",
                                "Mercedes-Benz",
                                "Audi",
                                "Toyota",
                                "Honda",
                                "Nissan",
                                "Hyundai",
                                "Other"
                            ];
                            ?>

                            <?php foreach ($brands as $brandOption): ?>

                                <option
                                    value="<?php echo e($brandOption); ?>"
                                    <?php echo $brand === $brandOption ? 'selected' : ''; ?>
                                >

                                    <?php echo e($brandOption); ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <!-- MODEL -->

                    <div>

                        <label for="model" class="mb-1.5 block text-xs font-semibold">

                            Model
                            <span class="text-red-500">*</span>

                        </label>

                        <input
                            type="text"
                            id="model"
                            name="model"
                            value="<?php echo e($model); ?>"
                            placeholder="Enter vehicle model"
                            maxlength="100"
                            required
                            class="h-10 w-full rounded-lg border border-gray-300 px-3 text-xs outline-none focus:border-[#E89000]"
                        >

                    </div>

                    <!-- MANUFACTURING YEAR -->

                    <div>

                        <label for="year" class="mb-1.5 block text-xs font-semibold">

                            Manufacturing Year
                            <span class="text-red-500">*</span>

                        </label>

                        <select
                            id="year"
                            name="manufacture_year"
                            required
                            class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-xs outline-none focus:border-[#E89000]"
                        >

                            <option value="" disabled <?php echo $year === '' ? 'selected' : ''; ?>>
                                Select Year
                            </option>

                            <?php for ($y = (int)date('Y'); $y >= 1886; $y--): ?>

                                <option
                                    value="<?php echo $y; ?>"
                                    <?php echo $year === (string)$y ? 'selected' : ''; ?>
                                >

                                    <?php echo $y; ?>

                                </option>

                            <?php endfor; ?>

                        </select>

                    </div>

                </div>

            </section>

            <!-- SERVICE SELECTION -->

            <section class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">

                <div class="mb-1 flex items-center gap-2">

                    <i class="fa-solid fa-screwdriver-wrench text-lg text-[#E89000]"></i>

                    <h2 class="text-base font-bold">
                        Select Service Types
                    </h2>

                </div>

                <p class="mb-4 text-xs text-gray-500">

                    You can select multiple services for your vehicle.

                </p>

                <?php if ($serviceLoadError): ?>

                    <p class="rounded-lg bg-red-50 p-3 text-xs text-red-600">

                        Unable to load available services. Please try again later.

                    </p>

                <?php elseif (empty($availableServices)): ?>

                    <p class="rounded-lg bg-yellow-50 p-3 text-xs text-yellow-700">

                        No active services are currently available.

                    </p>

                <?php else: ?>

                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">

                        <?php foreach ($availableServices as $service): ?>

                            <?php

                            $serviceName = $service['service_name'];

                            $searchText = strtolower(
                                $serviceName . ' ' . $service['service_type']
                            );

                            $icon = 'fa-screwdriver-wrench';

                            if (strpos($searchText, 'engine') !== false) {

                                $icon = 'fa-gears';

                            } elseif (
                                strpos($searchText, 'wheel') !== false ||
                                strpos($searchText, 'tyre') !== false ||
                                strpos($searchText, 'tire') !== false
                            ) {

                                $icon = 'fa-life-ring';

                            } elseif (strpos($searchText, 'brake') !== false) {

                                $icon = 'fa-compact-disc';

                            } elseif (
                                strpos($searchText, 'clean') !== false ||
                                strpos($searchText, 'wash') !== false
                            ) {

                                $icon = 'fa-car';

                            } elseif (strpos($searchText, 'battery') !== false) {

                                $icon = 'fa-car-battery';

                            } elseif (
                                strpos($searchText, 'air condition') !== false ||
                                strpos($searchText, 'air conditioning') !== false ||
                                strpos($searchText, 'ac service') !== false
                            ) {

                                $icon = 'fa-snowflake';

                            }

                            ?>

                            <label class="relative cursor-pointer">

                                <input
                                    type="checkbox"
                                    name="services[]"
                                    value="<?php echo (int)$service['id']; ?>"
                                    <?php
                                    echo in_array(
                                        (string)$service['id'],
                                        array_map('strval', is_array($selectedServices) ? $selectedServices : []),
                                        true
                                    ) ? 'checked' : '';
                                    ?>
                                    class="peer sr-only"
                                >

                                <div class="flex h-[105px] flex-col items-center justify-center rounded-lg border border-gray-200 bg-white px-1 text-center shadow-sm transition-all hover:border-orange-400 peer-checked:border-[#E89000] peer-checked:bg-orange-50">

                                    <i class="fa-solid <?php echo e($icon); ?> text-2xl text-gray-700"></i>

                                    <span class="mt-2 text-[13px] font-bold">

                                        <?php echo e($serviceName); ?>

                                    </span>

                                    <span class="mt-1 line-clamp-2 text-[11px] text-gray-500">

                                        <?php echo e($service['description']); ?>

                                    </span>

                                </div>

                                <span class="absolute right-2 top-2 hidden h-5 w-5 items-center justify-center rounded-full bg-[#E89000] text-[10px] text-white peer-checked:flex">

                                    <i class="fa-solid fa-check"></i>

                                </span>

                            </label>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </section>

            <!-- DATE, TIME AND NOTES -->

            <section class="grid grid-cols-1 gap-4 md:grid-cols-3">

                <!-- DATE -->

                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">

                    <h2 class="mb-4 flex items-center gap-2 text-base font-bold">

                        <i class="fa-regular fa-calendar-days text-[#E89000]"></i>

                        Choose Date

                    </h2>

                    <label for="appointment_date" class="mb-2 block text-xs font-semibold">

                        Appointment Date
                        <span class="text-red-500">*</span>

                    </label>

                    <div class="flex h-10 items-center gap-2 rounded-lg border border-gray-300 px-3 focus-within:border-[#E89000]">

                        <i class="fa-regular fa-calendar text-gray-400"></i>

                        <input
                            type="date"
                            id="appointment_date"
                            name="date"
                            value="<?php echo e($appointmentDate); ?>"
                            min="<?php echo date('Y-m-d'); ?>"
                            required
                            class="w-full bg-transparent text-xs outline-none"
                        >

                    </div>

                </div>

                <!-- TIME -->

                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">

                    <h2 class="mb-4 flex items-center gap-2 text-base font-bold">

                        <i class="fa-regular fa-clock text-[#E89000]"></i>

                        Select Time

                    </h2>

                    <p class="mb-3 text-xs text-gray-500">

                        Choose your preferred appointment time.

                    </p>

                    <div class="grid grid-cols-3 gap-2">

                        <?php

                        $timeSlots = [
                            "09:00" => "09:00 AM",
                            "09:30" => "09:30 AM",
                            "10:00" => "10:00 AM",
                            "10:30" => "10:30 AM",
                            "11:00" => "11:00 AM",
                            "11:30" => "11:30 AM",
                            "14:00" => "02:00 PM",
                            "14:30" => "02:30 PM",
                            "15:00" => "03:00 PM"
                        ];

                        ?>

                        <?php foreach ($timeSlots as $timeValue => $timeLabel): ?>

                            <label class="cursor-pointer">

                                <input
                                    type="radio"
                                    name="time"
                                    value="<?php echo e($timeValue); ?>"
                                    <?php echo $appointmentTime === $timeValue ? 'checked' : ''; ?>
                                    required
                                    class="peer sr-only"
                                >

                                <span class="flex h-9 items-center justify-center rounded-full border border-gray-200 text-[10px] font-medium transition peer-checked:border-orange-500 peer-checked:bg-[#E89000] peer-checked:text-white hover:border-orange-400">

                                    <?php echo e($timeLabel); ?>

                                </span>

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

                    <label for="additional_note" class="mb-2 block text-xs font-semibold">

                        Describe the problem (Optional)

                    </label>

                    <textarea
                        id="additional_note"
                        name="additional_note"
                        maxlength="200"
                        rows="5"
                        placeholder="Describe any problems with your vehicle..."
                        class="w-full resize-none rounded-lg border border-gray-300 p-3 text-xs leading-5 outline-none placeholder:text-gray-400 focus:border-orange-500"
                    ><?php echo e($additionalNote); ?></textarea>

                    <p class="mt-1 text-right text-[10px] text-gray-400">

                        Maximum 200 characters

                    </p>

                </div>

            </section>

            <!-- FORM BUTTONS -->

            <div class="flex flex-col-reverse justify-between gap-3 sm:flex-row sm:items-center">

                <a
                    href="homepage.php"
                    class="flex h-11 items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-6 text-xs font-semibold text-gray-600 shadow-sm transition hover:bg-gray-100"
                >

                    <i class="fa-solid fa-arrow-left"></i>

                    Cancel

                </a>

                <button
                    type="submit"
                    <?php echo ($serviceLoadError || empty($availableServices)) ? 'disabled' : ''; ?>
                    class="flex h-11 cursor-pointer items-center justify-center gap-3 rounded-lg bg-[#FFA500] px-4 text-xs font-bold text-white shadow-sm transition hover:bg-[#E89000] disabled:cursor-not-allowed disabled:opacity-50 sm:min-w-[200px]"
                >

                    <i class="fa-solid fa-arrow-up-from-bracket"></i>

                    Submit

                </button>

            </div>

        </form>

    </main>

</body>
</html>
