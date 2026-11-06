<?php

include "database.php";

$sql = "SELECT COUNT(*) AS clients_count FROM users WHERE role = 'client'";

$sql2 = "SELECT COUNT(*) AS mechanics_count FROM users WHERE role = 'mechanic'";

$sql3 = "SELECT COUNT(*) AS active_jobs_cards FROM job_cards WHERE status != 'close'";

$sql4 = "SELECT COUNT(*) AS spare_parts_count FROM spare_parts";

$stmt = $conn->query($sql);
$result = $stmt->fetch_assoc();
$clients_count = $result['clients_count'];
$stmt->close();

$stmt = $conn->query($sql2);
$result2 = $stmt->fetch_assoc();
$mechanics_count = $result2['mechanics_count'];
$stmt->close();

$stmt = $conn->query($sql3);
$result3 = $stmt->fetch_assoc();
$active_jobs_cards = $result3['active_jobs_cards'];
$stmt->close();

$stmt = $conn->query($sql4);
$result4 = $stmt->fetch_assoc();
$spare_parts_count = $result4['spare_parts_count'];
$stmt->close();

?>