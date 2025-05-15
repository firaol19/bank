<?php
session_start();

// Ensure only authenticated customers can access this page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Customer') {
    header("Location: login.php");
    exit();
}

require 'db_connection.php'; // This file initializes $conn (MySQLi connection)
$customer_id = $_SESSION['user_id'];

// Ensure the form is submitted via POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: support.php");
    exit();
}

// Retrieve and sanitize the support message.
$message = trim($_POST['message'] ?? '');

if (empty($message)) {
    echo "<script>alert('Please enter your support message.'); window.location.href='support.php';</script>";
    exit();
}

// Insert the support query into the Complaints table.
// The Complaints table schema (as created) is assumed to be:
// complaint_id INT AUTO_INCREMENT PRIMARY KEY,
// customer_id INT NOT NULL,
// message TEXT NOT NULL,
// submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
// status ENUM('Pending','Resolved') DEFAULT 'Pending',
// resolved_by INT NULL,
// with a foreign key linking customer_id to Customers(customer_id).
$stmt = $conn->prepare("INSERT INTO Complaints (customer_id, message) VALUES (?, ?)");
if (!$stmt) {
    echo "<script>alert('Database error: " . htmlspecialchars($conn->error) . "'); window.location.href='support.php';</script>";
    exit();
}
$stmt->bind_param("is", $customer_id, $message);

if ($stmt->execute()) {
    // On success, display a success alert and redirect back to support.php.
    echo "<script>alert('Your support message has been submitted successfully.'); window.location.href='support.php';</script>";
} else {
    // On failure, display an error message.
    echo "<script>alert('Failed to submit your support message. Please try again later.'); window.location.href='support.php';</script>";
}
$stmt->close();
$conn->close();
?>