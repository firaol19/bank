<?php
session_start();

// Ensure only an Employee can access this page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Employee') {
    header("Location: login.php");
    exit();
}

require 'db_connection.php'; // This file initializes the $conn variable.
$user_id = $_SESSION['user_id'];

// Retrieve employee details from the users table.
$query = "SELECT user_id, full_name, username, email, phone FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    echo "<script>alert('Profile not found.'); window.location.href='employee_dashboard.php';</script>";
    exit();
}
$user = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>My Profile | Credit & Saving System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet" />
    <link href="styles.css" rel="stylesheet" />
</head>

<body class="bg-2 text-color2">
    <div class="flex flex-col md:flex-row">
        <!-- Sidebar Navigation -->
        <aside class="w-full md:w-1/4 bg-primary text-white p-6 sticky top-0">
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
                    <li class="mb-4"><a href="withdrawal.php" class="hover:text-color3">💰 Withdrawal</a>
                    <li class="mb-4">
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

        <!-- Main Content: Employee Profile -->
        <main class="w-full md:w-3/4 p-6">
            <h1 class="text-3xl font-bold mb-6 text-color3">My Profile</h1>
            <div class="bg-white shadow-md rounded-lg p-6 max-w-xl mx-auto">
                <form action="update_profile.php" method="POST">
                    <div class="mb-4">
                        <label for="full_name" class="block text-lg text-color3 mb-2">Full Name</label>
                        <input type="text" id="full_name" name="full_name"
                            value="<?php echo htmlspecialchars($user['full_name']); ?>"
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3"
                            required>
                    </div>
                    <div class="mb-4">
                        <label for="username" class="block text-lg text-color3 mb-2">Username</label>
                        <input type="text" id="username" name="username"
                            value="<?php echo htmlspecialchars($user['username']); ?>"
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3"
                            readonly>
                    </div>
                    <div class="mb-4">
                        <label for="email" class="block text-lg text-color3 mb-2">Email</label>
                        <input type="email" id="email" name="email"
                            value="<?php echo htmlspecialchars($user['email']); ?>"
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3"
                            required>
                    </div>
                    <div class="mb-4">
                        <label for="phone" class="block text-lg text-color3 mb-2">Phone</label>
                        <input type="text" id="phone" name="phone"
                            value="<?php echo htmlspecialchars($user['phone']); ?>"
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                    </div>
                    <div class="flex flex-col md:flex-row md:space-x-4">
                        <button type="submit"
                            class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-color3 transition-all mb-4 md:mb-0">
                            Update Profile
                        </button>
                        <a href="change_password.php"
                            class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-all">
                            Change Password
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>

</html>