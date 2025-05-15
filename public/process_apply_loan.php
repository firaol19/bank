<?php
session_start();

// Ensure only authenticated customers can access this page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Customer') {
    header("Location: login.php");
    exit();
}

require 'db_connection.php'; // This file initializes your MySQLi connection in $conn.
$customer_id = $_SESSION['user_id'];

// Ensure the form was submitted via POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: apply_loan.php");
    exit();
}

// Retrieve and sanitize form inputs.
$loan_amount = isset($_POST['loan_amount']) ? trim($_POST['loan_amount']) : '';
$duration    = isset($_POST['duration']) ? trim($_POST['duration']) : '';
// Optional remarks—not stored in the current schema but can be used for logging if needed.
$remarks     = isset($_POST['remarks']) ? trim($_POST['remarks']) : '';

// Validate inputs.
if (empty($loan_amount) || !is_numeric($loan_amount) || floatval($loan_amount) <= 0) {
    echo "<script>alert('Please enter a valid loan amount.'); window.location.href='apply_loan.php';</script>";
    exit();
}
if (empty($duration) || !is_numeric($duration) || intval($duration) <= 0) {
    echo "<script>alert('Please enter a valid duration in months.'); window.location.href='apply_loan.php';</script>";
    exit();
}

$loan_amount = floatval($loan_amount);
$duration    = intval($duration);

// For this example, we'll use a default interest rate of 10.00%.
$interest_rate = 10.00;

// Insert a new loan application into the Loans table.
// The Loans table includes:
//   loan_id, customer_id, loan_amount, interest_rate, duration, status, approved_by, request_date, approval_date
// We leave approved_by NULL and use the default CURRENT_TIMESTAMP for request_date.
// The status will remain "Pending" as defined by the default.
$stmt = $conn->prepare("INSERT INTO Loans (customer_id, loan_amount, interest_rate, duration, status, request_date) VALUES (?, ?, ?, ?, 'Pending', DEFAULT)");
if ($stmt) {
    $stmt->bind_param("idid", $customer_id, $loan_amount, $interest_rate, $duration);
    if ($stmt->execute()) {
        echo "<script>alert('Loan application submitted successfully.'); window.location.href='loans.php';</script>";
    } else {
        echo "<script>alert('Failed to submit loan application. Please try again later.'); window.location.href='apply_loan.php';</script>";
    }
    $stmt->close();
} else {
    echo "<script>alert('Database error: " . $conn->error . "'); window.location.href='apply_loan.php';</script>";
}

$conn->close();
?>