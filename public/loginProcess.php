<?php
session_start();
require 'db_connection.php'; // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validate Inputs
    if (empty($username) || empty($password)) {
        echo "<script>alert('Both username and password are required!'); window.location.href='login.php';</script>";
        exit();
    }

    // Query the database to fetch user details, including their role
    $query = "SELECT user_id, username, password, role FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Directly check the entered password (No Hashing)
        if ($password === $user['password']) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on user role
            switch ($user['role']) {
                case 'Manager':
                    header("Location: manager_dashboard.php");
                    break;
                case 'Employee':
                    header("Location: employee_dashboard.php");
                    break;
                case 'Customer':
                    header("Location: customer_dashboard.php");
                    break;
                default:
                    echo "<script>alert('Invalid role!'); window.location.href='login.php';</script>";
                    exit();
            }
        } else {
            echo "<script>alert('Incorrect password!'); window.location.href='login.php';</script>";
        }
    } else {
        echo "<script>alert('User not found! Please check your username.'); window.location.href='login.php';</script>";
    }
}
?>