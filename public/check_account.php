<?php
session_start();
require_once 'db_connection.php';

// Check if account_id is provided
if (!isset($_GET['account_id']) || empty($_GET['account_id'])) {
    echo json_encode(["success" => false, "message" => "Account ID not provided."]);
    exit();
}

$account_id = intval($_GET['account_id']);

// 1. Fetch the record from the accounts table using the provided account_id
$stmt = $conn->prepare("SELECT customer_id FROM accounts WHERE account_id = ?");
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Database error: " . $conn->error]);
    exit();
}
$stmt->bind_param("i", $account_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $customer_id = $row['customer_id'];
    $stmt->close();
    // 2. Using customer_id, fetch the customer's name from the customers table
    $stmt2 = $conn->prepare("SELECT name FROM customers WHERE customer_id = ?");
    if (!$stmt2) {
        echo json_encode(["success" => false, "message" => "Database error: " . $conn->error]);
        exit();
    }
    $stmt2->bind_param("i", $customer_id);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    if ($row2 = $result2->fetch_assoc()) {
        $customer_name = $row2['name'];
        echo json_encode(["success" => true, "customer_name" => $customer_name]);
    } else {
        echo json_encode(["success" => false, "message" => "Customer record not found."]);
    }
    $stmt2->close();
} else {
    echo json_encode(["success" => false, "message" => "Account not found."]);
}
$conn->close();
?>