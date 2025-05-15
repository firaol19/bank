<?php
session_start();

// Ensure only authenticated customers can access this page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Customer') {
    header("Location: login.php");
    exit();
}

require 'db_connection.php'; // Initializes $conn (MySQLi connection)
$customer_id = $_SESSION['user_id'];

// Retrieve the customer's profile details from the Customers table.
$stmt = $conn->prepare("SELECT customer_id, name, age, salary, address, department, phone_number, account_number, balance, registration_date FROM Customers WHERE customer_id = ?");
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
    <title>Customer Profile | Bank System</title>
    <!-- Tailwind CSS CDN and custom styles -->
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
    <!-- Mobile Header with Hamburger Navigation -->
    <header class="bg-primary text-white flex items-center justify-between px-4 py-3 md:hidden">
        <h1 class="text-xl font-bold">My Profile</h1>
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
                    <li class="mb-4"><a href="financial_statement_report.php" class="hover:text-color3">🛠
                            Financial Statement
                            S</a>
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
        <!-- We use a left margin of approximately 25% to allow space for the fixed sidebar -->
        <main class="w-full md:w-3/4 p-6">
            <h1 class="text-2xl font-bold text-color3 mb-6">My Profile</h1>
            <?php if (!$customer): ?>
            <div class="bg-white p-6 rounded-lg shadow text-center">
                <p>Profile details not found. Please contact support.</p>
            </div>
            <?php else: ?>
            <div class="bg-white p-6 rounded-lg shadow max-w-2xl mx-auto">
                <div class="mb-4">
                    <h2 class="text-xl font-bold text-color3">Personal Information</h2>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($customer['name']?? ''); ?></p>
                    <p><strong>Age:</strong> <?php echo htmlspecialchars($customer['age'] ?? ''); ?></p>
                    <p><strong>Address:</strong> <?php echo nl2br(htmlspecialchars($customer['address'] ?? '')); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($customer['phone_number']?? ''); ?></p>
                </div>
                <div class="mb-4">
                    <h2 class="text-xl font-bold text-color3">Account Information</h2>
                    <p><strong>Account Number:</strong>
                        <?php echo htmlspecialchars($customer['account_number']?? ''); ?></p>

                    <p><strong>Registered On:</strong>
                        <?php echo htmlspecialchars($customer['registration_date']?? ''); ?>
                    </p>
                </div>
                <div class="flex justify-end">
                    <a href="update_customer_profile.php"
                        class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-color3 transition-all">
                        Edit Profile
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>

    <!-- Mobile Layout: Customer Profile -->
    <div class="md:hidden p-4">
        <h1 class="text-2xl font-bold text-color3 mb-4">My Profile</h1>
        <?php if (!$customer): ?>
        <div class="bg-white p-6 rounded-lg shadow text-center">
            <p>Profile details not found. Please contact support.</p>
        </div>
        <?php else: ?>
        <div class="bg-white p-6 rounded-lg shadow mb-4">
            <h2 class="text-xl font-bold text-color3 mb-2">Personal Information</h2>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($customer['name']?? ''); ?></p>
            <p><strong>Age:</strong> <?php echo htmlspecialchars($customer['age']?? ''); ?></p>
            <p><strong>Address:</strong> <?php echo nl2br(htmlspecialchars($customer['address']?? '')); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($customer['phone_number']?? ''); ?></p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow mb-4">
            <h2 class="text-xl font-bold text-color3 mb-2">Account Information</h2>
            <p><strong>Account Number:</strong> <?php echo htmlspecialchars($customer['account_number']?? ''); ?></p>
            <p><strong>Overall Balance:</strong> <?php echo number_format($customer['balance']?? '', 2); ?><span
                    class="text-lg">
                    birr</span></p>
            <p><strong>Registered On:</strong> <?php echo htmlspecialchars($customer['registration_date']?? ''); ?></p>
        </div>
        <div class="flex justify-end">
            <a href="update_customer_profile.php"
                class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-color3 transition-all">
                Edit Profile
            </a>
        </div>
        <?php endif; ?>
    </div>
</body>

</html>