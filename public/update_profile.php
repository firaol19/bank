<?php
session_start();

// Ensure only an Employee can access this page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Employee') {
    header("Location: login.php");
    exit();
}

// Ensure the request is a POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: employee_profile.php");
    exit();
}

require 'db_connection.php'; // This file should initialize the $conn variable.
$user_id = $_SESSION['user_id'];

// Retrieve and trim form inputs.
$full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : "";
$email     = isset($_POST['email'])     ? trim($_POST['email']) : "";
$phone     = isset($_POST['phone'])     ? trim($_POST['phone']) : "";

// Validation: full_name and email are required.
if (empty($full_name) || empty($email)) {
    echo "<script>alert('Full Name and Email are required.'); window.location.href='employee_profile.php';</script>";
    exit();
}

// Validate the email address.
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Please enter a valid Email address.'); window.location.href='employee_profile.php';</script>";
    exit();
}

// Prepare the UPDATE statement.
$query = "UPDATE users SET full_name = ?, email = ?, phone = ? WHERE user_id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    echo "<script>alert('Database error: " . $conn->error . "'); window.location.href='employee_profile.php';</script>";
    exit();
}

$stmt->bind_param("sssi", $full_name, $email, $phone, $user_id);

if ($stmt->execute()) {
    // Optionally, update session variables here (if you display the full name in the header, etc.).
    $_SESSION['full_name'] = $full_name;
    echo "<script>alert('Profile updated successfully.'); window.location.href='employee_profile.php';</script>";
} else {
    echo "<script>alert('Failed to update profile. Please try again later.'); window.location.href='employee_profile.php';</script>";
}

$stmt->close();
$conn->close();
?>