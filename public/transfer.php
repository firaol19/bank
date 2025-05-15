<?php
session_start();

// Ensure only authenticated customers can access this page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Customer') {
    header("Location: login.php");
    exit();
}

require 'db_connection.php'; // This file initializes the $conn variable.
$customer_id = $_SESSION['user_id'];  // We assume the logged-in customer's ID is stored here.

// Query active Savings accounts for the customer.
$queryAccounts = "SELECT account_id, account_type, balance 
                  FROM Accounts 
                  WHERE customer_id = ? 
                    AND status = 'Active' 
                    AND account_type = 'Savings'";
$stmt = $conn->prepare($queryAccounts);
$accounts = [];
if ($stmt) {
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $resultAccounts = $stmt->get_result();
    while ($row = $resultAccounts->fetch_assoc()) {
        $accounts[] = $row;
    }
    $stmt->close();
} else {
    die("Database error: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Transfer Funds | Bank System</title>
    <!-- Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet" />
    <!-- Custom styles (your styles.css should define bg-2, text-color2, bg-primary, and text-color3) -->
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
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const checkBtn = document.getElementById("check_button");
        const processBtn = document.getElementById("process_button");
        const accountInput = document.getElementById("recipient_account");
        const customerNameDiv = document.getElementById("customer_name");

        // Disable Process Withdrawal button until check is completed
        processBtn.disabled = true;

        checkBtn.addEventListener("click", function() {
            const accountID = accountInput.value.trim();
            if (accountID === "") {
                alert("Please enter an Account ID.");
                return;
            }
            customerNameDiv.textContent = "Checking account...";
            // Use AJAX (Fetch API) to call check_account.php
            fetch("check_account.php?account_id=" + encodeURIComponent(accountID))
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        customerNameDiv.textContent = "Customer Name: " + data.customer_name;
                        processBtn.disabled = false;
                    } else {
                        customerNameDiv.textContent = "Error: " + data.message;
                        processBtn.disabled = true;
                    }
                })
                .catch(err => {
                    console.error("Error fetching account info:", err);
                    customerNameDiv.textContent = "An error occurred. Please try again.";
                    processBtn.disabled = true;
                });
        });
    });
    </script>
</head>

<body class="bg-2 text-color2">
    <!-- Mobile Header with Hamburger Navigation (remains the same as in deposit.php) -->
    <header class="bg-primary text-white flex items-center justify-between px-4 py-3 md:hidden">
        <h1 class="text-xl font-bold">Transfer Funds</h1>
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

    <!-- Desktop Layout: Fixed Sidebar and Scrollable Main -->
    <div class="hidden md:flex md:h-screen">
        <!-- Sidebar for Desktop (fixed) with emoji icons -->
        <aside class="hidden md:block md:w-1/4 bg-primary text-white max-h-screen p-6 sticky top-0">
            <h2 class="text-2xl font-bold mb-6">Customer Dashboard</h2>
            <nav>
                <ul>
                    <li class="mb-4">
                        <a href="customer_dashboard.php" class="hover:text-color3 flex items-center">
                            <span class="mr-2 text-xl">🏠</span> Dashboard
                        </a>
                    </li>
                    <li class="mb-4">
                        <a href="account_summary.php" class="hover:text-color3 flex items-center">
                            <span class="mr-2 text-xl">📊</span> Account Summary
                        </a>
                    </li>
                    <li class="mb-4">
                        <a href="transactions.php" class="hover:text-color3 flex items-center">
                            <span class="mr-2 text-xl">💸</span> Transactions
                        </a>
                    </li>
                    <li class="mb-4">
                        <a href="loans.php" class="hover:text-color3 flex items-center">
                            <span class="mr-2 text-xl">🏦</span> Loans
                        </a>
                    </li>
                    <li class="mb-4">
                        <a href="customer_profile.php" class="hover:text-color3 flex items-center">
                            <span class="mr-2 text-xl">👤</span> Profile
                        </a>
                    </li>
                    <li class="mb-4">
                        <a href="support.php" class="hover:text-color3 flex items-center">
                            <span class="mr-2 text-xl">🛠</span> Support
                        </a>
                    </li>
                    <li class="mb-4">
                        <a href="logout.php" class="hover:text-color3 flex items-center">
                            <span class="mr-2 text-xl">🚪</span> Logout
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content for Desktop (scrollable) -->
        <!-- We use a left margin of 25% so that the main content sits adjacent to the fixed sidebar -->
        <main class="w-full md:w-3/4 p-6">
            <h1 class="text-2xl font-bold text-color3 mb-6">Transfer Funds</h1>
            <?php if(empty($accounts)): ?>
            <div class="bg-white p-6 rounded-lg shadow text-center">
                <p>No active savings account found. Please contact support or check your account summary.</p>
            </div>
            <?php else: ?>
            <div class="bg-white p-6 rounded-lg shadow max-w-lg mx-auto">
                <form action="process_transfer.php" method="POST">
                    <!-- Show a dropdown if multiple accounts exist; if only one, show it as read-only -->
                    <?php if(count($accounts) > 1): ?>
                    <div class="mb-4">
                        <label for="account" class="block text-lg text-color3 mb-2">Select Account</label>
                        <select id="account" name="account_id"
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                            <?php foreach($accounts as $acc): ?>
                            <option value="<?php echo htmlspecialchars($acc['account_id']); ?>">
                                <?php echo "Account #".$acc['account_id']." - Balance: ".number_format($acc['balance'], 2); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php else: ?>
                    <?php $acc = $accounts[0]; ?>
                    <input type="hidden" name="account_id" value="<?php echo htmlspecialchars($acc['account_id']); ?>">
                    <div class="mb-4">
                        <label class="block text-lg text-color3 mb-2">Account</label>
                        <p><?php echo "Account #".$acc['account_id']." - Balance: ".number_format($acc['balance'], 2); ?>
                        </p>
                    </div>
                    <?php endif; ?>
                    <div class="mb-4">
                        <label for="recipient_account" class="block text-lg text-color3 mb-2">Recipient Account
                            Number</label>
                        <input type="text" id="recipient_account" name="recipient_account"
                            placeholder="Enter recipient account number" required
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                    </div>
                    <div id="customer_name" class="mb-4 text-lg text-green-700"></div>
                    <div class="mb-4">
                        <label for="amount" class="block text-lg text-color3 mb-2">Transfer Amount</label>
                        <input type="number" step="0.01" id="amount" name="amount" placeholder="Enter amount" required
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                    </div>
                    <div class="mb-4">
                        <label for="remarks" class="block text-lg text-color3 mb-2">Remarks (Optional)</label>
                        <textarea id="remarks" name="remarks" placeholder="Enter remarks"
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3"></textarea>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button type="button" id="check_button" class="bg-blue-500 text-white px-4 py-2 rounded">
                            Check
                        </button>
                        <!-- Process Withdrawal button; initially disabled until check completes -->
                        <button type="submit" id="process_button" class="bg-green-500 text-white px-4 py-2 rounded"
                            disabled>
                            Transfer Now
                        </button>
                    </div>
                </form>
            </div>
            <?php endif; ?>
        </main>
    </div>

    <!-- Mobile Layout: Transfer Form -->
    <div class="md:hidden p-4">
        <h1 class="text-2xl font-bold text-color3 mb-4">Transfer Funds</h1>
        <?php if(empty($accounts)): ?>
        <div class="bg-white p-6 rounded-lg shadow text-center">
            <p>No active savings account found. Please contact support or check your account summary.</p>
        </div>
        <?php else: ?>
        <div class="bg-white p-6 rounded-lg shadow">
            <form action="process_transfer.php" method="POST">
                <?php if(count($accounts) > 1): ?>
                <div class="mb-4">
                    <label for="account_mobile" class="block text-lg text-color3 mb-2">Select Account</label>
                    <select id="account_mobile" name="account_id"
                        class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                        <?php foreach($accounts as $acc): ?>
                        <option value="<?php echo htmlspecialchars($acc['account_id']); ?>">
                            <?php echo "Account #".$acc['account_id']." - Balance: ".number_format($acc['balance'], 2); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php else: ?>
                <?php $acc = $accounts[0]; ?>
                <input type="hidden" name="account_id" value="<?php echo htmlspecialchars($acc['account_id']); ?>">
                <div class="mb-4">
                    <label class="block text-lg text-color3 mb-2">Account</label>
                    <p><?php echo "Account #".$acc['account_id']." - Balance: ".number_format($acc['balance'], 2); ?>
                    </p>
                </div>
                <?php endif; ?>
                <div class="mb-4">
                    <label for="recipient_account_mobile" class="block text-lg text-color3 mb-2">Recipient Account
                        Number</label>
                    <input type="text" id="recipient_account_mobile" name="recipient_account"
                        placeholder="Enter recipient account number" required
                        class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                </div>
                <div id="customer_name" class="mb-4 text-lg text-green-700"></div>
                <div class="mb-4">
                    <label for="amount_mobile" class="block text-lg text-color3 mb-2">Transfer Amount</label>
                    <input type="number" step="0.01" id="amount_mobile" name="amount" placeholder="Enter amount"
                        required class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                </div>
                <div class="mb-4">
                    <label for="remarks_mobile" class="block text-lg text-color3 mb-2">Remarks (Optional)</label>
                    <textarea id="remarks_mobile" name="remarks" placeholder="Enter remarks"
                        class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3"></textarea>
                </div>
                <div class="flex items-center space-x-4">
                    <button type="button" id="check_button" class="bg-blue-500 text-white px-4 py-2 rounded">
                        Check
                    </button>
                    <!-- Process Withdrawal button; initially disabled until check completes -->
                    <button type="submit" id="process_button" class="bg-green-500 text-white px-4 py-2 rounded"
                        disabled>
                        Transfer Now
                    </button>

                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>
</body>

</html>