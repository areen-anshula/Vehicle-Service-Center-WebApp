<?php

include '../PHP/database.php';

// Check form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    // Check empty fields
    if (empty($name) || empty($email) || empty($message)) {

        echo "<script>
                alert('Please fill all fields!');
                window.location.href='homepage.html#contact';
              </script>";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        echo "<script>
                alert('Please enter a valid email!');
                window.location.href='homepage.html#contact';
              </script>";

    } else {

        // Insert data into database
        $sql = "INSERT INTO contact_messages (name, email, message)
                VALUES (?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param($stmt, "sss", $name, $email, $message);

        if (mysqli_stmt_execute($stmt)) {

            echo "<script>
                    alert('Your message sent successfully!');
                    window.location.href='homepage.html#contact';
                  </script>";

        } else {

            echo "<script>
                    alert('Message sending failed!');
                    window.location.href='homepage.html#contact';
                  </script>";
        }

        mysqli_stmt_close($stmt);
    }
}

?>