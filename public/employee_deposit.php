<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Employee') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Employee Deposit</title>
    <!-- Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet" />
    <link href="styles.css" rel="stylesheet">
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
    // When the page loads, attach a click handler for the Check button.
    document.addEventListener("DOMContentLoaded", function() {
        const checkBtn = document.getElementById("check_button");
        const processBtn = document.getElementById("process_button");
        const accountInput = document.getElementById("account_id");
        const customerNameDiv = document.getElementById("customer_name");

        // Initially disable the Process Deposit button
        processBtn.disabled = true;

        checkBtn.addEventListener("click", function() {
            const accountID = accountInput.value.trim();
            if (accountID === "") {
                alert("Please enter an Account ID.");
                return;
            }
            // Show a temporary message while checking
            customerNameDiv.textContent = "Checking account...";
            // Perform an AJAX GET request to check_account.php
            fetch("check_account.php?account_id=" + encodeURIComponent(accountID))
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Display the customer name and enable the deposit button.
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


    <div class="flex">
        <aside class="w-[25%] bg-primary text-white h-screen p-6 sticky top-0">
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

        <!-- Main Content -->
        <main class="flex-grow p-6 h-screen w-[75%] overflow-auto">
            <h1 class="text-xl font-bold text-color3">Employee Deposit</h1>
            <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow">

                <form action="process_deposit_employee.php" method="POST">
                    <div class="mb-4">
                        <label for="account_id" class="block text-lg text-color3 mb-2">Customer Account ID</label>
                        <input type="text" id="account_id" name="account_id" placeholder="Enter Customer Account ID"
                            required class="w-full p-3 border rounded-lg" />
                    </div>
                    <!-- The customer name will be displayed here after checking -->
                    <div id="customer_name" class="mb-4 text-lg text-green-700"></div>
                    <div class="mb-4">
                        <label for="amount" class="block text-lg text-color3 mb-2">Deposit Amount (ብር)</label>
                        <input type="number" step="0.01" id="amount" name="amount" placeholder="Enter deposit amount"
                            required class="w-full p-3 border rounded-lg" />
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Check button (does not submit the form) -->
                        <button type="button" id="check_button" class="bg-blue-500 text-white px-4 py-2 rounded">
                            Check
                        </button>
                        <!-- Process Deposit button (submits form), initially disabled -->
                        <button type="submit" id="process_button" class="bg-green-500 text-white px-4 py-2 rounded"
                            disabled>
                            Process Deposit
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>

</html>