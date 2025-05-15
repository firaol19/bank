<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Customer') {
    header("Location: login.php");
    exit();
}

require_once 'db_connection.php';
$customer_id = $_SESSION['user_id'];

// Fetch customer's active loan record (assuming a customer has at most one active loan)
$stmt = $conn->prepare("SELECT loan_id, loan_amount, unpaid_amount, status FROM loans WHERE customer_id = ? AND status != 'Repaid' LIMIT 1");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
if ($loan = $result->fetch_assoc()) {
    $loan_id       = $loan['loan_id'];
    $loan_amount   = $loan['loan_amount'];
    $unpaid_amount = $loan['unpaid_amount'];
    $loan_status   = $loan['status'];
} else {
    $loan = null;
}
$stmt->close();

// Fetch customer's Savings account (used for repayment)
$stmt = $conn->prepare("SELECT account_id, balance FROM accounts WHERE customer_id = ? AND account_type = 'Savings' LIMIT 1");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
if ($savings = $result->fetch_assoc()) {
    $savings_account_id = $savings['account_id'];
    $savings_balance    = $savings['balance'];
} else {
    $savings = null;
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Loan Repayment | Customer Dashboard</title>
    <!-- Tailwind CSS CDN -->
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
    document.addEventListener("DOMContentLoaded", function() {
        const repayAllBtn = document.getElementById("repay_all");
        const repayInput = document.getElementById("repay_amount");
        if (repayAllBtn) {
            repayAllBtn.addEventListener("click", function(e) {
                e.preventDefault();
                const unpaid = document.getElementById("hidden_unpaid").value;
                repayInput.value = unpaid;
            });
        }
    });
    </script>
</head>

<body class="bg-2 text-color2">
    <!-- Header -->
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
        <aside class="hidden md:block md:w-1/4 bg-primary text-white h-screen p-6 sticky top-0">
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
                    <li class="mb-4"><a href="repay_loan.php" class="hover:text-color3">💸 Repay Loan</a></li>
                    <li class="mb-4"><a href="logout.php" class="hover:text-color3">🚪 Logout</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="w-full md:w-3/4 p-6">
            <h1 class="text-xl font-bold text-color3">Loan Repayment</h1>
            <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow">
                <?php if (!$loan): ?>
                <p class="text-lg">You do not have any active loans to repay.</p>
                <?php elseif (!$savings): ?>
                <p class="text-lg">No savings account found. Please contact support.</p>
                <?php else: ?>
                <p class="mb-4 text-lg">
                    Loan Unpaid Amount: <strong>ብር <?php echo number_format($unpaid_amount ?? 0, 2); ?></strong><br>

                    Your Savings Balance: <strong>ብር <?php echo number_format($savings_balance, 2); ?></strong>
                </p>
                <form action="process_repay.php" method="POST">
                    <!-- Hidden field to carry the loan ID and current unpaid amount -->
                    <input type="hidden" name="loan_id" value="<?php echo $loan_id; ?>">
                    <input type="hidden" id="hidden_unpaid" name="hidden_unpaid" value="<?php echo $unpaid_amount; ?>">
                    <div class="mb-4">
                        <label for="repay_amount" class="block text-lg text-color3 mb-2">Repayment Amount (ብር)</label>
                        <input type="number" step="0.01" min="0" id="repay_amount" name="repay_amount"
                            placeholder="Enter amount to repay" required class="w-full p-3 border rounded-lg" />
                    </div>
                    <div class="flex items-center space-x-4">
                        <button type="button" id="repay_all" class="bg-blue-500 text-white px-4 py-2 rounded">Repay
                            All</button>
                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Submit
                            Repayment</button>
                    </div>
                    <p class="mt-4 text-sm text-gray-600">Note: Your repayment will be deducted from your Savings
                        account if
                        sufficient funds exist.</p>
                </form>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>

</html>