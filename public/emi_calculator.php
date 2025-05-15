<?php
session_start();

// Ensure only authenticated employees can access this page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Employee') {
    header("Location: login.php");
    exit();
}

// Initialize variables
$loan_amount = $annual_rate = $tenure = $emi = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve and sanitize input data
    $loan_amount = isset($_POST['loan_amount']) ? floatval($_POST['loan_amount']) : 0;
    $annual_rate = isset($_POST['annual_rate']) ? floatval($_POST['annual_rate']) : 0;
    $tenure      = isset($_POST['tenure']) ? intval($_POST['tenure']) : 0;
    
    // Validate inputs
    if ($loan_amount > 0 && $annual_rate > 0 && $tenure > 0) {
        // Calculate monthly interest rate (in decimal)
        $monthly_rate = $annual_rate / 12 / 100;
        // EMI formula: EMI = [P * r * (1 + r)^n] / [(1 + r)^n - 1]
        $emi = ($loan_amount * $monthly_rate * pow(1 + $monthly_rate, $tenure)) / (pow(1 + $monthly_rate, $tenure) - 1);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EMI Calculator | Employee Dashboard</title>
    <!-- Tailwind CSS CDN and Custom Styles -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet" />
    <link href="styles.css" rel="stylesheet" />
    <style>
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
        <h1 class="text-xl font-bold">EMI Calculator</h1>
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
            <li class="mb-4"><a href="employee_dashboard.php" class="hover:text-color3">🏠 Overview</a></li>
            <li class="mb-4"><a href="employee_tasks.php" class="hover:text-color3">📝 My Tasks</a></li>
            <li class="mb-4"><a href="loan_review.php" class="hover:text-color3">🔍 Preliminary Loan Review</a>
            </li>
            <li class="mb-4"><a href="employee_transactions.php" class="hover:text-color3">💰 Transaction
                    History</a></li>
            <li class="mb-4"><a href="employee_deposit.php" class="hover:text-color3">💰 Deposit</a>
            <li class="mb-4"><a href="withdrawal.php" class="hover:text-color3">💰 Withdrawal</a>
            <li class="mb-4"><a href="customer_support.php" class="hover:text-color3">👥 Customer Support</a>
            </li>
            <li class="py-1"><a href="emi_calculator.php" class="block">🧮 EMI Calculator</a></li>
            <li class="mb-4"><a href="employee_profile.php" class="hover:text-color3">👤 My Profile</a></li>
            <li class="mb-4"><a href="logout.php" class="hover:text-color3">🚪 Logout</a></li>
        </ul>
    </nav>

    <!-- Desktop Layout: Fixed Sidebar and Scrollable Main Content -->
    <div class="hidden md:flex md:h-screen">
        <!-- Desktop Sidebar with Emoji Navigation -->
        <aside class="w-[25%] bg-primary text-white h-screen p-6 sticky">
            <h2 class="text-2xl font-bold mb-6">Employee Dashboard</h2>
            <nav>
                <ul>
                    <li class="mb-4"><a href="employee_dashboard.php" class="hover:text-color3">🏠 Overview</a></li>
                    <li class="mb-4"><a href="employee_tasks.php" class="hover:text-color3">📝 My Tasks</a></li>
                    <li class="mb-4"><a href="loan_review.php" class="hover:text-color3">🔍 Preliminary Loan Review</a>
                    </li>
                    <li class="mb-4"><a href="employee_transactions.php" class="hover:text-color3">💰 Transaction
                            History</a></li>
                    <li class="mb-4"><a href="employee_deposit.php" class="hover:text-color3">💰 Deposit</a>
                    <li class="mb-4"><a href="withdrawal.php" class="hover:text-color3">💰 Withdrawal</a>
                    <li class="mb-4"><a href="customer_support.php" class="hover:text-color3">👥 Customer Support</a>
                    </li>
                    <li class="py-1"><a href="emi_calculator.php" class="hover:text-color3">🧮 EMI Calculator</a></li>
                    <li class="mb-4"><a href="employee_profile.php" class="hover:text-color3">👤 My Profile</a></li>
                    <li class="mb-4"><a href="logout.php" class="hover:text-color3">🚪 Logout</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content for Desktop -->
        <main class="flex-grow p-6 h-screen w-[75%] overflow-auto">
            <h1 class="text-2xl font-bold text-color3 mb-6">EMI Calculator</h1>
            <div class="bg-white p-6 rounded-lg shadow max-w-lg mx-auto">
                <form method="POST" action="emi_calculator.php">
                    <div class="mb-4">
                        <label for="loan_amount" class="block text-lg text-color3 mb-2">Loan Amount (ብር)</label>
                        <input type="number" step="0.01" min="0" id="loan_amount" name="loan_amount"
                            placeholder="Enter loan amount" value="<?php echo htmlspecialchars($loan_amount); ?>"
                            required
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                    </div>
                    <div class="mb-4">
                        <label for="annual_rate" class="block text-lg text-color3 mb-2">Annual Interest Rate (%)</label>
                        <input type="number" step="0.01" min="0" id="annual_rate" name="annual_rate"
                            placeholder="Enter annual interest rate"
                            value="<?php echo htmlspecialchars($annual_rate); ?>" required
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                    </div>
                    <div class="mb-4">
                        <label for="tenure" class="block text-lg text-color3 mb-2">Tenure (Months)</label>
                        <input type="number" min="1" id="tenure" name="tenure" placeholder="Enter tenure in months"
                            value="<?php echo htmlspecialchars($tenure); ?>" required
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" name="submit"
                            class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-color3 transition-all">
                            Calculate EMI
                        </button>
                    </div>
                </form>
                <?php if ($_SERVER["REQUEST_METHOD"] === "POST" && $emi): ?>
                <div class="mt-6 p-4 bg-green-100 text-green-700 rounded">
                    <p class="text-lg">Estimated EMI is: <strong>ብር <?php echo number_format($emi, 2); ?></strong> per
                        month.</p>
                </div>
                <?php elseif ($_SERVER["REQUEST_METHOD"] === "POST"): ?>
                <div class="mt-6 p-4 bg-red-100 text-red-700 rounded">
                    <p class="text-lg">Please enter all the values correctly to calculate EMI.</p>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Mobile Layout: EMI Calculator -->
    <div class="md:hidden p-4">
        <h1 class="text-2xl font-bold text-color3 mb-4">EMI Calculator</h1>
        <div class="bg-white p-6 rounded-lg shadow">
            <form method="POST" action="emi_calculator.php">
                <div class="mb-4">
                    <label for="loan_amount_mobile" class="block text-lg text-color3 mb-2">Loan Amount (ብር)</label>
                    <input type="number" step="0.01" min="0" id="loan_amount_mobile" name="loan_amount"
                        placeholder="Enter loan amount" value="<?php echo htmlspecialchars($loan_amount); ?>" required
                        class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                </div>
                <div class="mb-4">
                    <label for="annual_rate_mobile" class="block text-lg text-color3 mb-2">Annual Interest Rate
                        (%)</label>
                    <input type="number" step="0.01" min="0" id="annual_rate_mobile" name="annual_rate"
                        placeholder="Enter annual interest rate" value="<?php echo htmlspecialchars($annual_rate); ?>"
                        required class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                </div>
                <div class="mb-4">
                    <label for="tenure_mobile" class="block text-lg text-color3 mb-2">Tenure (Months)</label>
                    <input type="number" min="1" id="tenure_mobile" name="tenure" placeholder="Enter tenure in months"
                        value="<?php echo htmlspecialchars($tenure); ?>" required
                        class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                </div>
                <div class="flex justify-end">
                    <button type="submit" name="submit"
                        class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-color3 transition-all">
                        Calculate EMI
                    </button>
                </div>
            </form>
            <?php if ($_SERVER["REQUEST_METHOD"] === "POST" && $emi): ?>
            <div class="mt-6 p-4 bg-green-100 text-green-700 rounded">
                <p class="text-lg">Estimated EMI is: <strong>ብር <?php echo number_format($emi, 2); ?></strong> per
                    month.</p>
            </div>
            <?php elseif ($_SERVER["REQUEST_METHOD"] === "POST"): ?>
            <div class="mt-6 p-4 bg-red-100 text-red-700 rounded">
                <p class="text-lg">Please enter all the values correctly to calculate EMI.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>