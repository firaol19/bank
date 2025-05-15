<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Customer') {
    header("Location: login.php");
    exit();
}

require_once 'db_connection.php';
$customer_id = $_SESSION['user_id'];

// Ensure the form was submitted via POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: repay_loan.php");
    exit();
}

// Retrieve POST data.
$loan_id      = intval($_POST['loan_id'] ?? 0);
$repay_amount = floatval($_POST['repay_amount'] ?? 0);

if ($repay_amount <= 0) {
    $msg = json_encode("Enter a valid repayment amount.");
    echo "<script>alert($msg); window.location.href='repay_loan.php';</script>";
    exit();
}

// Fetch the loan record (ensure it belongs to the customer).
$stmt = $conn->prepare("SELECT unpaid_amount, status FROM loans WHERE loan_id = ? AND customer_id = ?");
$stmt->bind_param("ii", $loan_id, $customer_id);
$stmt->execute();
$result = $stmt->get_result();
if (!$loan = $result->fetch_assoc()) {
    $stmt->close();
    $msg = json_encode("Loan record not found.");
    echo "<script>alert($msg); window.location.href='repay_loan.php';</script>";
    exit();
}
$current_unpaid = floatval($loan['unpaid_amount']);
$stmt->close();

// Ensure the repayment amount does not exceed the outstanding unpaid amount.
if ($repay_amount > $current_unpaid) {
    $msg = json_encode("Repayment amount exceeds outstanding loan balance.");
    echo "<script>alert($msg); window.location.href='repay_loan.php';</script>";
    exit();
}

// Fetch the customer's Savings account.
$stmt = $conn->prepare("SELECT account_id, balance FROM accounts WHERE customer_id = ? AND account_type = 'Savings' LIMIT 1");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
if (!$savings = $result->fetch_assoc()) {
    $stmt->close();
    $msg = json_encode("Savings account not found.");
    echo "<script>alert($msg); window.location.href='repay_loan.php';</script>";
    exit();
}
$savings_account_id = $savings['account_id'];
$savings_balance    = floatval($savings['balance']);
$stmt->close();

// Ensure the Savings account has enough funds.
if ($savings_balance < $repay_amount) {
    $msg = json_encode("Insufficient funds in your Savings account.");
    echo "<script>alert($msg); window.location.href='repay_loan.php';</script>";
    exit();
}

// Begin a transaction to update both the savings account and the loan record.
$conn->begin_transaction();
try {
    // 1. Deduct the repayment amount from the Savings account.
    $stmt = $conn->prepare("UPDATE accounts SET balance = balance - ? WHERE account_id = ?");
    $stmt->bind_param("di", $repay_amount, $savings_account_id);
    $stmt->execute();
    if ($stmt->affected_rows < 1) {
        throw new Exception("Failed to deduct from Savings account.");
    }
    $stmt->close();

    // 2. Subtract the repayment amount from the loan's unpaid_amount.
    $new_unpaid = $current_unpaid - $repay_amount;
    // Optionally, if repayment covers the entire amount, mark the loan as repaid.
    $new_status = ($new_unpaid == 0) ? 'Repaid' : $loan['status'];
    $stmt = $conn->prepare("UPDATE loans SET unpaid_amount = ?, status = ? WHERE loan_id = ?");
    $stmt->bind_param("dsi", $new_unpaid, $new_status, $loan_id);
    $stmt->execute();
    if ($stmt->affected_rows < 1) {
        throw new Exception("Failed to update loan record.");
    }
    $stmt->close();

    $conn->commit();
    $msg = json_encode("Repayment successful! You repaid ብር " . number_format($repay_amount, 2) . ". Remaining loan balance: ብር " . number_format($new_unpaid, 2));
    echo "<script>
            alert($msg);
            window.location.href = 'repay_loan.php';
          </script>";
} catch (Exception $e) {
    $conn->rollback();
    $msg = json_encode("Repayment failed: " . $e->getMessage());
    echo "<script>
            alert($msg);
            window.location.href = 'repay_loan.php';
          </script>";
    exit();
}

$conn->close();
?>