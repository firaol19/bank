<?php
session_start();

// Ensure only a Manager can access this page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Manager') {
    header("Location: login.php");
    exit();
}

require 'db_connection.php'; // This file should initialize the $conn variable.

$transactions = [];
$queryTransactions = "SELECT t.transaction_id, t.account_id, t.transaction_type, t.amount, t.transaction_date, u.full_name AS processed_by
                      FROM transactions t
                      LEFT JOIN users u ON t.processed_by = u.user_id
                      ORDER BY t.transaction_date DESC";
if ($result = $conn->query($queryTransactions)) {
    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }
    $result->free();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Transactions | Credit & Saving System</title>
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

        <!-- Main Content: Manage Transactions -->
        <main class="flex-grow p-6 h-screen w-[65%] overflow-auto">
            <h1 class="text-3xl font-bold mb-6 text-color3">Manage Transactions</h1>
            <p class="text-lg mb-6">
                Review all processed transactions.
            </p>
            <div class="bg-white shadow-md rounded-lg overflow-auto">
                <?php if (count($transactions) > 0): ?>
                <table class="w-full">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="px-4 py-2">Transaction ID</th>
                            <th class="px-4 py-2">Account ID</th>
                            <th class="px-4 py-2">Type</th>
                            <th class="px-4 py-2">Amount</th>
                            <th class="px-4 py-2">Date</th>
                            <th class="px-4 py-2">Processed By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $trans): ?>
                        <tr class="border-b hover:bg-gray-100">
                            <td class="px-4 py-2"><?php echo htmlspecialchars($trans['transaction_id']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($trans['account_id']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($trans['transaction_type']); ?></td>
                            <td class="px-4 py-2"><?php echo number_format($trans['amount'], 2); ?><span
                                    class="text-lg">
                                    birr</span></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($trans['transaction_date']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($trans['processed_by'] ?: 'Unknown'); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="p-6 text-center">No transactions found.</div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>

</html>