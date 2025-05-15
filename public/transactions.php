<?php
session_start();

// Ensure only authenticated customers can access this page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Customer') {
    header("Location: login.php");
    exit();
}

require 'db_connection.php'; // This file initializes your MySQLi connection in $conn
$customer_id = $_SESSION['user_id']; // Assumes customer’s ID (matching Customers.customer_id) is stored here

// Retrieve all transactions for all accounts belonging to this customer.
// Optionally joining with Employees table to show the name of the employee who processed the transaction.
$sql = "SELECT 
            T.transaction_id, 
            T.transaction_type, 
            T.amount, 
            T.transaction_date, 
            A.account_id,
            E.name AS processed_by_name
        FROM Transactions T 
        JOIN Accounts A ON T.account_id = A.account_id 
        LEFT JOIN Employees E ON T.processed_by = E.employee_id 
        WHERE A.customer_id = ? 
        ORDER BY T.transaction_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$transactions = [];
while ($row = $result->fetch_assoc()){
    $transactions[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions | Bank System</title>
    <!-- Tailwind CSS CDN and custom styles -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet" />
    <link href="styles.css" rel="stylesheet" />
    <style>
    /* Custom mobile hamburger menu */
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
    <!-- Mobile Header with Hamburger Navigation -->
    <header class="bg-primary text-white flex items-center justify-between px-4 py-3 md:hidden">
        <h1 class="text-xl font-bold">Transactions</h1>
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

    <!-- Desktop Layout: Fixed Sidebar and Scrollable Main Content -->
    <div class="hidden md:flex md:h-screen">
        <!-- Sidebar for Desktop (Fixed) with Emoji Icons -->
        <aside class="hidden md:block md:w-1/4 bg-primary text-white max-h-screen p-6 sticky top-0">
            <h2 class="text-2xl font-bold mb-6">Customer Dashboard</h2>
            <nav>
                <ul>
                    <li class="mb-4">
                        <a href="customer_dashboard.php" class="hover:text-color3 flex items-center">
                            <span class="mr-2 text-xl">🏠</span>Dashboard
                        </a>
                    </li>
                    <li class="mb-4">
                        <a href="account_summary.php" class="hover:text-color3 flex items-center">
                            <span class="mr-2 text-xl">📊</span>Account Summary
                        </a>
                    </li>
                    <li class="mb-4">
                        <a href="transactions.php" class="hover:text-color3 flex items-center">
                            <span class="mr-2 text-xl">💸</span>Transactions
                        </a>
                    </li>
                    <li class="mb-4">
                        <a href="loans.php" class="hover:text-color3 flex items-center">
                            <span class="mr-2 text-xl">🏦</span>Loans
                        </a>
                    </li>
                    <li class="mb-4">
                        <a href="customer_profile.php" class="hover:text-color3 flex items-center">
                            <span class="mr-2 text-xl">👤</span>Profile
                        </a>
                    </li>
                    <li class="mb-4">
                        <a href="support.php" class="hover:text-color3 flex items-center">
                            <span class="mr-2 text-xl">🛠</span>Support
                        </a>
                    </li>
                    <li class="mb-4">
                        <a href="logout.php" class="hover:text-color3 flex items-center">
                            <span class="mr-2 text-xl">🚪</span>Logout
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content for Desktop (Scrollable) -->
        <!-- We use a left margin of approximately 25% to accommodate the fixed sidebar -->
        <main class="w-full md:w-3/4 p-6">
            <h1 class="text-2xl font-bold text-color3 mb-6">Transactions</h1>
            <?php if(empty($transactions)): ?>
            <div class="bg-white p-6 rounded-lg shadow text-center">
                <p>No transactions found for your account.</p>
            </div>
            <?php else: ?>
            <div class="bg-white p-6 rounded-lg shadow overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-2">
                        <tr>
                            <th class="px-4 py-2 text-left text-color2">Transaction ID</th>
                            <th class="px-4 py-2 text-left text-color2">Account ID</th>
                            <th class="px-4 py-2 text-left text-color2">Type</th>
                            <th class="px-4 py-2 text-left text-color2">Amount </th>
                            <th class="px-4 py-2 text-left text-color2">Date</th>
                            <th class="px-4 py-2 text-left text-color2">Processed By</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach($transactions as $trans): ?>
                        <tr>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($trans['transaction_id']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($trans['account_id']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($trans['transaction_type']); ?></td>
                            <td class="px-4 py-2"><?php echo number_format($trans['amount'], 2); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($trans['transaction_date']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($trans['processed_by_name'] ?? 'N/A'); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </main>
    </div>

    <!-- Mobile Layout: Transactions -->
    <div class="md:hidden p-4">
        <h1 class="text-2xl font-bold text-color3 mb-4">Transactions</h1>
        <?php if(empty($transactions)): ?>
        <div class="bg-white p-6 rounded-lg shadow text-center">
            <p>No transactions found for your account.</p>
        </div>
        <?php else: ?>
        <div class="bg-white p-6 rounded-lg shadow overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-2">
                    <tr>
                        <th class="px-4 py-2 text-left text-color2">ID</th>
                        <th class="px-4 py-2 text-left text-color2">Acc ID</th>
                        <th class="px-4 py-2 text-left text-color2">Type</th>
                        <th class="px-4 py-2 text-left text-color2">Amount (₹)</th>
                        <th class="px-4 py-2 text-left text-color2">Date</th>
                        <th class="px-4 py-2 text-left text-color2">Processed By</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach($transactions as $trans): ?>
                    <tr>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($trans['transaction_id']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($trans['account_id']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($trans['transaction_type']); ?></td>
                        <td class="px-4 py-2"><?php echo number_format($trans['amount'], 2); ?><span class="text-lg">
                                birr</span></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($trans['transaction_date']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($trans['processed_by_name'] ?? 'N/A'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</body>

</html>