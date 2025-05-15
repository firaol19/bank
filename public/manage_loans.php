<?php
session_start();

// Ensure only a Manager can access this page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Manager') {
    header("Location: login.php");
    exit();
}

require 'db_connection.php'; // This file should initialize the $conn variable.

// Fetch Pending Loans (select all pending loans)
$pendingLoans = [];
$queryPendingLoans = "SELECT l.loan_id, l.loan_amount AS amount, l.status, l.request_date, u.full_name AS customer 
                      FROM loans l 
                      LEFT JOIN users u ON l.customer_id = u.user_id 
                      WHERE l.status = 'Pending' 
                      ORDER BY l.request_date DESC";
if ($result = $conn->query($queryPendingLoans)) {
    while ($row = $result->fetch_assoc()) {
        $pendingLoans[] = $row;
    }
    $result->free();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Loans | Credit & Saving System</title>
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

        <!-- Main Content: Manage Loans -->
        <main class="flex-grow p-6 h-screen w-[65%] overflow-auto">
            <h1 class="text-3xl font-bold mb-6 text-color3">Manage Loans</h1>
            <p class="text-lg mb-6">
                Review pending loan requests and manage their approval status.
            </p>
            <div class="bg-white shadow-md rounded-lg overflow-auto">
                <?php if (count($pendingLoans) > 0): ?>
                <table class="w-full">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="px-4 py-2">Loan ID</th>
                            <th class="px-4 py-2">Customer</th>
                            <th class="px-4 py-2">Amount</th>
                            <th class="px-4 py-2">Request Date</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingLoans as $loan): ?>
                        <tr class="border-b hover:bg-gray-100">
                            <td class="px-4 py-2"><?php echo htmlspecialchars($loan['loan_id']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($loan['customer'] ?: 'Unknown'); ?></td>
                            <td class="px-4 py-2"><?php echo number_format($loan['amount'], 2); ?><span class="text-lg">
                                    birr</span></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($loan['request_date']); ?></td>
                            <td class="px-4 py-2 text-yellow-500"><?php echo htmlspecialchars($loan['status']); ?></td>
                            <td class="px-4 py-2">
                                <a href="approve_loan.php?loan_id=<?php echo $loan['loan_id']; ?>"
                                    class="bg-green-600 text-white px-3 py-1 mr-2 rounded hover:bg-green-700">
                                    Approve
                                </a>
                                <a href="reject_loan.php?loan_id=<?php echo $loan['loan_id']; ?>"
                                    class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">
                                    Reject
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="p-6 text-center">No pending loan requests.</div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>

</html>