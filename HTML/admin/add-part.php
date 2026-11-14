<?php

include '../../PHP/database.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $part_name = $_POST['part_name'];
    $description = $_POST['description'];
    $part_price = $_POST['part_price'];
    $quantity = $_POST['quantity'];

    $sql = "INSERT INTO spare_parts
            (part_name, description, part_price, quantity)
            VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "ssdi",
        $part_name,
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
