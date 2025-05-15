<?php
session_start();

// Ensure user is logged in (or add any role-based checks as needed).
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'db_connection.php'; // Initialize $conn

// Check if task_id is provided in the URL (GET parameter).
if (!isset($_GET['task_id']) || empty($_GET['task_id'])) {
    echo "<script>alert('Invalid request. Task ID is missing.'); window.location.href='employee_tasks.php';</script>";
    exit();
}

$task_id = intval($_GET['task_id']);

// Optional: Check if the logged-in user is authorized to complete this task.
// You can perform an additional query to verify that the task is assigned to them.
// For simplicity, this example simply updates the task record.

// Prepare the update statement to mark the task as "Completed".
$stmt = $conn->prepare("UPDATE tasks SET status = 'Completed' WHERE task_id = ?");
if (!$stmt) {
    echo "<script>alert('Database error: " . htmlspecialchars($conn->error) . "'); window.location.href='view_task.php';</script>";
    exit();
}

$stmt->bind_param("i", $task_id);

if ($stmt->execute()) {
    echo "<script>alert('Task marked as Completed successfully.'); window.location.href='employee_tasks.php';</script>";
} else {
    echo "<script>alert('Failed to update task. Please try again.'); window.location.href='employee_tasks.php';</script>";
}

$stmt->close();
$conn->close();
?>