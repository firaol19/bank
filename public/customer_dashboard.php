<?php
session_start();

// Ensure only authenticated customers can access this page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Customer') {
    header("Location: login.php");
    exit();
}

require 'db_connection.php'; // This file initializes the $conn variable.
$customer_id = $_SESSION['user_id'];  // Using session user_id to represent the customer

// Fetch account balance from the accounts table (using customer_id)
$account_balance = 0;
$queryBalance = "SELECT balance FROM accounts WHERE customer_id = ?";
$stmt = $conn->prepare($queryBalance);
if ($stmt) {
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $resultBalance = $stmt->get_result();
    if ($row = $resultBalance->fetch_assoc()) {
         $account_balance = $row['balance'];
    }
    $stmt->close();
} else {
    die("Database error: " . $conn->error);
}

// Fetch recent transactions by joining transactions and accounts tables
$queryTransactions = "SELECT t.transaction_id, t.transaction_type, t.amount, t.transaction_date 
                      FROM transactions t 
                      JOIN accounts a ON t.account_id = a.account_id 
                      WHERE a.customer_id = ? 
                      ORDER BY t.transaction_date DESC LIMIT 5";
$recent_transactions = [];
$stmt2 = $conn->prepare($queryTransactions);
if ($stmt2) {
   $stmt2->bind_param("i", $customer_id);
   $stmt2->execute();
   $resultTransactions = $stmt2->get_result();
   while ($row = $resultTransactions->fetch_assoc()){
       $recent_transactions[] = $row;
   }
   $stmt2->close();
} else {
    die("Database error: " . $conn->error);
}

// Fetch active loans (using request_date instead of due_date, per our project schema)
$queryLoans = "SELECT loan_id, loan_amount, status, request_date FROM loans WHERE customer_id = ? AND status = 'Active'";
$active_loans = [];
$stmt3 = $conn->prepare($queryLoans);
if ($stmt3) {
    $stmt3->bind_param("i", $customer_id);
    $stmt3->execute();
    $resultLoans = $stmt3->get_result();
    while ($row = $resultLoans->fetch_assoc()){
          $active_loans[] = $row;
    }
    $stmt3->close();
} else {
    die("Database error: " . $conn->error);
}

// Fetch notifications (assume a "notifications" table with target_role = 'Customer')
$queryNotif = "SELECT notification_id, message, created_at 
               FROM notifications 
               WHERE target_role = 'Customer' 
               ORDER BY created_at DESC LIMIT 3";
$notifications = [];
$resultNotify = $conn->query($queryNotif);
if ($resultNotify) {
   while ($row = $resultNotify->fetch_assoc()){
         $notifications[] = $row;
   }
   $resultNotify->free();
} else {
    die("Database error: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Customer Dashboard | Credit & Saving System</title>
    <!-- Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet" />
    <link href="styles.css" rel="stylesheet" />
    <style>
    /* Custom styles for mobile hamburger menu */
    .mobile-menu {
        display: none;
    }

    @media (max-width: 767px) {
        .mobile-menu {
            display: block;
        }
    }
    </style>
    <script>
    function toggleMobileMenu() {
        var menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
    }
    </script>
</head>

<body class="bg-2 text-color2">
    <!-- Header with Hamburger Navigation for Mobile -->
    <header class="bg-primary text-white flex items-center justify-between px-4 py-3 md:hidden">
        <h1 class="text-xl font-bold">Customer Dashboard</h1>
        <button onclick="toggleMobileMenu()">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                </path>
            </svg>
        </button>
    </header>
    <!-- Mobile Navigation Menu -->
    <nav id="mobile-menu" class="bg-primary text-white px-4 py-2 hidden md:hidden">
        <ul>
            <li class="py-1"><a href="customer_dashboard.php" class="block">🏠 Dashboard</a></li>
            <li class="py-1"><a href="account_summary.php" class="block">📊 Account Summary</a></li>
            <li class="py-1"><a href="transactions.php" class="block">💸 Transactions</a></li>
            <li class="py-1"><a href="loans.php" class="block">🏦 Loans</a></li>
            <li class="py-1"><a href="customer_profile.php" class="block">👤 Profile</a></li>
            <li class="py-1"><a href="support.php" class="block">🛠 Support</a></li>
            <li class="py-1"><a href="logout.php" class="block">🚪 Logout</a></li>
        </ul>
    </nav>

    <div class="flex">
        <!-- Sidebar for Desktop -->
        <aside class="hidden md:block md:w-1/4 bg-primary text-white max-h-screen p-6 sticky top-0">
            <h2 class="text-2xl font-bold mb-6">Customer Dashboard</h2>
            <nav>
                <ul>
                    <li class="mb-4"><a href="customer_dashboard.php" class="hover:text-color3">🏠 Dashboard</a></li>
                    <li class="mb-4"><a href="account_summary.php" class="hover:text-color3">📊 Account Summary</a></li>
                    <li class="mb-4"><a href="transactions.php" class="hover:text-color3">💸 Transactions</a></li>
                    <li class="mb-4"><a href="loans.php" class="hover:text-color3">🏦 Loans</a></li>
                    <li class="mb-4"><a href="customer_profile.php" class="hover:text-color3">👤 Profile</a></li>
                    <li class="mb-4"><a href="support.php" class="hover:text-color3">🛠 Support</a></li>
                    <li class="mb-4"><a href="financial_statement_report.php" class="hover:text-color3">🛠
                            Financial Statement
                            S</a>
                    </li>

                    <li class="mb-4"><a href="logout.php" class="hover:text-color3">🚪 Logout</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="w-full md:w-3/4 p-6">
            <!-- Dashboard Overview Cards -->
            <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-color3">Account Balance</h3>
                    <p class="text-3xl font-bold mt-2"><?php echo number_format($account_balance, 2); ?><span
                            class="text-lg">
                            birr<span></span></p>
                </div>
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-color3">Transactions</h3>
                    <p class="text-3xl font-bold mt-2"><?php echo count($recent_transactions); ?></p>
                </div>
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-color3">Active Loans</h3>
                    <p class="text-3xl font-bold mt-2"><?php echo count($active_loans); ?></p>
                </div>
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-color3">Notifications</h3>
                    <p class="text-3xl font-bold mt-2"><?php echo count($notifications); ?></p>
                </div>
            </section>

            <!-- Quick Actions -->
            <section class="mb-6">
                <h2 class="text-2xl font-bold text-color3 mb-4">Quick Actions</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="open_account.php"
                        class="bg-primary text-white flex flex-col items-center justify-center p-4 rounded-lg shadow hover:bg-color3 transition-colors">
                        <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        <span>Open Account</span>
                    </a>
                    <a href="transfer.php"
                        class="bg-primary text-white flex flex-col items-center justify-center p-4 rounded-lg shadow hover:bg-color3 transition-colors">
                        <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8h2a2 2 0 012 2v8a2 2 0 01-2 2h-2M7 8H5a2 2 0 00-2 2v8a2 2 0 002 2h2m10-12l-5-5m0 0L7 8m5-5v18">
                            </path>
                        </svg>
                        <span>Transfer</span>
                    </a>
                    <a href="apply_loan.php"
                        class="bg-primary text-white flex flex-col items-center justify-center p-4 rounded-lg shadow hover:bg-color3 transition-colors">
                        <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>Apply Loan</span>
                    </a>
                    <a href="pay_bills.php"
                        class="bg-primary text-white flex flex-col items-center justify-center p-4 rounded-lg shadow hover:bg-color3 transition-colors">
                        <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2a2 2 0 012-2h2a2 2 0 012 2v2m-6 4h6a2 2 0 002-2v-6a2 2 0 00-2-2h-6a2 2 0 00-2 2v6a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span>Pay Bills</span>
                    </a>
                </div>
            </section>

            <!-- Recent Transactions -->
            <section class="mb-6">
                <h2 class="text-2xl font-bold text-color3 mb-4">Recent Transactions</h2>
                <?php if(count($recent_transactions) > 0): ?>
                <div class="bg-white shadow rounded-lg overflow-auto">
                    <table class="w-full">
                        <thead class="bg-2">
                            <tr>
                                <th class="px-4 py-2 text-left text-color2">ID</th>
                                <th class="px-4 py-2 text-left text-color2">Type</th>
                                <th class="px-4 py-2 text-left text-color2">Amount</th>
                                <th class="px-4 py-2 text-left text-color2">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($recent_transactions as $trans): ?>
                            <tr class="border-b">
                                <td class="px-4 py-2"><?php echo htmlspecialchars($trans['transaction_id']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($trans['transaction_type']); ?></td>
                                <td class="px-4 py-2"><?php echo number_format($trans['amount'], 2); ?><span
                                        class="text-lg">
                                        birr</span></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($trans['transaction_date']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <p>No recent transactions found.</p>
                </div>
                <?php endif; ?>
            </section>

            <!-- Active Loans -->
            <section class="mb-6">
                <h2 class="text-2xl font-bold text-color3 mb-4">Active Loans</h2>
                <?php if(count($active_loans) > 0): ?>
                <div class="bg-white shadow rounded-lg overflow-auto">
                    <table class="w-full">
                        <thead class="bg-2">
                            <tr>
                                <th class="px-4 py-2 text-left text-color2">Loan ID</th>
                                <th class="px-4 py-2 text-left text-color2">Amount</th>
                                <th class="px-4 py-2 text-left text-color2">Request Date</th>
                                <th class="px-4 py-2 text-left text-color2">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($active_loans as $loan): ?>
                            <tr class="border-b">
                                <td class="px-4 py-2"><?php echo htmlspecialchars($loan['loan_id']); ?></td>
                                <td class="px-4 py-2"><?php echo number_format($loan['loan_amount'], 2); ?><span
                                        class="text-lg">
                                        birr</span></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($loan['request_date']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($loan['status']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <p>No active loans found.</p>
                </div>
                <?php endif; ?>
            </section>

            <!-- Notifications -->
            <section>
                <h2 class="text-2xl font-bold text-color3 mb-4">Notifications</h2>
                <?php if(count($notifications) > 0): ?>
                <div class="bg-white shadow rounded-lg p-4">
                    <ul class="divide-y divide-color2">
                        <?php foreach($notifications as $note): ?>
                        <li class="py-2">
                            <p class="text-color2"><?php echo htmlspecialchars($note['message']); ?></p>
                            <small class="text-color2"><?php echo htmlspecialchars($note['created_at']); ?></small>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php else: ?>
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <p>No notifications available.</p>
                </div>
                <?php endif; ?>
            </section>

        </main>
    </div>
</body>

</html>