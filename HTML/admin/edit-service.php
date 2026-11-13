<?php
session_start();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/*
|--------------------------------------------------------------------------
| Redirect helper
|--------------------------------------------------------------------------
*/
function redirectWithMessage($type, $message)
{
    header("Location: services.php?" . $type . "=" . urlencode($message));
    exit();
}

/*
|--------------------------------------------------------------------------
| Only accept POST requests
|--------------------------------------------------------------------------
*/
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: services.php");
    exit();
}

$conn = null;

try {
    /*
    |--------------------------------------------------------------------------
    | Database connection
    |--------------------------------------------------------------------------
    */
    $conn = new mysqli("localhost", "root", "", "autopulse");
    $conn->set_charset("utf8mb4");

    /*
    |--------------------------------------------------------------------------
    | CSRF validation
    |--------------------------------------------------------------------------
    */
    $postedToken = $_POST["csrf_token"] ?? "";
    $sessionToken = $_SESSION["csrf_token"] ?? "";

    if ($sessionToken === "" || !hash_equals($sessionToken, $postedToken)) {
        throw new Exception("Security validation failed. Please refresh the page and try again.");
    }

    /*
    |--------------------------------------------------------------------------
    | Get submitted action and service ID
    |--------------------------------------------------------------------------
    */
    $action = $_POST["action"] ?? "";
    $serviceId = filter_input(INPUT_POST, "service_id", FILTER_VALIDATE_INT);

    if (!$serviceId || $serviceId < 1) {
        throw new Exception("Invalid service selected.");
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE SERVICE
    |--------------------------------------------------------------------------
    */
    if ($action === "update") {

        $serviceName = trim($_POST["service_name"] ?? "");
        $serviceType = strtolower(trim($_POST["category"] ?? ""));
        $description = trim($_POST["description"] ?? "");
        $price = trim($_POST["price"] ?? "");
        $duration = trim($_POST["duration"] ?? "");
        $status = strtolower(trim($_POST["status"] ?? ""));

        // Validate required fields
        if (
            $serviceName === "" ||
            $serviceType === "" ||
            $description === "" ||
            $price === "" ||
            $duration === ""
        ) {
            throw new Exception("Please complete all required fields.");
        }

        // Validate price
        if (!is_numeric($price) || (float)$price < 0) {
            throw new Exception("Please enter a valid service price.");
        }

        // Validate category
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

        // Validate status
        if ($status === "active") {
            $isActive = 1;
        } elseif ($status === "inactive") {
            $isActive = 0;
        } else {
            throw new Exception("Please select a valid service status.");
        }

        /*
         * Update the selected service.
         */
        $sql = "
            UPDATE services
            SET
                service_name = ?,
                service_type = ?,
                description = ?,
                price = ?,
                estimate_duration = ?,
                is_active = ?
            WHERE id = ?
        ";

        $stmt = $conn->prepare($sql);

        $servicePrice = (float)$price;

        $stmt->bind_param(
            "sssdsii",
            $serviceName,
            $serviceType,
            $description,
            $servicePrice,
            $duration,
            $isActive,
            $serviceId
        );

        $stmt->execute();

        if ($stmt->affected_rows >= 0) {
            $stmt->close();
            $conn->close();

            redirectWithMessage("updated", "Service details updated successfully.");
        }

        $stmt->close();

        throw new Exception("Unable to update this service.");
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE SERVICE
    |--------------------------------------------------------------------------
    */
    elseif ($action === "delete") {

        $stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
        $stmt->bind_param("i", $serviceId);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            $stmt->close();
            throw new Exception("The selected service could not be found.");
        }

        $stmt->close();
        $conn->close();

        redirectWithMessage("deleted", "Service deleted successfully.");
    }

    else {
        throw new Exception("Invalid service action.");
    }

} catch (mysqli_sql_exception $e) {

    /*
     * A service may already be referenced by appointment_services.
     * MySQL may prevent deletion because of that foreign key.
     */
    if ((int)$e->getCode() === 1451) {
        redirectWithMessage(
            "error",
            "This service is already linked to appointments and cannot be permanently deleted. Change its status to Inactive instead."
        );
    }

    error_log("Service management database error: " . $e->getMessage());

    redirectWithMessage(
        "error",
        "A database error occurred. Please check the service details and try again."
    );

} catch (Exception $e) {

    if ($conn instanceof mysqli) {
        $conn->close();
    }

    redirectWithMessage("error", $e->getMessage());
}
?>