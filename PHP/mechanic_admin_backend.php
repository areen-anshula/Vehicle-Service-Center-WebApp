<?php 
include "database.php";

$sql = "SELECT COUNT(*) as approve_count FROM appointment WHERE status = 'approve'";
$stmt = $conn->query($sql);
$result = $stmt->fetch_assoc();
$approve_count = $result['approve_count'];
$stmt->close();

$sql2 = "SELECT COUNT(*) as in_progress_count FROM appointment WHERE status = 'inporogress'";
$stmt = $conn->query($sql2);
$result = $stmt->fetch_assoc();
$in_progress_count = $result['in_progress_count'];
$stmt->close();

$sql3 = "SELECT COUNT(*) as close_count FROM appointment WHERE status = 'closed'";
$stmt = $conn->query($sql3);
$result = $stmt->fetch_assoc();
$close_count = $result['close_count'];
$stmt->close();

?>