<?php
session_start();

// Ensure only an Employee can access this page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Employee') {
    header("Location: login.php");
    exit();
}

require 'db_connection.php'; // This file should initialize the $conn variable.
$employee_id = $_SESSION['user_id'];

// 1. Assigned Tasks and Completed Tasks
$assignedTasks = [];
$completedTasks = 0;
// Check if the tasks table exists
$resultTaskCheck = $conn->query("SHOW TABLES LIKE 'tasks'");
if ($resultTaskCheck && $resultTaskCheck->num_rows > 0) {
    // Fetch assigned tasks for this employee.
    $queryTasks = "SELECT task_id, task_description, due_date, status 
                   FROM tasks 
                   WHERE assigned_to = $employee_id 
                   ORDER BY due_date ASC";
    if ($result = $conn->query($queryTasks)) {
        while ($row = $result->fetch_assoc()) {
            $assignedTasks[] = $row;
        }
        $result->free();
    }
    // Count completed tasks.
    $queryCompletedTasks = "SELECT COUNT(*) AS completedTasks 
                            FROM tasks 
                            WHERE assigned_to = $employee_id AND status = 'Completed'";
    if ($result = $conn->query($queryCompletedTasks)) {
        if ($row = $result->fetch_assoc()) {
            $completedTasks = $row['completedTasks'];
        }
        $result->free();
    }
} // Else, assignedTasks remains empty and completedTasks is 0.

// 2. Employee Transactions
$employeeTransactions = [];
$totalTransactionsValue = 0;
$queryTransactions = "SELECT transaction_id, account_id, transaction_type, amount, transaction_date 
                      FROM transactions 
                      WHERE processed_by = $employee_id 
                      ORDER BY transaction_date DESC";
if ($result = $conn->query($queryTransactions)) {
    while ($row = $result->fetch_assoc()) {
        $employeeTransactions[] = $row;
        $totalTransactionsValue += $row['amount'];
    }
    $result->free();
}
$transactionCount = count($employeeTransactions);

// 3. Pending Loan Reviews
$pendingLoanReviews = [];
// Since there is no 'preliminary_review' column, simply fetch loans with status 'Pending'
$queryLoanReviews = "SELECT loan_id, customer_id, loan_amount, request_date 
                     FROM loans 
                     WHERE status = 'Pending' 
                     ORDER BY request_date ASC";
if ($result = $conn->query($queryLoanReviews)) {
    while ($row = $result->fetch_assoc()) {
        $pendingLoanReviews[] = $row;
    }
    $result->free();
}
$pendingLoanReviewsCount = count($pendingLoanReviews);

// 4. Notifications – Retrieve from the notifications table for Employee role.
$notifications = [];
$queryNotifications = "SELECT notification_id, message, created_at 
                       FROM notifications 
                       WHERE target_role = 'Employee' 
                       ORDER BY created_at DESC";
if ($result = $conn->query($queryNotifications)) {
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
    $result->free();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard | Credit & Saving System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>

<body class="bg-3 text-color2">
    <div class="flex">
        <!-- Sidebar Navigation -->
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

        <!-- Main Content -->
        <main class="flex-grow p-6 h-screen w-[75%] overflow-auto">
            <h1 class="text-3xl font-bold mb-6 text-color3">Welcome,
                <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>

            <!-- Summary Cards Section -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h3 class="text-xl font-semibold">Assigned Tasks</h3>
                    <p class="text-3xl font-bold"><?php echo count($assignedTasks); ?></p>
                </div>
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h3 class="text-xl font-semibold">Completed Tasks</h3>
                    <p class="text-3xl font-bold"><?php echo $completedTasks; ?></p>
                </div>
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h3 class="text-xl font-semibold">Transactions Processed</h3>
                    <p class="text-3xl font-bold"><?php echo $transactionCount; ?></p>
                    <small>Total: <?php echo number_format($totalTransactionsValue, 2); ?><span class="text-sm">
                            birr<span></small>
                </div>
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h3 class="text-xl font-semibold">Pending Loan Reviews</h3>
                    <p class="text-3xl font-bold"><?php echo $pendingLoanReviewsCount; ?></p>
                </div>
            </div>

            <!-- My Tasks Section -->
            <section class="mb-10">
                <h2 class="text-2xl font-bold mb-4 text-color3">My Tasks</h2>
                <?php if(count($assignedTasks) > 0): ?>
                <div class="bg-white shadow-md rounded-lg overflow-auto">
                    <table class="w-full">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th class="px-4 py-2">Task ID</th>
                                <th class="px-4 py-2">Description</th>
                                <th class="px-4 py-2">Due Date</th>
                                <th class="px-4 py-2">Status</th>
                                <th class="px-4 py-2">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($assignedTasks as $task): ?>
                            <tr class="border-b hover:bg-gray-100">
                                <td class="px-4 py-2"><?php echo htmlspecialchars($task['task_id']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($task['task_description']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($task['due_date']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($task['status']); ?></td>
                                <td class="px-4 py-2">
                                    <a href="view_task.php?task_id=<?php echo $task['task_id']; ?>"
                                        class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">View</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="p-6 text-center bg-white shadow-md rounded-lg">No tasks assigned.</div>
                <?php endif; ?>
            </section>

            <!-- Preliminary Loan Review Section -->
            <section class="mb-10">
                <h2 class="text-2xl font-bold mb-4 text-color3">Preliminary Loan Review</h2>
                <?php if(count($pendingLoanReviews) > 0): ?>
                <div class="bg-white shadow-md rounded-lg overflow-auto">
                    <table class="w-full">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th class="px-4 py-2">Loan ID</th>
                                <th class="px-4 py-2">Customer ID</th>
                                <th class="px-4 py-2">Amount</th>
                                <th class="px-4 py-2">Request Date</th>
                                <th class="px-4 py-2">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($pendingLoanReviews as $loan): ?>
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
                <div class="p-6 text-center bg-white shadow-md rounded-lg">No pending loan reviews.</div>
                <?php endif; ?>
            </section>

            <!-- Transaction History Section -->
            <section class="mb-10">
                <h2 class="text-2xl font-bold mb-4 text-color3">Transaction History</h2>
                <?php if(count($employeeTransactions) > 0): ?>
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
                            <?php foreach($employeeTransactions as $trans): ?>
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
                <div class="p-6 text-center bg-white shadow-md rounded-lg">No transactions found.</div>
                <?php endif; ?>
            </section>

            <!-- Customer Support Section -->
            <section class="mb-10">
                <h2 class="text-2xl font-bold mb-4 text-color3">Customer Support</h2>
                <div class="bg-white shadow-md rounded-lg p-6">
                    <p class="mb-4">Search for customer accounts to view or update information.</p>
                    <form action="search_customer.php" method="GET" class="flex">
                        <input type="text" name="query" placeholder="Enter customer name or ID"
                            class="flex-grow p-3 border rounded-l-lg focus:outline-none">
                        <button type="submit"
                            class="bg-primary text-white px-4 py-3 rounded-r-lg hover:bg-color3 transition-all">Search</button>
                    </form>
                </div>
            </section>

            <!-- Notifications Section -->
            <section class="mb-10">
                <h2 class="text-2xl font-bold mb-4 text-color3">Notifications</h2>
                <?php if(count($notifications) > 0): ?>
                <div class="bg-white shadow-md rounded-lg p-6">
                    <ul class="list-disc pl-5">
                        <?php foreach($notifications as $note): ?>
                        <li class="mb-2">
                            <?php echo htmlspecialchars($note['message']); ?>
                            <small
                                class="block text-gray-500"><?php echo htmlspecialchars($note['created_at']); ?></small>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php else: ?>
                <div class="p-6 text-center bg-white shadow-md rounded-lg">No notifications found.</div>
                <?php endif; ?>
            </section>

        </main>
    </div>
</body>

</html>