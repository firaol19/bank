<?php
session_start();

// Only allow logged-in customers.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Customer') {
    header("Location: login.php");
    exit();
}

require 'db_connection.php'; // Assumes $conn is initialized (MySQLi connection)
$customer_id = $_SESSION['user_id'];

// Check which accounts the customer already has.
$stmt = $conn->prepare("SELECT account_type FROM Accounts WHERE customer_id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();

$hasSavings = false;
$hasLoan    = false;
while ($row = $result->fetch_assoc()) {
    if ($row['account_type'] === 'Savings') {
        $hasSavings = true;
    }
    if ($row['account_type'] === 'Loan') {
        $hasLoan = true;
    }
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Open Account | Bank System</title>
    <!-- Tailwind CSS CDN and custom styles -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="styles.css" />
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
        <h1 class="text-xl font-bold">Open Account</h1>
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
        <main class="w-full md:w-3/4 p-6">
            <h1 class="text-2xl font-bold text-color3 mb-6">Open Account</h1>
            <?php if ($hasSavings && $hasLoan): ?>
            <!-- Both accounts exist – display error message -->
            <div class="bg-white p-6 rounded-lg shadow text-center">
                <p>You already have both a Savings and Loan account. Duplicate account creation is not allowed.</p>
            </div>
            <?php else: ?>
            <div class="bg-white p-6 rounded-lg shadow max-w-lg mx-auto">
                <form action="process_open_account.php" method="POST">
                    <?php if (!$hasSavings && !$hasLoan): ?>
                    <!-- If neither account exists, allow the customer to choose -->
                    <div class="mb-4">
                        <label class="block text-lg text-color3 mb-2">Select Account Type</label>
                        <div class="flex items-center space-x-4">
                            <label class="inline-flex items-center">
                                <input type="radio" class="form-radio" name="account_type" value="Savings" required />
                                <span class="ml-2">Savings Account</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" class="form-radio" name="account_type" value="Loan" required />
                                <span class="ml-2">Loan Account</span>
                            </label>
                        </div>
                    </div>
                    <?php elseif (!$hasSavings): ?>
                    <!-- Only Savings account is missing -->
                    <input type="hidden" name="account_type" value="Savings">
                    <div class="mb-4">
                        <p class="text-lg text-color3">You do not have a Savings Account yet.</p>
                    </div>
                    <?php elseif (!$hasLoan): ?>
                    <!-- Only Loan account is missing -->
                    <input type="hidden" name="account_type" value="Loan">
                    <div class="mb-4">
                        <p class="text-lg text-color3">You do not have a Loan Account yet.</p>
                    </div>
                    <?php endif; ?>
                    <!-- Since customers cannot deposit money, no initial deposit field is shown. -->
                    <div class="flex justify-end">
                        <button type="submit"
                            class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-color3 transition-all">
                            Open Account
                        </button>
                    </div>
                </form>
            </div>
            <?php endif; ?>
        </main>
    </div>

    <!-- Mobile Layout: Open Account -->
    <div class="md:hidden p-4">
        <h1 class="text-2xl font-bold text-color3 mb-4">Open Account</h1>
        <?php if ($hasSavings && $hasLoan): ?>
        <div class="bg-white p-6 rounded-lg shadow text-center">
            <p>You already have both a Savings and Loan account. Duplicate account creation is not allowed.</p>
        </div>
        <?php else: ?>
        <div class="bg-white p-6 rounded-lg shadow">
            <form action="process_open_account.php" method="POST">
                <?php if (!$hasSavings && !$hasLoan): ?>
                <div class="mb-4">
                    <label class="block text-lg text-color3 mb-2">Select Account Type</label>
                    <div class="flex items-center space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" class="form-radio" name="account_type" value="Savings" required />
                            <span class="ml-2">Savings Account</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" class="form-radio" name="account_type" value="Loan" required />
                            <span class="ml-2">Loan Account</span>
                        </label>
                    </div>
                </div>
                <?php elseif (!$hasSavings): ?>
                <input type="hidden" name="account_type" value="Savings">
                <div class="mb-4">
                    <p class="text-lg text-color3">You do not have a Savings Account yet.</p>
                </div>
                <?php elseif (!$hasLoan): ?>
                <input type="hidden" name="account_type" value="Loan">
                <div class="mb-4">
                    <p class="text-lg text-color3">You do not have a Loan Account yet.</p>
                </div>
                <?php endif; ?>
                <div class="flex justify-end">
                    <button type="submit"
                        class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-color3 transition-all">
                        Open Account
                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>
</body>

</html>