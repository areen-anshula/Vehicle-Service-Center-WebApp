<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "AutoPulse";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Database connected successfully!";

?>