<?php
session_start();

// Ensure only an Employee can access this page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Employee') {
    header("Location: login.php");
    exit();
}

require 'db_connection.php'; // This file should initialize the $conn variable.

// Validate the loan_id parameter
if (!isset($_GET['loan_id']) || !is_numeric($_GET['loan_id'])) {
    echo "<script>alert('Invalid loan ID.'); window.location.href='loan_review.php';</script>";
    exit();
}

$loan_id = intval($_GET['loan_id']);

// Retrieve loan details from the loans table and join with the users table to get the customer name.
$query = "SELECT l.loan_id, l.customer_id, l.loan_amount, l.interest_rate, l.duration, l.status, l.request_date, u.full_name AS customer_name
          FROM loans l
          LEFT JOIN users u ON l.customer_id = u.user_id
          WHERE l.loan_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $loan_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<script>alert('Loan not found.'); window.location.href='loan_review.php';</script>";
    exit();
}

$loan = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Loan | Credit & Saving System</title>
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
                        <a href="loan_review.php" class="hover:text-color3">🔍 Preliminary Loan Review</a>
                    </li>
                    <li class="mb-4">
                        <a href="employee_transactions.php" class="hover:text-color3">💰 Transaction History</a>
                    </li>
                    <li class="mb-4">
                        <a href="customer_support.php" class="hover:text-color3">👥 Customer Support</a>
                    </li>
                    <li class="mb-4">
                        <a href="employee_profile.php" class="hover:text-color3">👤 My Profile</a>
                    </li>
                    <li class="mb-4">
                        <a href="logout.php" class="hover:text-color3">🚪 Logout</a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-grow p-6 h-screen w-[75%] overflow-auto">
            <h1 class="text-3xl font-bold mb-6 text-color3">Review Loan Application</h1>

            <!-- Loan Details Card -->
            <div class="bg-white shadow-md rounded-lg p-6 max-w-lg mb-6">
                <h2 class="text-xl font-semibold mb-4">Loan Details</h2>
                <p><strong>Loan ID:</strong> <?php echo htmlspecialchars($loan['loan_id']); ?></p>
                <p><strong>Customer ID:</strong> <?php echo htmlspecialchars($loan['customer_id']); ?></p>
                <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($loan['customer_name']); ?></p>
                <p><strong>Loan Amount:</strong> <?php echo number_format($loan['loan_amount'], 2); ?><span
                        class="text-lg">
                        birr</span></p>
                <p><strong>Interest Rate:</strong> <?php echo htmlspecialchars($loan['interest_rate']); ?>%</p>
                <p><strong>Duration:</strong> <?php echo htmlspecialchars($loan['duration']); ?> months</p>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($loan['status']); ?></p>
                <p><strong>Request Date:</strong> <?php echo htmlspecialchars($loan['request_date']); ?></p>
            </div>

            <!-- Review Form -->
            <div class="bg-white shadow-md rounded-lg p-6 max-w-lg">
                <h2 class="text-xl font-semibold mb-4">Preliminary Review</h2>
                <form action="process_review_loan.php" method="POST">
                    <input type="hidden" name="loan_id" value="<?php echo htmlspecialchars($loan['loan_id']); ?>">
                    <div class="mb-4">
                        <label for="review_remarks" class="block text-lg text-color3">Review Remarks</label>
                        <textarea name="review_remarks" id="review_remarks" rows="4"
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3"
                            placeholder="Enter your review remarks here" required></textarea>
                    </div>
                    <button type="submit"
                        class="bg-green-600 text-white px-6 py-3 rounded hover:bg-green-700 transition-all">Submit
                        Review</button>
                    <a href="loan_review.php"
                        class="bg-gray-600 text-white px-6 py-3 rounded hover:bg-gray-700 transition-all ml-4">Cancel</a>
                </form>
            </div>
        </main>
    </div>
</body>

</html>