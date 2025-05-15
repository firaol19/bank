<?php
session_start();

// Ensure only authenticated customers can access the page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Customer') {
    header("Location: login.php");
    exit();
}

require 'db_connection.php'; // Initializes $conn (MySQLi connection)
$customer_id = $_SESSION['user_id'];

// Retrieve all loan records for the logged-in customer.
$stmt = $conn->prepare("SELECT loan_id, loan_amount, interest_rate, duration, status, request_date, approval_date,
                        COALESCE(unpaid_amount, 0) AS unpaid_amount 
                        FROM Loans 
                        WHERE customer_id = ? 
                        ORDER BY request_date DESC");

$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$loans = [];
while ($row = $result->fetch_assoc()) {
    $loans[] = $row;
}
$stmt->close();

    $allRepaid = true;
    foreach ($loans as $loan) {
        if ($loan['status'] !== 'Repaid') {
            $allRepaid = false;
            break;
        }
    }
    
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Loans | Bank System</title>
    <!-- Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet" />
    <!-- Custom styles file (defines classes: bg-2, text-color2, bg-primary, text-color3) -->
    <link href="styles.css" rel="stylesheet" />
    <style>
    /* Mobile hamburger menu display */
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
        <h1 class="text-xl font-bold">Loans</h1>
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
        <!-- Sidebar for Desktop (fixed) -->
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

        <!-- Main Content for Desktop (scrollable) -->
        <!-- We use roughly 25% left margin for the fixed sidebar -->
        <main class="w-full md:w-3/4 p-6">
            <h1 class="text-2xl font-bold text-color3 mb-6">Your Loans</h1>

            <!-- Optionally, add a link/button to apply for a new loan -->
            <div class="mb-6">
                <a href="apply_loan.php"
                    class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-color3 transition-all">
                    Apply for a Loan
                </a>
            </div>

            <?php if(empty($loans)): ?>
            <div class="bg-white p-6 rounded-lg shadow text-center">
                <p>No loan records found.</p>
            </div>
            <?php elseif ($allRepaid): ?>

            <div class="bg-white p-6 rounded-lg shadow text-center">
                <p>No loan records found.</p>
            </div>
            <?php else: ?>
            <div class="bg-white p-6 rounded-lg shadow overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-2">
                        <tr>
                            <th class="px-4 py-2 text-left text-color2">Loan ID</th>
                            <th class="px-4 py-2 text-left text-color2">Amount </th>
                            <th class="px-4 py-2 text-left text-color2">Interest Rate (%)</th>
                            <th class="px-4 py-2 text-left text-color2">Duration (Months)</th>
                            <th class="px-4 py-2 text-left text-color2">Status</th>
                            <th class="px-4 py-2 text-left text-color2">Requested On</th>
                            <th class="px-4 py-2 text-left text-color2">Approval Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($loans as $loan): ?>
                        <tr>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($loan['loan_id']); ?></td>
                            <td class="px-4 py-2"><?php echo number_format($loan['loan_amount'], 2); ?><span
                                    class="text-lg">
                                    birr</span></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($loan['interest_rate']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($loan['duration']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($loan['status']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($loan['request_date']); ?></td>
                            <td class="px-4 py-2">
                                <?php 
                      echo $loan['approval_date'] ? htmlspecialchars($loan['approval_date']) : 'N/A'; 
                    ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="mb-6">
                    <a href="repay_loan.php"
                        class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-color3 transition-all">
                        RePay
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>

    <!-- Mobile Layout: Loans Page -->
    <div class="md:hidden p-4">
        <h1 class="text-2xl font-bold text-color3 mb-4">Your Loans</h1>
        <div class="mb-4">
            <a href="apply_loan.php" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-color3 transition-all">
                Apply for a Loan
            </a>
        </div>
        <?php if(empty($loans)): ?>
        <div class="bg-white p-6 rounded-lg shadow text-center">
            <p>No loan records found.</p>
        </div>
        <?php else: ?>
        <div class="bg-white p-6 rounded-lg shadow overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-2">
                    <tr>
                        <th class="px-4 py-2 text-left text-color2">Loan ID</th>
                        <th class="px-4 py-2 text-left text-color2">Amount</th>
                        <th class="px-4 py-2 text-left text-color2">Rate</th>
                        <th class="px-4 py-2 text-left text-color2">Duration</th>
                        <th class="px-4 py-2 text-left text-color2">Status</th>
                        <th class="px-4 py-2 text-left text-color2">Requested</th>
                        <th class="px-4 py-2 text-left text-color2">Approved</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($loans as $loan): ?>
                    <tr>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($loan['loan_id']); ?></td>
                        <td class="px-4 py-2"><?php echo number_format($loan['loan_amount'], 2); ?><span
                                class="text-lg">
                                birr</span></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($loan['interest_rate']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($loan['duration']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($loan['status']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($loan['request_date']); ?></td>
                        <td class="px-4 py-2">
                            <?php echo $loan['approval_date'] ? htmlspecialchars($loan['approval_date']) : 'N/A'; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</body>

</html>