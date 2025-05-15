<?php
session_start();

// Ensure only authenticated customers can access this page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Customer') {
    header("Location: login.php");
    exit();
}

require 'db_connection.php'; // This file initializes your $conn variable (mysqli connection)
$customer_id = $_SESSION['user_id'];

// Ensure the form is submitted via POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: transfer.php");
    exit();
}

// Retrieve and sanitize form inputs.
$account_id         = isset($_POST['account_id']) ? intval($_POST['account_id']) : 0;
$recipient_account  = isset($_POST['recipient_account']) ? intval($_POST['recipient_account']) : 0;
$amount_raw         = isset($_POST['amount']) ? trim($_POST['amount']) : '';
$remarks            = isset($_POST['remarks']) ? trim($_POST['remarks']) : '';

if ($account_id <= 0 || $recipient_account <= 0 || !is_numeric($amount_raw) || floatval($amount_raw) <= 0) {
    echo "<script>alert('Invalid input. Please check your entries.'); window.location.href='transfer.php';</script>";
    exit();
}
$amount = floatval($amount_raw);

// STEP 1: Verify sender’s account: Ensure it belongs to the logged–in customer, is active, and is a Savings account.
$stmt = $conn->prepare("SELECT balance FROM Accounts WHERE account_id = ? AND customer_id = ? AND status = 'Active' AND account_type = 'Savings'");
$stmt->bind_param("ii", $account_id, $customer_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $sender_balance = $row['balance'];
    if ($sender_balance < $amount) {
        echo "<script>alert('Insufficient funds to complete this transfer.'); window.location.href='transfer.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('Sender account not found.'); window.location.href='transfer.php';</script>";
    exit();
}
$stmt->close();

// STEP 2: Identify recipient’s account using account_id directly.
$stmt2 = $conn->prepare("SELECT account_id FROM Accounts WHERE account_id = ? AND account_type = 'Savings' AND status = 'Active'");
$stmt2->bind_param("i", $recipient_account);
$stmt2->execute();
$result2 = $stmt2->get_result();
if ($rrow = $result2->fetch_assoc()) {
    $recipient_account_id = $rrow['account_id'];
} else {
    echo "<script>alert('Recipient saving account not found.'); window.location.href='transfer.php';</script>";
    exit();
}
$stmt2->close();

// Optional: Prevent transfer to same account
if ($account_id === $recipient_account_id) {
    echo "<script>alert('You cannot transfer money to the same account.'); window.location.href='transfer.php';</script>";
    exit();
}

// STEP 3: Begin Transaction
$conn->begin_transaction();

try {
    // Debit the sender's account.
    $stmt3 = $conn->prepare("UPDATE Accounts SET balance = balance - ? WHERE account_id = ?");
    $stmt3->bind_param("di", $amount, $account_id);
    $stmt3->execute();
    if ($stmt3->affected_rows < 1) {
        throw new Exception("Failed to debit sender account.");
    }
    $stmt3->close();

    // Credit the recipient's account.
    $stmt4 = $conn->prepare("UPDATE Accounts SET balance = balance + ? WHERE account_id = ?");
    $stmt4->bind_param("di", $amount, $recipient_account_id);
    $stmt4->execute();
    if ($stmt4->affected_rows < 1) {
        throw new Exception("Failed to credit recipient account.");
    }
    $stmt4->close();

    // Insert transaction record for the sender's account.
    $default_employee_id = 1;
    $stmt5 = $conn->prepare("INSERT INTO Transactions (account_id, transaction_type, amount, processed_by) VALUES (?, 'Transfer', ?, ?)");
    $stmt5->bind_param("idi", $account_id, $amount, $default_employee_id);
    $stmt5->execute();
    if ($stmt5->affected_rows < 1) {
        throw new Exception("Failed to record sender transaction.");
    }
    $stmt5->close();

    // Insert transaction record for the recipient's account.
    $stmt6 = $conn->prepare("INSERT INTO Transactions (account_id, transaction_type, amount, processed_by) VALUES (?, 'Transfer', ?, ?)");
    $stmt6->bind_param("idi", $recipient_account_id, $amount, $default_employee_id);
    $stmt6->execute();
    if ($stmt6->affected_rows < 1) {
        throw new Exception("Failed to record recipient transaction.");
    }
    $stmt6->close();

    // Optional: Save remarks in a separate table if needed.

    // Commit the transaction when all operations succeed.
    $conn->commit();
    echo "<script>alert('Transfer successful: " . number_format($amount, 2) . " has been transferred.'); window.location.href='transfer.php';</script>";
} catch (Exception $ex) {
    // Roll back the transaction if any operation fails.
    $conn->rollback();
    echo "<script>alert('Transfer failed: " . $ex->getMessage() . "'); window.location.href='transfer.php';</script>";
    exit();
}
?>