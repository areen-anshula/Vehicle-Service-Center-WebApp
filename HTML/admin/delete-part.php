<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function redirectError($message) {
    header("Location: inventory.php?error=" . urlencode($message) . "#spare-parts-list");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: inventory.php");
    exit();
}

$conn = null;

try {
    $token = $_POST["csrf_token"] ?? "";
    if (empty($_SESSION["csrf_token"]) || !hash_equals($_SESSION["csrf_token"], $token)) {
        throw new Exception("Security validation failed. Please refresh the inventory page and try again.");
    }

    $partId = filter_var($_POST["part_id"] ?? null, FILTER_VALIDATE_INT);
    if (!$partId || $partId < 1) throw new Exception("Invalid spare part selected.");

    $conn = new mysqli("localhost", "root", "", "autopulse");
    $conn->set_charset("utf8mb4");

    $stmt = $conn->prepare("DELETE FROM spare_parts WHERE id = ?");
    $stmt->bind_param("i", $partId);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        $stmt->close();
        $conn->close();
        redirectError("The selected spare part could not be found.");
    }

    $stmt->close();
    $conn->close();
    header("Location: inventory.php?deleted=" . urlencode("Spare part deleted successfully."));
    exit();

} catch (mysqli_sql_exception $ex) {
    error_log("Delete spare part error: " . $ex->getMessage());
    if ($conn instanceof mysqli) $conn->close();

    if ((int)$ex->getCode() === 1451) {
        redirectError("This spare part is linked to existing records and cannot be deleted.");
    }
    redirectError("A database error occurred while deleting the spare part.");
} catch (Exception $ex) {
    if ($conn instanceof mysqli) $conn->close();
    redirectError($ex->getMessage());
}
?>
