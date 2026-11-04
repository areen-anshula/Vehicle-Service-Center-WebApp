<?php

include "database.php";

$sql = "SELECT COUNT(*) AS clients_count FROM users WHERE role = 'client'";
$sql2 = "SELECT COUNT(*) AS mechanics_count FROM users WHERE role = 'mechanic'";

$stmt = $conn->query($sql);

$result = $stmt->fetch_assoc();

$clients_count = $result['clients_count'];

$stmt->close();


$stmt = $conn->query($sql2);

$result2 = $stmt->fetch_assoc();

$mechanics_count = $result2['mechanics_count'];

$stmt->close();

?>