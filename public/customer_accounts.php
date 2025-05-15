<?php
session_start();

// Ensure only a Manager can access this page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Manager') {
    header("Location: login.php");
    exit();
}

require 'db_connection.php'; // This file should initialize the $conn variable.

$customers = [];
$queryCustomers = "SELECT user_id, full_name, username, email, phone, created_at 
                   FROM users 
                   WHERE role = 'Customer' 
                   ORDER BY created_at DESC";
if ($result = $conn->query($queryCustomers)) {
    while ($row = $result->fetch_assoc()) {
        $customers[] = $row;
    }
    $result->free();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Customer Accounts | Credit & Saving System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet" />
    <link href="styles.css" rel="stylesheet" />
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
                    <li class="mb-4"><a href="financial_statement_report.php" class="hover:text-color3">🛠
                            Financial Statement
                            S</a>
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

        <!-- Main Content: Customer Accounts -->
        <main class="flex-grow p-6 h-screen w-[65%] overflow-auto">
            <h1 class="text-3xl font-bold mb-6 text-color3">Customer Accounts</h1>
            <p class="text-lg mb-6">Review all registered customer accounts below.</p>
            <div class="bg-white shadow-md rounded-lg overflow-auto">
                <?php if (count($customers) > 0): ?>
                <table class="w-full">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="px-4 py-2">Customer ID</th>
                            <th class="px-4 py-2">Full Name</th>
                            <th class="px-4 py-2">Username</th>
                            <th class="px-4 py-2">Email</th>
                            <th class="px-4 py-2">Phone</th>
                            <th class="px-4 py-2">Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $customer): ?>
                        <tr class="border-b hover:bg-gray-100">
                            <td class="px-4 py-2"><?php echo htmlspecialchars($customer['user_id']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($customer['full_name']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($customer['username']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($customer['email']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($customer['phone']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($customer['created_at']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="p-6 text-center">No customer accounts found.</div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>

</html>