<?php
session_start();

// Ensure only an Employee can access this page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Employee') {
    header("Location: login.php");
    exit();
}

require 'db_connection.php'; // The file to set up the $conn variable.

$searchTerm = "";
$results = [];

// Check if a search query is provided.
if (isset($_GET['query']) && trim($_GET['query']) !== "") {
    $searchTerm = trim($_GET['query']);
    $likeQuery = "%" . $searchTerm . "%";

    // Prepare a query to search for customers matching the search term.
    $query = "SELECT user_id, full_name, username, email, phone, created_at 
              FROM users 
              WHERE role = 'Customer' 
                AND (full_name LIKE ? OR username LIKE ? OR email LIKE ? OR phone LIKE ?)
              ORDER BY created_at DESC";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Database error: " . $conn->error);
    }
    $stmt->bind_param("ssss", $likeQuery, $likeQuery, $likeQuery, $likeQuery);
    $stmt->execute();
    $resultObj = $stmt->get_result();
    while ($row = $resultObj->fetch_assoc()) {
        $results[] = $row;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Customer Accounts | Credit & Saving System</title>
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
                    <li class="mb-4"><a href="loan_review.php" class="hover:text-color3">🔍 Preliminary Loan Review</a>
                    </li>
                    <li class="mb-4"><a href="employee_transactions.php" class="hover:text-color3">💰 Transaction
                            History</a></li>
                    <li class="mb-4"><a href="customer_support.php" class="hover:text-color3">👥 Customer Support</a>
                    </li>
                    <li class="mb-4"><a href="employee_profile.php" class="hover:text-color3">👤 My Profile</a></li>
                    <li class="mb-4"><a href="logout.php" class="hover:text-color3">🚪 Logout</a></li>
                </ul>
            </nav>
        </aside>
        <!-- Main Content -->
        <main class="flex-grow p-6 h-screen w-[75%] overflow-auto">
            <h1 class="text-3xl font-bold mb-6 text-color3">Search Customer Accounts</h1>

            <!-- If no search query is provided -->
            <?php if ($searchTerm === ""): ?>
            <div class="bg-white shadow-md rounded-lg p-6">
                <p class="text-lg">No search query provided. Please use the search box on the Customer Support page.</p>
                <a href="customer_support.php" class="text-blue-600 hover:underline">Go back</a>
            </div>
            <?php else: ?>
            <div class="mb-6">
                <p class="text-lg">Search results for: <span
                        class="font-semibold"><?php echo htmlspecialchars($searchTerm); ?></span></p>
            </div>
            <?php if (count($results) > 0): ?>
            <div class="bg-white shadow-md rounded-lg overflow-auto">
                <table class="w-full">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="px-4 py-2">Customer ID</th>
                            <th class="px-4 py-2">Full Name</th>
                            <th class="px-4 py-2">Username</th>
                            <th class="px-4 py-2">Email</th>
                            <th class="px-4 py-2">Phone</th>
                            <th class="px-4 py-2">Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $customer): ?>
                        <tr class="border-b hover:bg-gray-100">
                            <td class="px-4 py-2"><?php echo htmlspecialchars($customer['user_id']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($customer['full_name']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($customer['username']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($customer['email']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($customer['phone']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($customer['created_at']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="bg-white shadow-md rounded-lg p-6">
                <p class="text-lg">No customer accounts found matching your search criteria.</p>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </main>
    </div>
</body>

</html>