<?php
session_start();

// Ensure only authenticated customers can access this page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Customer') {
    header("Location: login.php");
    exit();
}

require 'db_connection.php'; // Initializes the $conn variable (MySQLi connection)
$customer_id = $_SESSION['user_id'];

// (Optional) Retrieve customer details. For example, you might want to display the customer's name.
// Here we fetch from the Customers table:
$stmt = $conn->prepare("SELECT name, account_number, balance FROM Customers WHERE customer_id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Apply for Loan | Bank System</title>
    <!-- Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet" />
    <!-- Custom CSS as defined (bg-2, text-color2, bg-primary, text-color3) -->
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
    <!-- Mobile Header with Hamburger Navigation -->
    <header class="bg-primary text-white flex items-center justify-between px-4 py-3 md:hidden">
        <h1 class="text-xl font-bold">Apply for Loan</h1>
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

    <!-- Desktop Layout: Fixed Sidebar & Scrollable Main Content -->
    <div class="hidden md:flex md:h-screen">
        <!-- Sidebar (Fixed) with Emoji Navigation icons -->
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

        <!-- Main Content (Desktop) -->
        <!-- We provide a left margin to accommodate the fixed sidebar -->
        <main class="w-full md:w-3/4 p-6">
            <h1 class="text-2xl font-bold text-color3 mb-6 mx-auto">Apply for a Loan</h1>
            <div class="bg-white p-6 rounded-lg shadow max-w-lg mx-auto">
                <form action="process_apply_loan.php" method="POST">
                    <div class="mb-4">
                        <label for="loan_amount" class="block text-lg text-color3 mb-2">Loan Amount</label>
                        <input type="number" step="0.01" id="loan_amount" name="loan_amount"
                            placeholder="Enter loan amount" required
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                    </div>
                    <div class="mb-4">
                        <label for="duration" class="block text-lg text-color3 mb-2">Duration (Months)</label>
                        <input type="number" id="duration" name="duration" placeholder="Enter loan duration in months"
                            required
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                    </div>
                    <div class="mb-4">
                        <label for="remarks" class="block text-lg text-color3 mb-2">Remarks (Optional)</label>
                        <textarea id="remarks" name="remarks" placeholder="Enter any remarks or loan purpose"
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3"></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit"
                            class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-color3 transition-all">Apply
                            Now</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <!-- Mobile Layout: Apply Loan Form -->
    <div class="md:hidden p-4">
        <h1 class="text-2xl font-bold text-color3 mb-4">Apply for a Loan</h1>
        <div class="bg-white p-6 rounded-lg shadow">
            <form action="process_apply_loan.php" method="POST">
                <div class="mb-4">
                    <label for="loan_amount_mobile" class="block text-lg text-color3 mb-2">Loan Amount <span
                            class="text-lg">
                            birr</span></label>
                    <input type="number" step="0.01" id="loan_amount_mobile" name="loan_amount"
                        placeholder="Enter loan amount" required
                        class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                </div>
                <div class="mb-4">
                    <label for="duration_mobile" class="block text-lg text-color3 mb-2">Duration (Months)</label>
                    <input type="number" id="duration_mobile" name="duration"
                        placeholder="Enter loan duration in months" required
                        class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                </div>
                <div class="mb-4">
                    <label for="remarks_mobile" class="block text-lg text-color3 mb-2">Remarks (Optional)</label>
                    <textarea id="remarks_mobile" name="remarks" placeholder="Enter any remarks or loan purpose"
                        class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3"></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="submit"
                        class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-color3 transition-all">Apply
                        Now</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>