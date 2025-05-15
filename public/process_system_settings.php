<?php
session_start();

// Ensure only a Manager can access this page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Manager') {
    header("Location: login.php");
    exit();
}

// Make sure that this script is being accessed using a POST request.
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: system_settings.php");
    exit();
}

require 'db_connection.php'; // This file should initialize the $conn variable.

// Retrieve and trim POST data.
$system_name      = isset($_POST["system_name"]) ? trim($_POST["system_name"]) : "";
$contact_email    = isset($_POST["contact_email"]) ? trim($_POST["contact_email"]) : "";
$default_currency = isset($_POST["default_currency"]) ? trim($_POST["default_currency"]) : "";
$maintenance_mode = isset($_POST["maintenance_mode"]) ? trim($_POST["maintenance_mode"]) : "0";

// Basic validation for contact email.
if (!filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Invalid contact email provided.'); window.location.href='system_settings.php';</script>";
    exit();
}

// Define an associative array of settings to update.
$settings = [
    "system_name"      => $system_name,
    "contact_email"    => $contact_email,
    "default_currency" => $default_currency,
    "maintenance_mode" => $maintenance_mode
];

// Loop through each setting and update (or insert) it into the settings table.
foreach ($settings as $key => $value) {
    $query = "INSERT INTO settings (setting_key, setting_value)
              VALUES (?, ?)
              ON DUPLICATE KEY UPDATE setting_value = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo "<script>alert('Database error: " . $conn->error . "'); window.location.href='system_settings.php';</script>";
        exit();
    }
    $stmt->bind_param("sss", $key, $value, $value);
    if (!$stmt->execute()) {
        echo "<script>alert('Failed to update setting: $key'); window.location.href='system_settings.php';</script>";
        $stmt->close();
        exit();
    }
    $stmt->close();
}

// All settings updated successfully.
echo "<script>alert('Settings updated successfully!'); window.location.href='system_settings.php';</script>";
?>