<?php

include "database.php";

$sql = "SELECT COUNT(*) AS clients_count FROM users WHERE role = 'client'";
$sql4 = "SELECT COUNT(*) AS monthly_clients FROM users WHERE role = 'client' AND MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())";

$stmt = $conn->query($sql);
$result = $stmt->fetch_assoc();
$clients_count = $result['clients_count'];
$stmt->close();

$stmt = $conn->query($sql4);
$result4 = $stmt->fetch_assoc();
$monthly_clients = $result4['monthly_clients'];
$stmt->close();



?>