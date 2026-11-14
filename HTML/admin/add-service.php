
<?php
session_start();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: services.php");
    exit();
}

try {

    // Connect to AutoPulse database
    $conn = new mysqli("localhost", "root", "", "autopulse");
    $conn->set_charset("utf8mb4");

    // Get submitted form data
    $serviceName = trim($_POST["service_name"] ?? "");
    $serviceType = trim($_POST["category"] ?? "");
    $price = trim($_POST["price"] ?? "");
    $duration = trim($_POST["duration"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $status = strtolower(trim($_POST["status"] ?? "active"));

    // Validate required fields
    if (
        $serviceName === "" ||
        $serviceType === "" ||
        $price === "" ||
        $duration === "" ||
        $description === ""
    ) {
        throw new Exception("Please complete all required fields.");
    }

    // Validate price
    if (!is_numeric($price) || (float)$price < 0) {
        throw new Exception("Please enter a valid service price.");
    }

    // Validate service category
    $allowedCategories = [
        "maintenance",
        "repair",
        "inspection",
        "cleaning",
        "electrical",
        "other"
    ];

    if (!in_array($serviceType, $allowedCategories, true)) {
        throw new Exception("Please select a valid service category.");
    }

    // Convert status into database value
    if ($status === "active") {
        $isActive = 1;
    } elseif ($status === "inactive") {
        $isActive = 0;
    } else {
        throw new Exception("Please select a valid service status.");
    }

    // Insert new service into database
    $sql = "
        INSERT INTO services (
            service_name,
            service_type,
            description,
            price,
            is_active,
            estimate_duration
        )
        VALUES (?, ?, ?, ?, ?, ?)
    ";

    $stmt = $conn->prepare($sql);

    $servicePrice = (float)$price;

    $stmt->bind_param(
        "sssdis",
        $serviceName,
        $serviceType,
        $description,
        $servicePrice,
        $isActive,
        $duration
    );

    $stmt->execute();

    $stmt->close();
    $conn->close();

    // Return to service management page
    header("Location: services.php?added=1");
    exit();

} catch (Exception $e) {

    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }

    error_log("Add service error: " . $e->getMessage());

    header("Location: services.php?error=" . urlencode($e->getMessage()));
    exit();
}
?>
