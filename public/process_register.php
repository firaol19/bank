<?php
session_start();
require_once 'db_connection.php'; // Assumes $conn is initialized

// Ensure the request method is POST.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: register.php");
    exit();
}

// Retrieve and sanitize form data
$full_name = trim($_POST['full_name'] ?? '');
$username  = trim($_POST['username'] ?? '');
$password  = trim($_POST['password'] ?? '');
$email     = trim($_POST['email'] ?? '');
$phone     = trim($_POST['phone'] ?? '');

// Validate required fields (adjust further validation as needed)
if (empty($full_name) || empty($username) || empty($password)) {
    echo "<script>alert('Please fill in all required fields.'); window.location.href='register.php';</script>";
    exit();
}

// Do NOT hash the password per your requirement
// Insert the new user into the users table (role is hardcoded as 'Customer')
$stmt = $conn->prepare("INSERT INTO users (full_name, username, password, role, email, phone) VALUES (?, ?, ?, 'Customer', ?, ?)");
if (!$stmt) {
    echo "<script>alert('Database error: " . htmlspecialchars($conn->error) . "'); window.location.href='register.php';</script>";
    exit();
}
$stmt->bind_param("sssss", $full_name, $username, $password, $email, $phone);
if (!$stmt->execute()) {
    echo "<script>alert('Registration failed: " . htmlspecialchars($stmt->error) . "'); window.location.href='register.php';</script>";
    $stmt->close();
    exit();
}
$stmt->close();

// Retrieve the auto-incremented user_id; this will serve as the customer_id
$new_user_id = $conn->insert_id;

// Generate a random 11-digit account number that is unique in the customers table
do {
    // Generate an 11-digit random number (as a string)
    $accountNumber = strval(mt_rand(10000000000, 99999999999));
    
    // Check for uniqueness in customers table
    $checkStmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM customers WHERE account_number = ?");
    if (!$checkStmt) {
        echo "<script>alert('Database error: " . htmlspecialchars($conn->error) . "'); window.location.href='register.php';</script>";
        exit();
    }
    $checkStmt->bind_param("s", $accountNumber);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    $row = $result->fetch_assoc();
    $exists = ($row["cnt"] > 0);
    $checkStmt->close();
} while ($exists);

// Insert a new row into the customers table using the new user_id as customer_id,
// the full_name from users as the customer name, and the generated unique account number.
// Other fields are left as NULL.
$insertCustomerStmt = $conn->prepare("INSERT INTO customers (customer_id, name, account_number) VALUES (?, ?, ?)");
if (!$insertCustomerStmt) {
    echo "<script>alert('Database error: " . htmlspecialchars($conn->error) . "'); window.location.href='register.php';</script>";
    exit();
}
$insertCustomerStmt->bind_param("iss", $new_user_id, $full_name, $accountNumber);
if (!$insertCustomerStmt->execute()) {
    echo "<script>alert('Customer registration failed: " . htmlspecialchars($insertCustomerStmt->error) . "'); window.location.href='register.php';</script>";
    $insertCustomerStmt->close();
    exit();
}
$insertCustomerStmt->close();
$conn->close();

// Registration successful; alert the user and redirect to log in.
echo "<script>
        alert('User registered successfully! You can now log in.');
        window.location.href='login.php';
      </script>";
?>