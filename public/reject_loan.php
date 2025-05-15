<?php
session_start();

// Ensure only a Manager can access this page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Manager') {
    header("Location: login.php");
    exit();
}

require 'db_connection.php'; // This file should initialize $conn.

if (!isset($_GET['loan_id']) || !is_numeric($_GET['loan_id'])) {
    echo "<script>alert('Invalid loan ID.'); window.location.href='manage_loans.php';</script>";
    exit();
}

$loan_id = intval($_GET['loan_id']);
$manager_id = $_SESSION['user_id']; // Manager's ID must be stored in session.

$query = "UPDATE loans 
          SET status = 'Rejected', approved_by = ?, approval_date = NOW() 
          WHERE loan_id = ? AND status = 'Pending'";
$stmt = $conn->prepare($query);
if (!$stmt) {
    echo "<script>alert('Database error.'); window.location.href='manage_loans.php';</script>";
    exit();
}

$stmt->bind_param("ii", $manager_id, $loan_id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    echo "<script>alert('Loan rejected successfully.'); window.location.href='manage_loans.php';</script>";
} else {
    echo "<script>alert('Failed to reject loan. Make sure the loan is still pending.'); window.location.href='manage_loans.php';</script>";
}

$stmt->close();
?>