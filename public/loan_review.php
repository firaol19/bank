<?php
session_start();

// Ensure only an Employee can access this page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Employee') {
    header("Location: login.php");
    exit();
}

require 'db_connection.php'; // Initializes the $conn variable.

// Retrieve pending loans for review.
$pendingLoans = [];
$query = "SELECT loan_id, customer_id, loan_amount, request_date 
          FROM loans 
          WHERE status = 'Pending' 
          ORDER BY request_date ASC";
if ($result = $conn->query($query)) {
    while ($row = $result->fetch_assoc()) {
        $pendingLoans[] = $row;
    }
    $result->free();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Review | Credit & Saving System</title>
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
                    <li class="mb-4"><a href="employee_dashboard.php" class="hover:text-color3">🏠 Overview</a></li>
                    <li class="mb-4"><a href="employee_tasks.php" class="hover:text-color3">📝 My Tasks</a></li>
                    <li class="mb-4"><a href="loan_review.php" class="hover:text-color3">🔍 Loan Review</a></li>
                    <li class="mb-4"><a href="employee_transactions.php" class="hover:text-color3">💰 Transaction
                            History</a></li>
                    <li class="mb-4"><a href="withdrawal.php" class="hover:text-color3">💰 Withdrawal</a>
                    <li class="mb-4"><a href="employee_deposit.php" class="hover:text-color3">💰 Deposit</a>
                    <li class="mb-4"><a href="customer_support.php" class="hover:text-color3">👥 Customer Support</a>
                    </li>
                    <li class="py-1"><a href="emi_calculator.php" class="hover:text-color3">🧮 EMI Calculator</a></li>
                    <li class="mb-4"><a href="employee_profile.php" class="hover:text-color3">👤 My Profile</a></li>
                    <li class="mb-4"><a href="logout.php" class="hover:text-color3">🚪 Logout</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content: Loan Review -->
        <main class="flex-grow p-6 h-screen w-[75%] overflow-auto">
            <h1 class="text-3xl font-bold mb-6 text-color3">Loan Review</h1>

            <?php if(count($pendingLoans) > 0): ?>
            <div class="bg-white shadow-md rounded-lg overflow-auto">
                <table class="w-full">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="px-4 py-2">Loan ID</th>
                            <th class="px-4 py-2">Customer ID</th>
                            <th class="px-4 py-2">Loan Amount</th>
                            <th class="px-4 py-2">Request Date</th>
                            <th class="px-4 py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pendingLoans as $loan): ?>
                        <tr class="border-b hover:bg-gray-100">
                            <td class="px-4 py-2"><?php echo htmlspecialchars($loan['loan_id']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($loan['customer_id']); ?></td>
                            <td class="px-4 py-2"><?php echo number_format($loan['loan_amount'], 2); ?><span
                                    class="text-lg">
                                    birr</span></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($loan['request_date']); ?></td>
                            <td class="px-4 py-2">
                                <a href="review_loan.php?loan_id=<?php echo $loan['loan_id']; ?>"
                                    class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Review</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="p-6 text-center bg-white shadow-md rounded-lg">
                <p>No pending loans for review.</p>
            </div>
            <?php endif; ?>
        </main>
    </div>
</body>

</html>