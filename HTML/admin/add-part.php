<?php

include '../../PHP/database.php';

<<<<<<< HEAD
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $part_name = $_POST['part_name'];
    $part_category = $_POST['category'];
    $description = $_POST['description'];
    $part_price = $_POST['unit_price'];
    $quantity = $_POST['quantity'];

    $sql = "INSERT INTO spare_parts
            (part_name, part_category, description, part_price, quantity)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "sssdi",
        $part_name,
        $part_category,
        $description,
        $part_price,
        $quantity
    );


    if ($stmt->execute()) {

        echo "<script>
                alert('Spare Part Added Successfully!');
                window.location.href = 'inventory.php';
              </script>";

    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();

?>
=======

?>
>>>>>>> c2a1b42aa4b6b8953da12d920024d2b9f8a28072
