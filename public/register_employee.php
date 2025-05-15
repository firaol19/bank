<?php
session_start();

// Ensure only a manager can access this page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Manager') {
    header("Location: login.php");
    exit();
}

require 'db_connection.php'; // This file should initialize the $conn variable, if needed.
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Employee | Credit & Saving System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>

<body class="bg-2 text-color2">
    <div class="flex">
        <!-- Sidebar Navigation -->
        <aside class="w-[35%] bg-primary text-white h-screen p-6 sticky">
            <h2 class="text-2xl font-bold mb-6">Manager Dashboard</h2>
            <nav>
                <ul>
                    <li class="mb-4">
                        <a href="manager_dashboard.php" class="hover:text-color3">🏠 Overview</a>
                    </li>
                    <li class="mb-4">
                        <a href="register_employee.php" class="hover:text-color3">👤 Register Employee</a>
                    </li>
                    <li class="mb-4">
                        <a href="manage_loans.php" class="hover:text-color3">🏦 Loan Approvals</a>
                    </li>
                    <li class="mb-4">
                        <a href="manage_transactions.php" class="hover:text-color3">💰 Transactions</a>
                    </li>
                    <li class="mb-4">
                        <a href="customer_accounts.php" class="hover:text-color3">👥 Customer Accounts</a>
                    </li>
                    <li class="mb-4">
                        <a href="view_reports.php" class="hover:text-color3">📊 Reports & Analytics</a>
                    </li>
                    <li class="mb-4">
                        <a href="system_settings.php" class="hover:text-color3">⚙️ System Settings</a>
                    </li>
                    <li class="mb-4">
                        <a href="logout.php" class="hover:text-color3">🚪 Logout</a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content (Employee Registration Form) -->
        <main class="flex-grow p-6 h-screen w-[65%] overflow-auto ml-80">
            <h1 class="text-3xl font-bold mb-6 text-color3">
                Register New Employee
            </h1>
            <p class="text-lg mb-6">
                Fill in the details below to register a new employee.
            </p>
            <form action="process_register_employee.php" method="POST"
                class="bg-white max-w-md shadow-lg shadow-[#a0c878] rounded-lg p-6 ">
                <div class="mb-4">
                    <label for="full_name" class="block text-lg text-color3">Full Name</label>
                    <input type="text" name="full_name" id="full_name" required
                        class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                </div>
                <div class="mb-4">
                    <label for="username" class="block text-lg text-color3">Username</label>
                    <input type="text" name="username" id="username" required
                        class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-lg text-color3">Email Address</label>
                    <input type="email" name="email" id="email" required
                        class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                </div>
                <div class="mb-4">
                    <label for="phone" class="block text-lg text-color3">Phone Number</label>
                    <input type="text" name="phone" id="phone" required
                        class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-lg text-color3">Password</label>
                    <input type="password" name="password" id="password" required
                        class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                </div>
                <!-- Automatically set role to "Employee" -->
                <input type="hidden" name="role" value="Employee">
                <button type="submit"
                    class="mt-4 bg-primary text-white px-6 py-3 rounded-lg hover:bg-color3 transition-all w-full">
                    Register Employee
                </button>
            </form>
        </main>
    </div>
</body>

</html>