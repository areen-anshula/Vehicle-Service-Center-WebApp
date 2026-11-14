<?php
$required_role = "receptionist";
require_once __DIR__ . "/auth_guard.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli("localhost", "root", "", "autopulse");
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    exit("Database connection failed.");
}

// 1. Total Parts Types
$total_query = $conn->query("SELECT COUNT(*) AS count FROM spare_parts");
$total_parts = $total_query->fetch_assoc()['count'];

// 2. In Stock (More than 5)
$in_stock_query = $conn->query("SELECT COUNT(*) AS count FROM spare_parts WHERE quantity > 5");
$in_stock_parts = $in_stock_query->fetch_assoc()['count'];

// 3. Low Stock (Between 1 and 5)
$low_stock_query = $conn->query("SELECT COUNT(*) AS count FROM spare_parts WHERE quantity > 0 AND quantity <= 5");
$low_stock_parts = $low_stock_query->fetch_assoc()['count'];

// 4. Out of Stock (0)
$out_of_stock_query = $conn->query("SELECT COUNT(*) AS count FROM spare_parts WHERE quantity = 0");
$out_of_stock_parts = $out_of_stock_query->fetch_assoc()['count'];

// 5. Fetch all parts for the grid
$all_parts = $conn->query("SELECT * FROM spare_parts ORDER BY part_name ASC");
?>