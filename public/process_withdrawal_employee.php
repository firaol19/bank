<?php
session_start();

// Ensure only authenticated employees can access this page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Employee') {
    header("Location: login.php");
    exit();
}

require 'db_connection.php'; // Initializes the $conn variable (MySQLi connection)

// Ensure the form is submitted via POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: withdrawal.php");
    exit();
}

// Retrieve and sanitize the input fields.
$account_id = trim($_POST['account_id'] ?? '');
$amount_raw = trim($_POST['amount'] ?? '');
$remarks    = trim($_POST['remarks'] ?? '');

// Validate inputs: account ID must not be empty and the withdrawal amount must be a positive number.
if (empty($account_id) || !is_numeric($amount_raw) || floatval($amount_raw) <= 0) {
    echo "<script>alert('Invalid input. Please check your entries.'); window.location.href='withdrawal.php';</script>";
    exit();
}

$amount = floatval($amount_raw);

// Verify that the specified account exists and is active.
$stmt = $conn->prepare("SELECT balance FROM Accounts WHERE account_id = ? AND status = 'Active'");
if (!$stmt) {
    echo "<script>alert('Database error: " . htmlspecialchars($conn->error) . "'); window.location.href='withdrawal.php';</script>";
    exit();
}
$stmt->bind_param("i", $account_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $current_balance = $row['balance'];
    // Ensure that there are sufficient funds to process the withdrawal.
    if ($current_balance < $amount) {
        echo "<script>alert('Insufficient funds in account #$account_id to process this withdrawal.'); window.location.href='withdrawal.php';</script>";
        $stmt->close();
        exit();
    }
} else {
    echo "<script>alert('Account not found or inactive.'); window.location.href='withdrawal.php';</script>";
    $stmt->close();
    exit();
}
$stmt->close();

// Get the employee's ID from the session.
$employee_id = $_SESSION['user_id'];

// Begin a database transaction.
$conn->begin_transaction();

try {
    // Deduct the withdrawal amount from the customer’s account.
    $stmt_update = $conn->prepare("UPDATE Accounts SET balance = balance - ? WHERE account_id = ?");
    if (!$stmt_update) {
        throw new Exception("Database error (update): " . $conn->error);
    }
    $stmt_update->bind_param("di", $amount, $account_id);
    $stmt_update->execute();
    if ($stmt_update->affected_rows < 1) {
        throw new Exception("Failed to update account balance.");
    }
    $stmt_update->close();

    // Insert a transaction record for this withdrawal.
    $stmt_insert = $conn->prepare("INSERT INTO Transactions (account_id, transaction_type, amount, processed_by) VALUES (?, 'Withdrawal', ?, ?)");
    if (!$stmt_insert) {
        throw new Exception("Database error (insert): " . $conn->error);
    }
    $stmt_insert->bind_param("idi", $account_id, $amount, $employee_id);
    $stmt_insert->execute();
    if ($stmt_insert->affected_rows < 1) {
        throw new Exception("Failed to record the withdrawal transaction.");
    }
    $stmt_insert->close();

    // Commit the transaction if all operations succeed.
    $conn->commit();

    // Success: display a confirmation message including the account id and the withdrawal amount in Birr (ብር).
    echo "<script>alert('Withdrawal successful: ብር " . number_format($amount, 2) . " has been withdrawn from account #$account_id.'); window.location.href='employee_dashboard.php';</script>";
} catch (Exception $ex) {
    // Rollback the transaction on any error.
    $conn->rollback();
    echo "<script>alert('Withdrawal failed: " . $ex->getMessage() . "'); window.location.href='withdrawal.php';</script>";
    exit();
}

$conn->close();
?>