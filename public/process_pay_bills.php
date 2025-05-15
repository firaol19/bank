<?php
session_start();

// Ensure only authenticated customers can access this page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Customer') {
    header("Location: login.php");
    exit();
}

require 'db_connection.php'; // This file initializes your $conn variable (MySQLi connection)
$customer_id = $_SESSION['user_id'];

// Ensure the form is submitted via POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: pay_bills.php");
    exit();
}

// Retrieve and sanitize form inputs.
$account_id      = isset($_POST['account_id']) ? intval($_POST['account_id']) : 0;
$bill_reference  = isset($_POST['bill_reference']) ? trim($_POST['bill_reference']) : "";
$amount_raw      = isset($_POST['amount']) ? trim($_POST['amount']) : "";
$remarks         = isset($_POST['remarks']) ? trim($_POST['remarks']) : "";

if ($account_id <= 0 || !is_numeric($amount_raw) || floatval($amount_raw) <= 0) {
    echo "<script>alert('Invalid input. Please check your entries.'); window.location.href='pay_bills.php';</script>";
    exit();
}

$amount = floatval($amount_raw);

// STEP 1: Verify that the selected account belongs to the logged-in customer,
// is active, is a Savings account, and has sufficient balance.
$stmt = $conn->prepare("SELECT balance FROM Accounts 
                        WHERE account_id = ? 
                          AND customer_id = ? 
                          AND account_type = 'Savings'
                          AND status = 'Active'");
$stmt->bind_param("ii", $account_id, $customer_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $current_balance = $row['balance'];
    if ($current_balance < $amount) {
        echo "<script>alert('Insufficient funds to complete the bill payment.'); window.location.href='pay_bills.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('Account not found.'); window.location.href='pay_bills.php';</script>";
    exit();
}
$stmt->close();

// STEP 2: Begin Transaction
$conn->begin_transaction();

try {
    // Debit the bill amount from the customer's account.
    $stmt_update = $conn->prepare("UPDATE Accounts SET balance = balance - ? 
                                   WHERE account_id = ?");
    $stmt_update->bind_param("di", $amount, $account_id);
    $stmt_update->execute();
    if ($stmt_update->affected_rows < 1) {
        throw new Exception("Failed to update account balance.");
    }
    $stmt_update->close();
    
    // Insert a transaction record for the bill payment.
    // Since 'process_by' is required and must reference an employee,
    // we use a default employee id (for example, 1) to denote system processing.
    $default_employee_id = 1;
    $stmt_insert = $conn->prepare("INSERT INTO Transactions 
                                   (account_id, transaction_type, amount, processed_by) 
                                   VALUES (?, 'Withdrawal', ?, ?)");
    $stmt_insert->bind_param("idi", $account_id, $amount, $default_employee_id);
    $stmt_insert->execute();
    if ($stmt_insert->affected_rows < 1) {
        throw new Exception("Failed to record the transaction.");
    }
    $stmt_insert->close();

    // Optionally, you can store bill_reference and remarks in a logging table if needed.
    // For now, we assume these are not kept in our primary tables.

    // Commit the transaction as all operations succeeded.
    $conn->commit();
    echo "<script>alert('Bill payment successful: ₹" . number_format($amount, 2) . " has been deducted.'); window.location.href='pay_bills.php';</script>";
} catch (Exception $e) {
    // Rollback on any error.
    $conn->rollback();
    echo "<script>alert('Bill payment failed: " . $e->getMessage() . "'); window.location.href='pay_bills.php';</script>";
    exit();
}

$conn->close();
?>