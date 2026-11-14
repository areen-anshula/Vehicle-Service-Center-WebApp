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

    $partName = trim($_POST["part_name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $priceInput = $_POST["part_price"] ?? "";
    $quantityInput = $_POST["quantity"] ?? "";

    if ($partName === "") throw new Exception("Please enter the spare-part name.");
    if ($priceInput === "" || !is_numeric($priceInput) || (float)$priceInput < 0) throw new Exception("Please enter a valid unit price.");
    if ($quantityInput === "" || filter_var($quantityInput, FILTER_VALIDATE_INT) === false || (int)$quantityInput < 0) throw new Exception("Please enter a valid non-negative quantity.");

    $price = (float)$priceInput;
    $quantity = (int)$quantityInput;

    $conn = new mysqli("localhost", "root", "", "autopulse");
    $conn->set_charset("utf8mb4");

    $stmt = $conn->prepare("UPDATE spare_parts SET part_name = ?, description = ?, part_price = ?, quantity = ? WHERE id = ?");
    $stmt->bind_param("ssdii", $partName, $description, $price, $quantity, $partId);
    $stmt->execute();

    if ($stmt->affected_rows < 0) throw new Exception("Unable to update this spare part.");

    $stmt->close();
    $conn->close();
    header("Location: inventory.php?updated=" . urlencode("Spare part details updated successfully."));
    exit();

} catch (mysqli_sql_exception $ex) {
    error_log("Edit spare part error: " . $ex->getMessage());
    if ($conn instanceof mysqli) $conn->close();
    redirectError("A database error occurred while updating the spare part.");
} catch (Exception $ex) {
    if ($conn instanceof mysqli) $conn->close();
    redirectError($ex->getMessage());
}
?>
