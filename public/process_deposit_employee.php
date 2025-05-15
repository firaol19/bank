<?php
session_start();

// Ensure only authenticated employees can access this page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Employee') {
    header("Location: login.php");
    exit();
}

require 'db_connection.php'; // This file initializes $conn (MySQLi connection)

// Ensure the form is submitted via POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: deposit.php");
    exit();
}

// Retrieve and sanitize the input fields.
$account_id = trim($_POST['account_id'] ?? '');
$amount_raw = trim($_POST['amount'] ?? '');
$remarks    = trim($_POST['remarks'] ?? '');

// Validate input values.
if (empty($account_id) || !is_numeric($amount_raw) || floatval($amount_raw) <= 0) {
    echo "<script>alert('Invalid input. Please check your entries.'); window.location.href='deposit.php';</script>";
    exit();
}

$amount = floatval($amount_raw);

// Check if the specified account exists and is active.
$stmt = $conn->prepare("SELECT balance FROM Accounts WHERE account_id = ? AND status = 'Active'");
if (!$stmt) {
    echo "<script>alert('Database error: " . htmlspecialchars($conn->error) . "'); window.location.href='deposit.php';</script>";
    exit();
}
$stmt->bind_param("i", $account_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // Account exists; we could retrieve the current balance if needed.
    $current_balance = $row['balance'];
} else {
    echo "<script>alert('Account not found or inactive.'); window.location.href='employee_deposit.php';</script>";
    exit();
}
$stmt->close();

// Get the employee's id from the session.
$employee_id = $_SESSION['user_id'];

// Begin database transaction.
$conn->begin_transaction();

try {
    // Update the account's balance by adding the deposit amount.
    $stmt_update = $conn->prepare("UPDATE Accounts SET balance = balance + ? WHERE account_id = ?");
    if (!$stmt_update) {
        throw new Exception("Database error (update): " . $conn->error);
    }
    $stmt_update->bind_param("di", $amount, $account_id);
    $stmt_update->execute();
    if ($stmt_update->affected_rows < 1) {
        throw new Exception("Failed to update the account balance.");
    }
    $stmt_update->close();

    // Insert a transaction record for this deposit.
    // Our Transactions table should have columns: transaction_id, account_id, transaction_type, amount, transaction_date (default current timestamp), and processed_by.
    $stmt_insert = $conn->prepare("INSERT INTO Transactions (account_id, transaction_type, amount, processed_by) VALUES (?, 'Deposit', ?, ?)");
    if (!$stmt_insert) {
        throw new Exception("Database error (insert): " . $conn->error);
    }
    $stmt_insert->bind_param("idi", $account_id, $amount, $employee_id);
    $stmt_insert->execute();
    if ($stmt_insert->affected_rows < 1) {
        throw new Exception("Failed to record the deposit transaction.");
    }
    $stmt_insert->close();

    // Commit the transaction if all operations succeed.
    $conn->commit();

    // Display a success message. We use Birr (ብር) as the currency indicator.
    echo "<script>alert('Deposit successful: ብር " . number_format($amount, 2) . " has been credited to account #$account_id.'); window.location.href='employee_dashboard.php';</script>";
} catch (Exception $ex) {
    // Roll back the transaction if any step fails.
    $conn->rollback();
    echo "<script>alert('Deposit failed: " . $ex->getMessage() . "'); window.location.href='employee_deposit.php';</script>";
    exit();
}

$conn->close();
?>