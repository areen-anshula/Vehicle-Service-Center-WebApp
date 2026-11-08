
<?php

require_once "db_connect.php";

// CHANGE THESE TWO VALUES TO YOUR ADMIN DETAILS
$adminEmail = "admin@autopulse.com";
$adminPassword = "Admin@12345";

try {

    // Hash the password
    $hashedPassword = password_hash(
        $adminPassword,
        PASSWORD_DEFAULT
    );

    // Update the existing Admin account
    $sql = "UPDATE users
            SET password = ?,
                role = 'admin',
                status = 'active'
            WHERE email = ?";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $hashedPassword,
        $adminEmail
    ]);

    if ($stmt->rowCount() > 0) {

        echo "Admin password updated successfully!";

    } else {

        // Check whether the account exists
        $check = $pdo->prepare(
            "SELECT user_id FROM users WHERE email = ?"
        );

        $check->execute([$adminEmail]);

        if ($check->fetch()) {

            echo "Account exists. Password may already be updated.";

        } else {

            echo "Admin account not found. Check the email address.";

        }
    }

} catch (PDOException $e) {

    error_log($e->getMessage());
    echo "Unable to update Admin account.";

}

?>
