<?php
session_start();

// Ensure only authorized users (e.g., Employee or Manager) can approve loans.
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'Employee' && $_SESSION['role'] !== 'Manager')) {
    header("Location: login.php");
    exit();
}

require_once 'db_connection.php'; // Initializes $conn

// Ensure loan_id is provided via GET
if (!isset($_GET['loan_id']) || empty($_GET['loan_id'])) {
    echo "<script>alert('Missing loan ID parameter.'); window.location.href='manage_loans.php';</script>";
    exit();
}

$loan_id = intval($_GET['loan_id']);

// 1. Fetch the loan record from the loans table using the provided loan_id.
$stmt = $conn->prepare("SELECT customer_id, loan_amount, status FROM loans WHERE loan_id = ?");
if (!$stmt) {
    echo "<script>alert('Database error: " . htmlspecialchars($conn->error) . "'); window.location.href='manage_loans.php';</script>";
    exit();
}
$stmt->bind_param("i", $loan_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $customer_id     = $row['customer_id'];
    $requested_amount = floatval($row['loan_amount']);
    $currentLoanStatus = $row['status'];
} else {
    $stmt->close();
    echo "<script>alert('Loan request not found.'); window.location.href='manage_loans.php';</script>";
    exit();
}
$stmt->close();

// Optional: Prevent re-approval if already approved.
if ($currentLoanStatus === 'Approved') {
    echo "<script>alert('This loan request has already been approved.'); window.location.href='manage_loans.php';</script>";
    exit();
}

// 2. Fetch the customer's loan account from the accounts table.
$stmt = $conn->prepare("SELECT account_id, balance FROM accounts WHERE customer_id = ? AND account_type = 'Loan'");
if (!$stmt) {
    echo "<script>alert('Database error: " . htmlspecialchars($conn->error) . "'); window.location.href='manage_loans.php';</script>";
    exit();
}
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($account = $result->fetch_assoc()) {
    $account_id      = $account['account_id'];
    $current_balance = floatval($account['balance']);
} else {
    $stmt->close();
    echo "<script>alert('Loan account not found for this customer.'); window.location.href='manage_loans.php';</script>";
    exit();
}
$stmt->close();

// 3. Calculate the new loan account balance by adding the requested loan amount.
$new_balance = $current_balance + $requested_amount;

// Apply a fixed 10% interest rate.
// Total due is the principal plus interest.
$total_due = $requested_amount * 1.10;

// 4. Begin a transaction so that updating the account balance and the loan status is atomic.
$conn->begin_transaction();

try {
    // 4a. Update the accounts table with the new balance.
    $stmt = $conn->prepare("UPDATE accounts SET balance = ? WHERE account_id = ?");
    if (!$stmt) {
        throw new Exception("Database error updating account: " . $conn->error);
    }
    $stmt->bind_param("di", $new_balance, $account_id);
    if (!$stmt->execute()) {
        throw new Exception("Failed to update loan account balance.");
    }
    $stmt->close();

    // 4b. Update the loans table:
    // Set status to 'Approved' and assign the computed total due (principal + 10% interest) to unpaid_amount.
    $stmt = $conn->prepare("UPDATE loans SET status = 'Approved', unpaid_amount = ? WHERE loan_id = ?");
    if (!$stmt) {
        throw new Exception("Database error updating loan status: " . $conn->error);
    }
    $stmt->bind_param("di", $total_due, $loan_id);
    if (!$stmt->execute()) {
        throw new Exception("Failed to update loan status and unpaid amount.");
    }
    $stmt->close();

    // Commit the transaction if everything is successful.
    $conn->commit();
    
    echo "<script>
            alert('Loan approved! ብር " . number_format($requested_amount, 2) . " has been added to the loan account (Account ID: $account_id). The total due (with 10% interest) is ብር " . number_format($total_due, 2) . ".');
            window.location.href = 'manage_loans.php';
          </script>";
} catch (Exception $e) {
    // Roll back the transaction if any error occurs.
    $conn->rollback();
    echo "<script>
            alert('Loan approval failed: " . $e->getMessage() . "');
            window.location.href = 'manage_loans.php';
          </script>";
    exit();
}

$conn->close();
?>