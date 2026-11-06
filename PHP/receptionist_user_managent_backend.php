
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli("localhost", "root", "", "autopulse");
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    die("Database connection failed: " . htmlspecialchars($e->getMessage()));
}

// CSRF protection
if (empty($_SESSION['user_management_token'])) {
    $_SESSION['user_management_token'] = bin2hex(random_bytes(32));
}

$csrf_token = $_SESSION['user_management_token'];

$message = "";
$message_type = "";

// Process customer updates and deletion
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($csrf_token, $_POST['csrf_token'])
    ) {
        die("Invalid request. Please refresh the page and try again.");
    }

    $action = $_POST['action'] ?? '';

    try {

        // UPDATE CUSTOMER
        if ($action === "update") {

            $customer_id = filter_input(
                INPUT_POST,
                'customer_id',
                FILTER_VALIDATE_INT
            );

            $full_name = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');

            if (!$customer_id || $full_name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $phone === '') {
                throw new Exception("Please enter valid customer details.");
            }

            // Ensure the selected account is a customer
            $check = $conn->prepare("
                SELECT id FROM users
                WHERE id = ?
                AND LOWER(role) IN ('client', 'customer')
            ");

            $check->bind_param("i", $customer_id);
            $check->execute();

            if ($check->get_result()->num_rows === 0) {
                throw new Exception("Customer account not found.");
            }

            // Check duplicate email
            $email_check = $conn->prepare("
                SELECT id FROM users
                WHERE email = ? AND id != ?
            ");

            $email_check->bind_param("si", $email, $customer_id);
            $email_check->execute();

            if ($email_check->get_result()->num_rows > 0) {
                throw new Exception("This email address is already in use.");
            }

            $stmt = $conn->prepare("
                UPDATE users
                SET full_name = ?, email = ?, phone = ?
                WHERE id = ?
                AND LOWER(role) IN ('client', 'customer')
            ");

            $stmt->bind_param(
                "sssi",
                $full_name,
                $email,
                $phone,
                $customer_id
            );

            $stmt->execute();

            $_SESSION['user_management_message'] = "Customer details updated successfully.";
            $_SESSION['user_management_message_type'] = "success";
        }

        // DELETE CUSTOMER
        elseif ($action === "delete") {

            $customer_id = filter_input(
                INPUT_POST,
                'customer_id',
                FILTER_VALIDATE_INT
            );

            if (!$customer_id) {
                throw new Exception("Invalid customer ID.");
            }

            $stmt = $conn->prepare("
                DELETE FROM users
                WHERE id = ?
                AND LOWER(role) IN ('client', 'customer')
            ");

            $stmt->bind_param("i", $customer_id);
            $stmt->execute();

            if ($stmt->affected_rows === 0) {
                throw new Exception("Customer not found or already deleted.");
            }

            $_SESSION['user_management_message'] = "Customer deleted successfully.";
            $_SESSION['user_management_message_type'] = "success";
        }

        header("Location: ../HTML/receptionist/user_management.php");
        exit();

    } catch (Exception $e) {

        $_SESSION['user_management_message'] = $e->getMessage();
        $_SESSION['user_management_message_type'] = "error";

        header("Location: ../HTML/receptionist/user_management.php");
        exit();
    }
}

// Display notification messages
if (isset($_SESSION['user_management_message'])) {
    $message = $_SESSION['user_management_message'];
    $message_type = $_SESSION['user_management_message_type'] ?? 'success';

    unset(
        $_SESSION['user_management_message'],
        $_SESSION['user_management_message_type']
    );
}

// Fetch all registered customers
$clients = [];

$stmt = $conn->prepare("
    SELECT id, full_name, email, phone, role
    FROM users
    WHERE LOWER(role) IN ('client', 'customer')
    ORDER BY id DESC
");

$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $clients[] = $row;
}

$clients_count = count($clients);
?>
