<?php
session_start();

// Ensure only a manager can access this page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Manager') {
    header("Location: login.php");
    exit();
}

require 'db_connection.php'; // This file should initialize the $conn variable.

// Fetch Total Employees (only counting those with role 'Employee')
$totalEmployees = 0;
$queryEmployees = "SELECT COUNT(*) AS totalEmployees FROM users WHERE role = 'Employee'";
if ($result = $conn->query($queryEmployees)) {
    if ($row = $result->fetch_assoc()) {
        $totalEmployees = $row['totalEmployees'];
    }
    $result->free();
}

// Fetch Pending Loans (assuming a 'loans' table with status 'Pending')
$totalPendingLoans = 0;
$queryPendingLoans = "SELECT COUNT(*) AS pendingLoans FROM loans WHERE status = 'Pending'";
if ($result = $conn->query($queryPendingLoans)) {
    if ($row = $result->fetch_assoc()) {
        $totalPendingLoans = $row['pendingLoans'];
    }
    $result->free();
}

// Fetch Total Transactions (assuming a 'transactions' table with an 'amount' column)
$totalTransactions = 0;
$queryTotalTransactions = "SELECT IFNULL(SUM(amount), 0) AS totalTransactions FROM transactions";
if ($result = $conn->query($queryTotalTransactions)) {
    if ($row = $result->fetch_assoc()) {
        $totalTransactions = $row['totalTransactions'];
    }
    $result->free();
}

// Fetch Recent Loan Requests (limit to 5 pending loans)
// Note: Use "l.request_date" since the loans table uses request_date instead of created_at.
$recentLoans = [];
$queryRecentLoans = "SELECT l.loan_id, l.loan_amount AS amount, l.status, l.request_date, u.full_name AS customer 
                     FROM loans l 
                     LEFT JOIN users u ON l.customer_id = u.user_id 
                     WHERE l.status = 'Pending' 
                     ORDER BY l.request_date DESC 
                     LIMIT 5";
if ($result = $conn->query($queryRecentLoans)) {
    while ($row = $result->fetch_assoc()) {
        $recentLoans[] = $row;
    }
    $result->free();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard | Credit & Saving System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>

<body class="bg-2 text-color2">
    <!-- Sidebar Navigation -->
    <div class="flex">
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

        <!-- Main Content -->
        <main class="flex-grow p-6 h-screen w-[65%] overflow-auto">
            <h1 class="text-3xl font-bold mb-6 text-color3">
                Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!
            </h1>

            <!-- Quick Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h3 class="text-xl font-semibold">Total Employees</h3>
                    <p class="text-3xl font-bold"><?php echo $totalEmployees; ?></p>
                </div>
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h3 class="text-xl font-semibold">Pending Loans</h3>
                    <p class="text-3xl font-bold"><?php echo $totalPendingLoans; ?></p>
                </div>
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h3 class="text-xl font-semibold">Total Transactions</h3>
                    <p class="text-3xl font-bold"><?php echo number_format($totalTransactions, 2); ?><span
                            class="text-lg">
                            birr<span>
                    </p>
                </div>
            </div>

            <!-- Recent Loan Requests Section -->
            <section>
                <h2 class="text-2xl font-bold mb-4 text-color3">Recent Loan Requests</h2>
                <div class="bg-white shadow-md rounded-lg overflow-auto">
                    <?php if (count($recentLoans) > 0): ?>
                    <table class="w-full">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th class="px-4 py-2">Customer</th>
                                <th class="px-4 py-2">Amount</th>
                                <th class="px-4 py-2">Status</th>
                                <th class="px-4 py-2">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentLoans as $loan): ?>
                            <tr class="border-b hover:bg-gray-100">
                                <td class="px-4 py-2"><?php echo htmlspecialchars($loan['customer'] ?: 'Unknown'); ?>
                                </td>
                                <td class="px-4 py-2">₹<?php echo number_format($loan['amount'], 2); ?></td>
                                <td class="px-4 py-2 text-yellow-500"><?php echo htmlspecialchars($loan['status']); ?>
                                </td>
                                <td class="px-4 py-2">
                                    <a href="manage_loans.php"
                                        class="bg-green-600 text-white px-3 py-1 mr-2 rounded hover:bg-green-700">
                                        Approve
                                    </a>
                                    <a href="manage_loans.php"
                                        class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">
                                        Reject
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="p-6 text-center">No recent loan requests.</div>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>



</body>

</html>