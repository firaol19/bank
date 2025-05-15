<?php
session_start();

// Ensure only an Employee can access this page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Employee') {
    header("Location: login.php");
    exit();
}

require 'db_connection.php'; // This file should initialize the $conn variable.

if (!isset($_GET['task_id']) || !is_numeric($_GET['task_id'])) {
    echo "<script>alert('Invalid task ID.'); window.location.href='employee_tasks.php';</script>";
    exit();
}

$task_id = intval($_GET['task_id']);

// Prepare a statement to ensure the task belongs to the logged-in employee.
$query = "SELECT task_id, task_description, due_date, status 
          FROM tasks 
          WHERE task_id = ? AND assigned_to = ?";
$stmt  = $conn->prepare($query);
$stmt->bind_param("ii", $task_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<script>alert('Task not found.'); window.location.href='employee_tasks.php';</script>";
    exit();
}

$task = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Task | Credit & Saving System</title>
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
            <h1 class="text-3xl font-bold mb-6 text-color3">Task Details</h1>

            <div class="bg-white shadow-md rounded-lg p-6 max-w-lg">
                <p class="mb-2"><span class="font-semibold">Task ID:</span>
                    <?php echo htmlspecialchars($task['task_id']); ?></p>
                <p class="mb-2"><span class="font-semibold">Description:</span>
                    <?php echo htmlspecialchars($task['task_description']); ?></p>
                <p class="mb-2"><span class="font-semibold">Due Date:</span>
                    <?php echo htmlspecialchars($task['due_date']); ?></p>
                <p class="mb-2"><span class="font-semibold">Status:</span>
                    <?php echo htmlspecialchars($task['status']); ?></p>
            </div>

            <div class="mt-6">
                <a href="employee_tasks.php"
                    class="bg-primary text-white px-6 py-3 rounded hover:bg-color3 transition-all">Back to Tasks</a>
                <?php if($task['status'] !== 'Completed'): ?>
                <a href="complete_task.php?task_id=<?php echo $task['task_id']; ?>"
                    class="bg-green-600 text-white px-6 py-3 rounded hover:bg-green-700 transition-all ml-4">Mark as
                    Completed</a>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>

</html>