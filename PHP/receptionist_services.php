<?php
include "database.php";

$sql = "SELECT COUNT(*) AS total_services FROM services";
$stmt = $conn->query($sql);
$result = $stmt->fetch_assoc();
$total_services = $result['total_services'];
$stmt->close();

$sql = "SELECT COUNT(*) AS active_services FROM services where is_active = 1";
$stmt = $conn->query($sql);
$result = $stmt->fetch_assoc();
$active_services = $result['active_services'];
$stmt->close();

$sql = "SELECT COUNT(*) AS inactive_services FROM services where is_active = 0";
$stmt = $conn->query($sql);
$result = $stmt->fetch_assoc();
$inactive_services = $result['inactive_services'];
$stmt->close();

$sql = "SELECT COUNT(DISTINCT service_type) AS service_types_count FROM services";
$stmt = $conn->query($sql);
$result = $stmt->fetch_assoc();
$service_types_count = $result['service_types_count'];
$stmt->close();

?>