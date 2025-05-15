<?php
session_start();

// Ensure only an Employee can access this page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Employee') {
    header("Location: login.php");
    exit();
}

// Check that the request method is POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: review_loan.php");
    exit();
}

require 'db_connection.php'; // This file should initialize the $conn variable.

// Validate input: loan_id must be a number and review_remarks must not be empty.
if (!isset($_POST['loan_id']) || !is_numeric($_POST['loan_id']) || empty(trim($_POST['review_remarks']))) {
    echo "<script>alert('Invalid input data.'); window.location.href='review_loan.php';</script>";
    exit();
}

$loan_id = intval($_POST['loan_id']);
$review_remarks = trim($_POST['review_remarks']);
$employee_id = $_SESSION['user_id'];

// Optionally verify that the loan exists and is still pending.
$queryLoan = "SELECT loan_id FROM loans WHERE loan_id = ? AND status = 'Pending'";
$stmt = $conn->prepare($queryLoan);
if (!$stmt) {
    echo "<script>alert('Database error.'); window.location.href='review_loan.php';</script>";
    exit();
}
$stmt->bind_param("i", $loan_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "<script>alert('The loan either does not exist or is no longer pending.'); window.location.href='review_loan.php';</script>";
    $stmt->close();
    exit();
}
$stmt->close();

// Insert the review into the loan_reviews table.
$queryInsert = "INSERT INTO loan_reviews (loan_id, employee_id, review_remarks, review_date) VALUES (?, ?, ?, NOW())";
$stmt = $conn->prepare($queryInsert);
if (!$stmt) {
    echo "<script>alert('Database error on insert.'); window.location.href='review_loan.php';</script>";
    exit();
}
$stmt->bind_param("iis", $loan_id, $employee_id, $review_remarks);

if ($stmt->execute()) {
    // Optionally, update the loan record to reflect that an employee review has been submitted.
    echo "<script>alert('Review submitted successfully!'); window.location.href='review_loan.php';</script>";
} else {
    echo "<script>alert('Failed to submit review. Please try again later.'); window.location.href='review_loan.php';</script>";
}

$stmt->close();
?>