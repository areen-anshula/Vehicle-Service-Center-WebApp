<?php
$required_role = "receptionist";
require_once __DIR__ . "/auth_guard.php";
include "database.php";

$sql = "SELECT COUNT(*) AS total_services FROM services";
$result_obj = $conn->query($sql);
$result = $result_obj->fetch_assoc();
$total_services = $result['total_services'];
$result_obj->free();

$sql = "SELECT COUNT(*) AS active_services FROM services WHERE is_active = 1";
$result_obj = $conn->query($sql);
$result = $result_obj->fetch_assoc();
$active_services = $result['active_services'];
$result_obj->free();

$sql = "SELECT COUNT(*) AS inactive_services FROM services WHERE is_active = 0";
$result_obj = $conn->query($sql);
$result = $result_obj->fetch_assoc();
$inactive_services = $result['inactive_services'];
$result_obj->free();

$sql = "SELECT COUNT(DISTINCT service_type) AS service_types_count FROM services";
$result_obj = $conn->query($sql);
$result = $result_obj->fetch_assoc();
$service_types_count = $result['service_types_count'];
$result_obj->free();

// Fetch all services for card rendering
$sql = "SELECT id, service_name, service_type, description, price, is_active, estimate_duration FROM services ORDER BY is_active DESC, id ASC";
$all_services = $conn->query($sql);

?>