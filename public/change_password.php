<?php
session_start();

// Ensure only an Employee can access this page.
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
    <title>Change Password | Credit & Saving System</title>
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

        <!-- Main Content: Change Password -->
        <main class="w-full md:w-3/4 p-6 pb-60">
            <h1 class="text-3xl font-bold mb-6 text-color3">Change Password</h1>
            <div class="bg-white shadow-md rounded-lg p-6 max-w-lg mx-auto">
                <form action="process_change_password.php" method="POST">
                    <div class="mb-4">
                        <label for="current_password" class="block text-lg text-color3 mb-2">Current Password</label>
                        <input type="password" id="current_password" name="current_password"
                            placeholder="Enter your current password" required
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                    </div>
                    <div class="mb-4">
                        <label for="new_password" class="block text-lg text-color3 mb-2">New Password</label>
                        <input type="password" id="new_password" name="new_password"
                            placeholder="Enter your new password" required
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                    </div>
                    <div class="mb-4">
                        <label for="confirm_password" class="block text-lg text-color3 mb-2">Confirm New
                            Password</label>
                        <input type="password" id="confirm_password" name="confirm_password"
                            placeholder="Confirm your new password" required
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                    </div>
                    <div>
                        <button type="submit"
                            class="bg-primary text-white w-full px-6 py-3 rounded-lg hover:bg-color3 transition-all">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>

</html>