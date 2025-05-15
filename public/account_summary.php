<?php
session_start();

// Ensure only authenticated customers can access this page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Customer') {
    header("Location: login.php");
    exit();
}

require 'db_connection.php'; // Initializes $conn (mysqli connection)
$customer_id = $_SESSION['user_id'];

// Retrieve customer details
$stmt = $conn->prepare("SELECT customer_id, name, account_number, balance, registration_date FROM Customers WHERE customer_id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$customer = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Retrieve all accounts for the customer
$stmt2 = $conn->prepare("SELECT account_id, account_type, balance, status, created_at FROM Accounts WHERE customer_id = ?");
$stmt2->bind_param("i", $customer_id);
$stmt2->execute();
$accounts = $stmt2->get_result();
$stmt2->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Account Summary | Bank System</title>
    <!-- Tailwind CSS CDN and custom styles -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet" />
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
        <h1 class="text-xl font-bold">Account Summary</h1>
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
        <!-- We use a left margin that accommodates the fixed sidebar (approximately 25% width). -->
        <main class="w-full md:w-3/4 p-6">
            <h1 class="text-2xl font-bold text-color3 mb-6">Account Summary</h1>

            <!-- Display Customer Details -->
            <?php if (!$customer): ?>
            <div class="bg-white p-6 rounded-lg shadow text-center">
                <p>Customer details not found. Please contact support.</p>
            </div>
            <?php else: ?>
            <div class="bg-white p-6 rounded-lg shadow mb-6">
                <h2 class="text-xl font-bold text-color3 mb-4">Customer Details</h2>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($customer['name']); ?></p>
                <p><strong>Account Number:</strong> <?php echo htmlspecialchars($customer['account_number']); ?></p>

                <p><strong>Registered On:</strong> <?php echo htmlspecialchars($customer['registration_date']); ?></p>
            </div>
            <?php endif; ?>

            <!-- Display Account Details -->
            <div class="bg-white p-6 rounded-lg shadow mt-5">
                <h2 class="text-xl font-bold text-color3 mb-4">Account Details</h2>
                <?php if ($accounts->num_rows > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-2">
                            <tr>
                                <th class="px-4 py-2 text-left text-color2">Account ID</th>
                                <th class="px-4 py-2 text-left text-color2">Type</th>
                                <th class="px-4 py-2 text-left text-color2">Balance <span class="text-lg">
                                        birr<span></span></th>
                                <th class="px-4 py-2 text-left text-color2">Status</th>
                                <th class="px-4 py-2 text-left text-color2">Created On</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php while ($acc = $accounts->fetch_assoc()): ?>
                            <tr>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($acc['account_id']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($acc['account_type']); ?></td>
                                <td class="px-4 py-2"><?php echo number_format($acc['balance'], 2); ?><span
                                        class="text-lg">
                                        birr</span></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($acc['status']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($acc['created_at']); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-center text-color3">No account details available.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Mobile Layout: Account Summary -->
    <div class="md:hidden p-4">
        <h1 class="text-2xl font-bold text-color3 mb-4">Account Summary</h1>
        <?php if (!$customer): ?>
        <div class="bg-white p-6 rounded-lg shadow text-center">
            <p>Customer details not found. Please contact support.</p>
        </div>
        <?php else: ?>
        <div class="bg-white p-6 rounded-lg shadow mb-4">
            <h2 class="text-xl font-bold text-color3 mb-4">Customer Details</h2>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($customer['name']); ?></p>
            <p><strong>Account Number:</strong> <?php echo htmlspecialchars($customer['account_number']); ?></p>
            <p><strong>Overall Balance:</strong> <?php echo number_format($customer['balance'], 2); ?><span
                    class="text-lg">
                    birr</span></p>
            <p><strong>Registered On:</strong> <?php echo htmlspecialchars($customer['registration_date']); ?></p>
        </div>
        <?php endif; ?>
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-bold text-color3 mb-4">Account Details</h2>
            <?php  
        // For mobile, we re-query (or reuse the earlier $accounts if stored in an array).
        $stmt_mobile = $conn->prepare("SELECT account_id, account_type, balance, status, created_at FROM Accounts WHERE customer_id = ?");
        $stmt_mobile->bind_param("i", $customer_id);
        $stmt_mobile->execute();
        $accounts_mobile = $stmt_mobile->get_result();
        $stmt_mobile->close();
      ?>
            <?php if ($accounts_mobile->num_rows > 0): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-2">
                        <tr>
                            <th class="px-4 py-2 text-left text-color2">Acc ID</th>
                            <th class="px-4 py-2 text-left text-color2">Type</th>
                            <th class="px-4 py-2 text-left text-color2">Balance</th>
                            <th class="px-4 py-2 text-left text-color2">Status</th>
                            <th class="px-4 py-2 text-left text-color2">Created</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php while ($acc = $accounts_mobile->fetch_assoc()): ?>
                        <tr>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($acc['account_id']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($acc['account_type']); ?></td>
                            <td class="px-4 py-2"><?php echo number_format($acc['balance'], 2); ?><span class="text-lg">
                                    birr</span></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($acc['status']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($acc['created_at']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p class="text-center text-color3">No account details available.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>