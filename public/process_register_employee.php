<?php
session_start();

// Ensure only a Manager can access this page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Manager') {
    header("Location: login.php");
    exit();
}

require 'db_connection.php'; // This file should set up the $conn variable.

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and trim the posted data.
    $full_name = trim($_POST['full_name']);
    $username  = trim($_POST['username']);
    $email     = trim($_POST['email']);
    $phone     = trim($_POST['phone']);
    $password  = trim($_POST['password']);
    $role      = 'Employee'; // Automatically set role to "Employee"

    // Validate required inputs.
    if (empty($full_name) || empty($username) || empty($email) || empty($phone) || empty($password)) {
        echo "<script>alert('All fields are required!'); window.location.href='register_employee.php';</script>";
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email address!'); window.location.href='register_employee.php';</script>";
        exit();
    }

    // Check if the username already exists.
    $checkQuery = "SELECT user_id FROM users WHERE username = ?";
    $stmt       = $conn->prepare($checkQuery);
    if (!$stmt) {
        echo "<script>alert('Database error! Please try again later.'); window.location.href='register_employee.php';</script>";
        exit();
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo "<script>alert('Username already exists. Please choose a different username.'); window.location.href='register_employee.php';</script>";
        $stmt->close();
        exit();
    }
    $stmt->close();

    // Optionally, hash the password for better security.
    // $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare the INSERT query.
    $query = "INSERT INTO users (full_name, username, email, phone, password, role, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $stmt  = $conn->prepare($query);
    if (!$stmt) {
        echo "<script>alert('Database error! Please try again later.'); window.location.href='register_employee.php';</script>";
        exit();
    }

    // Bind parameters (using plain text password; replace $password with $hashed_password if hashing).
    $stmt->bind_param("ssssss", $full_name, $username, $email, $phone, $password, $role);

    if ($stmt->execute()) {
        echo "<script>alert('Employee registered successfully!'); window.location.href='register_employee.php';</script>";
    } else {
        echo "<script>alert('Registration failed. Please try again later.'); window.location.href='register_employee.php';</script>";
    }
    $stmt->close();
} else {
    header("Location: register_employee.php");
    exit();
}
?>