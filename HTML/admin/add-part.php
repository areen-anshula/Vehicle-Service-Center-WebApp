<?php

include '../../PHP/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $part_name = $_POST['part_name'];
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];
    $unit_price = $_POST['unit_price'];
    $description = $_POST['description'];


    $sql = "INSERT INTO spare_parts
            (part_name, part_category, description, part_price, quantity)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

}

?>
