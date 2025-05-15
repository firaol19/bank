<?php
session_start();

// Ensure only a Manager can access this page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Manager') {
    header("Location: login.php");
    exit();
}

require 'db_connection.php'; // This file should initialize the $conn variable.

// Set default settings
$defaultSettings = array(
    'system_name'      => 'Credit & Saving System',
    'contact_email'    => 'support@creditsavingsystem.com',
    'default_currency' => 'birr',
    'maintenance_mode' => '0'  // 0 means off, 1 means on
);

$settings = $defaultSettings;

// Check if the settings table exists
$resultCheck = $conn->query("SHOW TABLES LIKE 'settings'");
if ($resultCheck && $resultCheck->num_rows > 0) {
    // Table exists. Retrieve settings.
    $querySettings = "SELECT setting_key, setting_value FROM settings";
    if ($result = $conn->query($querySettings)) {
        while ($row = $result->fetch_assoc()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        $result->free();
    }
} 
// If the table doesn't exist, default values remain.

$systemName      = $settings['system_name'];
$contactEmail    = $settings['contact_email'];
$defaultCurrency = $settings['default_currency'];
$maintenanceMode = $settings['maintenance_mode'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings | Credit & Saving System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>

<body class="bg-2 text-color2">
    <div class="flex">
        <!-- Sidebar Navigation -->
        <aside class="w-[35%] bg-primary text-white h-screen p-6 sticky">
            <h2 class="text-2xl font-bold mb-6">Manager Dashboard</h2>
            <nav>
                <ul>
                    <li class="mb-4"><a href="manager_dashboard.php" class="hover:text-color3">🏠 Overview</a></li>
                    <li class="mb-4"><a href="register_employee.php" class="hover:text-color3">👤 Register Employee</a>
                    </li>
                    <li class="mb-4"><a href="manage_loans.php" class="hover:text-color3">🏦 Loan Approvals</a></li>
                    <li class="mb-4"><a href="manage_transactions.php" class="hover:text-color3">💰 Transactions</a>
                    </li>
                    <li class="mb-4"><a href="customer_accounts.php" class="hover:text-color3">👥 Customer Accounts</a>
                    </li>
                    <li class="mb-4"><a href="view_reports.php" class="hover:text-color3">📊 Reports & Analytics</a>
                    </li>
                    <li class="mb-4"><a href="system_settings.php" class="hover:text-color3">⚙️ System Settings</a></li>
                    <li class="mb-4"><a href="logout.php" class="hover:text-color3">🚪 Logout</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content: System Settings -->
        <main class="flex-grow p-6 h-screen w-[65%] ml-40 overflow-auto">
            <h1 class="text-3xl font-bold mb-6 text-color3">System Settings</h1>
            <p class="text-lg mb-6">Update core configuration options for the system.</p>

            <!-- System Settings Form -->
            <div class="bg-white shadow-md rounded-lg p-6 max-w-lg">
                <!-- Form action can point to a separate processing script, e.g. process_system_settings.php -->
                <form action="process_system_settings.php" method="POST">
                    <div class="mb-4">
                        <label for="system_name" class="block text-lg text-color3">System Name</label>
                        <input type="text" name="system_name" id="system_name"
                            value="<?php echo htmlspecialchars($systemName); ?>"
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                    </div>
                    <div class="mb-4">
                        <label for="contact_email" class="block text-lg text-color3">Contact Email</label>
                        <input type="email" name="contact_email" id="contact_email"
                            value="<?php echo htmlspecialchars($contactEmail); ?>"
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                    </div>
                    <div class="mb-4">
                        <label for="default_currency" class="block text-lg text-color3">Default Currency</label>
                        <input type="text" name="default_currency" id="default_currency"
                            value="<?php echo htmlspecialchars($defaultCurrency); ?>"
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                    </div>
                    <div class="mb-4">
                        <label class="block text-lg text-color3">Maintenance Mode</label>
                        <div class="flex items-center">
                            <label class="mr-4">
                                <input type="radio" name="maintenance_mode" value="0"
                                    <?php echo $maintenanceMode == 0 ? 'checked' : ''; ?>>
                                Off
                            </label>
                            <label>
                                <input type="radio" name="maintenance_mode" value="1"
                                    <?php echo $maintenanceMode == 1 ? 'checked' : ''; ?>>
                                On
                            </label>
                        </div>
                    </div>
                    <button type="submit"
                        class="mt-4 bg-primary text-white px-6 py-3 rounded-lg hover:bg-color3 transition-all w-full">
                        Save Settings
                    </button>
                </form>
            </div>

            <!-- Additional Settings Section -->
            <div class="mt-10 bg-white shadow-md rounded-lg p-6 max-w-lg">
                <h2 class="text-2xl font-bold mb-4 text-color3">Advanced Settings</h2>
                <p class="text-gray-600">Additional configurations and system parameters can be added here in the
                    future.</p>
                <!-- You could include additional forms or toggles for more configurations -->
            </div>
        </main>
    </div>
</body>

</html>