<?php
session_start();

// Verify that the logged-in user is a Customer.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Customer') {
    header("Location: login.php");
    exit();
}

require 'db_connection.php'; // This file initializes the $conn variable (MySQLi connection)
$customer_id = $_SESSION['user_id'];

// Ensure the form was submitted via POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: open_account.php");
    exit();
}

// Retrieve and sanitize the submitted account type.
$account_type = trim($_POST['account_type'] ?? '');

if (empty($account_type) || !in_array($account_type, ['Savings', 'Loan'])) {
    echo "<script>alert('Invalid account type selected.'); window.location.href='open_account.php';</script>";
    exit();
}

// For both account types, customers cannot deposit money initially.
// Thus, balance is set to 0.00 in Birr.
$balance = 0.00;

// Check for duplication: ensure the customer doesn't already have an account of the selected type.
$stmt = $conn->prepare("SELECT account_id FROM Accounts WHERE customer_id = ? AND account_type = ?");
if (!$stmt) {
    echo "<script>alert('Database error: " . htmlspecialchars($conn->error) . "'); window.location.href='open_account.php';</script>";
    exit();
}
$stmt->bind_param("is", $customer_id, $account_type);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo "<script>alert('Duplicate account creation is not allowed. You already have a $account_type account.'); window.location.href='open_account.php';</script>";
    $stmt->close();
    exit();
}
$stmt->close();

// Insert the new account into the Accounts table.
// The new account will have a balance of 0.00, account type as selected, status 'Active' and use the default created_at.
$stmtInsert = $conn->prepare("INSERT INTO Accounts (customer_id, account_type, balance, status) VALUES (?, ?, ?, 'Active')");
if (!$stmtInsert) {
    echo "<script>alert('Database error: " . htmlspecialchars($conn->error) . "'); window.location.href='open_account.php';</script>";
    exit();
}
$stmtInsert->bind_param("isd", $customer_id, $account_type, $balance);

if ($stmtInsert->execute()) {
    // Get the newly created account ID.
    $new_account_id = $conn->insert_id;
    // Display success message along with the account ID (using Birr as currency, though balance is zero).
    echo "<script>alert('Account opened successfully as a $account_type account. Your Account ID is: #$new_account_id. (Balance: 0.00 ብር)'); window.location.href='account_summary.php';</script>";
} else {
    echo "<script>alert('Failed to open account. Please try again later.'); window.location.href='open_account.php';</script>";
}
$stmtInsert->close();
$conn->close();
?>