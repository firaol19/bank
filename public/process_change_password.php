<?php
session_start();

// Ensure only an Employee can access this page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Employee') {
    header("Location: login.php");
    exit();
}

// Ensure the request method is POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: change_password.php");
    exit();
}

require 'db_connection.php'; // This file initializes the $conn variable.
$user_id = $_SESSION['user_id'];

// Retrieve and trim form inputs.
$current_password = isset($_POST['current_password']) ? trim($_POST['current_password']) : "";
$new_password     = isset($_POST['new_password']) ? trim($_POST['new_password']) : "";
$confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : "";

// Basic validation.
if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    echo "<script>alert('All password fields are required.'); window.location.href='change_password.php';</script>";
    exit();
}

if ($new_password !== $confirm_password) {
    echo "<script>alert('New password and confirm password do not match.'); window.location.href='change_password.php';</script>";
    exit();
}

// Retrieve the current password (stored in plain text) for the user.
$query = "SELECT password FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    echo "<script>alert('Database error: " . $conn->error . "'); window.location.href='change_password.php';</script>";
    exit();
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('User not found.'); window.location.href='change_password.php';</script>";
    $stmt->close();
    exit();
}

$row = $result->fetch_assoc();
$stmt->close();

// Compare plain text passwords.
if ($current_password !== $row['password']) {
    echo "<script>alert('Current password is incorrect.'); window.location.href='change_password.php';</script>";
    exit();
}

// Update the password with the new password (stored as plain text).
$updateQuery = "UPDATE users SET password = ? WHERE user_id = ?";
$updateStmt = $conn->prepare($updateQuery);
if (!$updateStmt) {
    echo "<script>alert('Database error on update: " . $conn->error . "'); window.location.href='change_password.php';</script>";
    exit();
}
$updateStmt->bind_param("si", $new_password, $user_id);

if ($updateStmt->execute()) {
    echo "<script>alert('Password updated successfully!'); window.location.href='employee_profile.php';</script>";
} else {
    echo "<script>alert('Failed to update password. Please try again later.'); window.location.href='change_password.php';</script>";
}

$updateStmt->close();
$conn->close();
?>