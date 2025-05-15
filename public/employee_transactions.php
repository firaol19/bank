<?php
session_start();

// Ensure only an Employee can access this page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Employee') {
    header("Location: login.php");
    exit();
}

require 'db_connection.php'; // This file should initialize the $conn variable.
$employee_id = $_SESSION['user_id'];

// Retrieve transactions processed by the logged-in employee.
$transactions   = [];
$totalAmount    = 0;
$transactionCount = 0;

$query = "SELECT transaction_id, account_id, transaction_type, amount, transaction_date 
          FROM transactions 
          WHERE processed_by = ? 
          ORDER BY transaction_date DESC";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Database error: " . $conn->error);
}
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
    $totalAmount    += $row['amount'];
}
$stmt->close();
$transactionCount = count($transactions);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History | Credit & Saving System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>

<body class="bg-2 text-color2">
    <div class="flex">
        <!-- Sidebar Navigation -->
        <aside class="w-[25%] bg-primary text-white h-screen p-6 sticky">
            <h2 class="text-2xl font-bold mb-6">Employee Dashboard</h2>
            <nav>
                <ul>
                    <li class="mb-4">
                        <a href="employee_dashboard.php" class="hover:text-color3">🏠 Overview</a>
                    </li>
                    <li class="mb-4">
                        <a href="employee_tasks.php" class="hover:text-color3">📝 My Tasks</a>
                    </li>
                    <li class="mb-4">
                        <a href="loan_review.php" class="hover:text-color3">🔍 Loan Review</a>
                    </li>
                    <li class="mb-4">
                        <a href="employee_transactions.php" class="hover:text-color3">💰 Transaction History</a>
                    </li>
                    <li class="mb-4"><a href="employee_deposit.php" class="hover:text-color3">💰 Deposit</a>
                    <li class="mb-4">
                    <li class="mb-4"><a href="withdrawal.php" class="hover:text-color3">💰 Withdrawal</a>

                    </li>
                    <li>
                        <a href="customer_support.php" class="hover:text-color3">👥 Customer Support</a>
                    </li>
                    <li class="py-1"><a href="emi_calculator.php" class="hover:text-color3">🧮 EMI Calculator</a></li>
                    <li class="mb-4">
                        <a href="employee_profile.php" class="hover:text-color3">👤 My Profile</a>
                    </li>
                    <li class="mb-4">
                        <a href="logout.php" class="hover:text-color3">🚪 Logout</a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content: Transaction History -->
        <main class="flex-grow p-6 h-screen w-[75%] overflow-auto">
            <h1 class="text-3xl font-bold mb-6 text-color3">Transaction History</h1>

            <div class="mb-6">
                <p class="text-lg">
                    Total Transactions Processed: <span class="font-semibold"><?php echo $transactionCount; ?></span>
                </p>
                <p class="text-lg">
                    Total Amount Processed: <span
                        class="font-semibold"><?php echo number_format($totalAmount, 2); ?><span class="text-sm">
                            birr<span></span>
                </p>
            </div>

            <?php if ($transactionCount > 0): ?>
            <div class="bg-white shadow-md rounded-lg overflow-auto">
                <table class="w-full">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="px-4 py-2">Transaction ID</th>
                            <th class="px-4 py-2">Account ID</th>
                            <th class="px-4 py-2">Type</th>
                            <th class="px-4 py-2">Amount</th>
                            <th class="px-4 py-2">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $trans): ?>
                        <tr class="border-b hover:bg-gray-100">
                            <td class="px-4 py-2"><?php echo htmlspecialchars($trans['transaction_id']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($trans['account_id']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($trans['transaction_type']); ?></td>
                            <td class="px-4 py-2"><?php echo number_format($trans['amount'], 2); ?><span
                                    class="text-lg">
                                    birr</span></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($trans['transaction_date']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="bg-white shadow-md rounded-lg p-6 text-center">
                <p>No transactions found.</p>
            </div>
            <?php endif; ?>
        </main>
    </div>
</body>

</html>