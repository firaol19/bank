<?php
session_start();

// Ensure only a Manager can access this page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Manager') {
    header("Location: login.php");
    exit();
}

require 'db_connection.php'; // Initializes $conn

// ----- Transaction Reports -----
$totalDeposits = 0;
$queryDeposits = "SELECT IFNULL(SUM(amount), 0) AS totalDeposits FROM transactions WHERE transaction_type='Deposit'";
if ($result = $conn->query($queryDeposits)) {
    if ($row = $result->fetch_assoc()) {
        $totalDeposits = $row['totalDeposits'];
    }
    $result->free();
}

$totalWithdrawals = 0;
$queryWithdrawals = "SELECT IFNULL(SUM(amount), 0) AS totalWithdrawals FROM transactions WHERE transaction_type='Withdrawal'";
if ($result = $conn->query($queryWithdrawals)) {
    if ($row = $result->fetch_assoc()) {
        $totalWithdrawals = $row['totalWithdrawals'];
    }
    $result->free();
}

$totalTransfers = 0;
$queryTransfers = "SELECT IFNULL(SUM(amount), 0) AS totalTransfers FROM transactions WHERE transaction_type='Transfer'";
if ($result = $conn->query($queryTransfers)) {
    if ($row = $result->fetch_assoc()) {
        $totalTransfers = $row['totalTransfers'];
    }
    $result->free();
}

// ----- Loan Reports -----
$approvedLoansCount = 0;
$queryApprovedLoans = "SELECT COUNT(*) AS approvedCount FROM loans WHERE status='Approved'";
if ($result = $conn->query($queryApprovedLoans)) {
    if ($row = $result->fetch_assoc()) {
        $approvedLoansCount = $row['approvedCount'];
    }
    $result->free();
}

$pendingLoansCount = 0;
$queryPendingLoans = "SELECT COUNT(*) AS pendingCount FROM loans WHERE status='Pending'";
if ($result = $conn->query($queryPendingLoans)) {
    if ($row = $result->fetch_assoc()) {
        $pendingLoansCount = $row['pendingCount'];
    }
    $result->free();
}

$rejectedLoansCount = 0;
$queryRejectedLoans = "SELECT COUNT(*) AS rejectedCount FROM loans WHERE status='Rejected'";
if ($result = $conn->query($queryRejectedLoans)) {
    if ($row = $result->fetch_assoc()) {
        $rejectedLoansCount = $row['rejectedCount'];
    }
    $result->free();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics | Credit & Saving System</title>
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
                    <li class="mb-4"><a href="manager_dashboard.php" class="hover:text-color3">🏠 Overview</a></li>
                    <li class="mb-4"><a href="register_employee.php" class="hover:text-color3">👤 Register Employee</a>
                    </li>
                    <li class="mb-4"><a href="manage_loans.php" class="hover:text-color3">🏦 Loan Approvals</a></li>
                    <li class="mb-4"><a href="manage_transactions.php" class="hover:text-color3">💰 Transactions</a>
                    </li>
                    <li class="mb-4"><a href="customer_accounts.php" class="hover:text-color3">👥 Customer Accounts</a>
                    </li>
                    <li class="mb-4"><a href="view_reports.php" class="hover:text-color3">📊 Reports & Analytics</a>
                    </li>
                    <li class="mb-4"><a href="system_settings.php" class="hover:text-color3">⚙️ System Settings</a></li>
                    <li class="mb-4"><a href="logout.php" class="hover:text-color3">🚪 Logout</a></li>
                </ul>
            </nav>
        </aside>
        <!-- Main Content: Reports & Analytics -->
        <main class="flex-grow p-6 h-screen w-[65%] overflow-auto">
            <h1 class="text-3xl font-bold mb-6 text-color3">Reports & Analytics</h1>

            <!-- Transaction Reports Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <h3 class="text-xl font-semibold">Total Deposits</h3>
                    <p class="text-3xl font-bold"><?php echo number_format($totalDeposits, 2); ?><span class="text-lg">
                            birr<span></span></p>
                </div>
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <h3 class="text-xl font-semibold">Total Withdrawals</h3>
                    <p class="text-3xl font-bold"><?php echo number_format($totalWithdrawals, 2); ?><span
                            class="text-lg">
                            birr<span></span></p>
                </div>
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <h3 class="text-xl font-semibold">Total Transfers</h3>
                    <p class="text-3xl font-bold"><?php echo number_format($totalTransfers, 2); ?><span class="text-lg">
                            birr<span></span></p>
                </div>
            </div>

            <!-- Loan Reports Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <h3 class="text-xl font-semibold">Approved Loans</h3>
                    <p class="text-3xl font-bold"><?php echo $approvedLoansCount; ?></p>
                </div>
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <h3 class="text-xl font-semibold">Pending Loans</h3>
                    <p class="text-3xl font-bold"><?php echo $pendingLoansCount; ?></p>
                </div>
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <h3 class="text-xl font-semibold">Rejected Loans</h3>
                    <p class="text-3xl font-bold"><?php echo $rejectedLoansCount; ?></p>
                </div>
            </div>


        </main>
    </div>
</body>

</html>