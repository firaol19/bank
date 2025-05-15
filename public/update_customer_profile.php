<?php
session_start();

// Ensure only authenticated customers access this page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Customer') {
    header("Location: login.php");
    exit();
}

require 'db_connection.php'; // Initializes MySQLi connection in $conn
$customer_id = $_SESSION['user_id'];

$errors = [];
$success = "";

// Process the form submission.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve updated fields from POST
    $name         = trim($_POST['name'] ?? '');
    $age          = trim($_POST['age'] ?? '');
    $salary       = trim($_POST['salary'] ?? '');
    $address      = trim($_POST['address'] ?? '');
    $department   = trim($_POST['department'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');

    // Basic validation (expand as needed)
    if (empty($name)) {
        $errors[] = "Full Name is required.";
    }
    // Optionally, add further validation for numeric fields.
    
    if (empty($errors)) {
        // Prepare update query for the customer's profile.
        $stmt = $conn->prepare("UPDATE customers SET name = ?, age = ?, salary = ?, address = ?, department = ?, phone_number = ? WHERE customer_id = ?");
        if (!$stmt) {
            $errors[] = "Database error: " . $conn->error;
        } else {
            // Convert age and salary to appropriate types if valid; otherwise, pass NULL.
            $age_val    = is_numeric($age) ? $age : null;
            $salary_val = is_numeric($salary) ? $salary : null;
            $stmt->bind_param("sidsssi", $name, $age_val, $salary_val, $address, $department, $phone_number, $customer_id);
            if ($stmt->execute()) {
                $success = "Profile updated successfully.";
            } else {
                $errors[] = "Failed to update profile: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// Retrieve current profile data to pre-fill the form.
$stmt = $conn->prepare("SELECT name, age, salary, address, department, phone_number FROM customers WHERE customer_id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result    = $stmt->get_result();
$customer  = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Update Profile | Bank System</title>
    <!-- Tailwind CSS CDN & Custom Styles -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet" />
    <link href="styles.css" rel="stylesheet" />
    <style>
    .mobile-menu {
        display: none;
    }

    @media (max-width: 767px) {
        .mobile-menu {
            display: block;
        }
    }
    </style>
    <script>
    function toggleMobileMenu() {
        var menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
    }
    </script>
</head>

<body class="bg-2 text-color2">
    <!-- Mobile Header with Hamburger Navigation -->
    <header class="bg-primary text-white flex items-center justify-between px-4 py-3 md:hidden">
        <h1 class="text-xl font-bold">Update Profile</h1>
        <button onclick="toggleMobileMenu()">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                </path>
            </svg>
        </button>
    </header>
    <!-- Mobile Navigation Menu -->
    <nav id="mobile-menu" class="bg-primary text-white px-4 py-2 hidden md:hidden">
        <ul>
            <li class="py-1"><a href="customer_dashboard.php" class="block">🏠 Dashboard</a></li>
            <li class="py-1"><a href="account_summary.php" class="block">📊 Account Summary</a></li>
            <li class="py-1"><a href="transactions.php" class="block">💸 Transactions</a></li>
            <li class="py-1"><a href="loans.php" class="block">🏦 Loans</a></li>
            <li class="py-1"><a href="customer_profile.php" class="block">👤 Profile</a></li>
            <li class="py-1"><a href="support.php" class="block">🛠 Support</a></li>
            <li class="py-1"><a href="logout.php" class="block">🚪 Logout</a></li>
        </ul>
    </nav>

    <!-- Desktop Layout: Fixed Sidebar and Scrollable Main Content -->
    <div class="hidden md:flex md:h-screen">
        <!-- Sidebar for Desktop (Fixed) -->
        <aside class="hidden md:block md:w-1/4 bg-primary text-white max-h-screen p-6 sticky top-0">
            <h2 class="text-2xl font-bold mb-6">Customer Dashboard</h2>
            <nav>
                <ul>
                    <li class="mb-4">
                        <a href="customer_dashboard.php" class="hover:text-color3 flex items-center">
                            <span class="mr-2 text-xl">🏠</span>Dashboard
                        </a>
                    </li>
                    <li class="mb-4">
                        <a href="account_summary.php" class="hover:text-color3 flex items-center">
                            <span class="mr-2 text-xl">📊</span>Account Summary
                        </a>
                    </li>
                    <li class="mb-4">
                        <a href="transactions.php" class="hover:text-color3 flex items-center">
                            <span class="mr-2 text-xl">💸</span>Transactions
                        </a>
                    </li>
                    <li class="mb-4">
                        <a href="loans.php" class="hover:text-color3 flex items-center">
                            <span class="mr-2 text-xl">🏦</span>Loans
                        </a>
                    </li>
                    <li class="mb-4">
                        <a href="customer_profile.php" class="hover:text-color3 flex items-center">
                            <span class="mr-2 text-xl">👤</span>Profile
                        </a>
                    </li>
                    <li class="mb-4">
                        <a href="support.php" class="hover:text-color3 flex items-center">
                            <span class="mr-2 text-xl">🛠</span>Support
                        </a>
                    </li>
                    <li class="mb-4">
                        <a href="logout.php" class="hover:text-color3 flex items-center">
                            <span class="mr-2 text-xl">🚪</span>Logout
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content for Desktop -->
        <main class="w-full md:w-3/4 p-6">
            <h1 class="text-2xl font-bold text-color3 mb-6">Update Profile</h1>
            <?php if (!empty($errors)): ?>
            <div class="mb-4 p-4 bg-red-200 text-red-800 rounded">
                <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
            <div class="mb-4 p-4 bg-green-200 text-green-800 rounded">
                <p><?php echo htmlspecialchars($success); ?></p>
            </div>
            <?php endif; ?>
            <div class="bg-white p-6 rounded-lg shadow max-w-2xl mx-auto">
                <form action="update_customer_profile.php" method="POST">
                    <div class="mb-4">
                        <label for="name" class="block text-lg text-color3 mb-2">Full Name</label>
                        <input type="text" id="name" name="name"
                            value="<?php echo htmlspecialchars($customer['name']?? ''); ?>" required
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                    </div>
                    <div class="mb-4">
                        <label for="age" class="block text-lg text-color3 mb-2">Age</label>
                        <input type="number" id="age" name="age"
                            value="<?php echo htmlspecialchars($customer['age']?? ''); ?>"
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                    </div>
                    <div class="mb-4 hidden">
                        <label for="salary" class="block text-lg text-color3 mb-2">Salary</label>
                        <input type="number" step="0.01" id="salary" name="salary"
                            value="<?php echo htmlspecialchars($customer['salary']?? ''); ?>"
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                    </div>
                    <div class="mb-4 ">
                        <label for="address" class="block text-lg text-color3 mb-2">Address</label>
                        <input type="string" step="0.01" id="address" name="address"
                            value="<?php echo htmlspecialchars($customer['address']?? ''); ?>"
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                    </div>

                    <div class="mb-4 hidden">
                        <label for="department" class="block text-lg text-color3 mb-2">Department</label>
                        <input type="text" id="department" name="department"
                            value="<?php echo htmlspecialchars($customer['department']?? ''); ?>"
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                    </div>
                    <div class="mb-4">
                        <label for="phone_number" class="block text-lg text-color3 mb-2">Phone Number</label>
                        <input type="text" id="phone_number" name="phone_number"
                            value="<?php echo htmlspecialchars($customer['phone_number']?? ''); ?>"
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                    </div>
                    <div class="flex justify-end">
                        <button type="submit"
                            class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-color3 transition-all">Update
                            Profile</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <!-- Mobile Layout -->
    <div class="md:hidden p-4">
        <h1 class="text-2xl font-bold text-color3 mb-4">Update Profile</h1>
        <?php if (!empty($errors)): ?>
        <div class="mb-4 p-4 bg-red-200 text-red-800 rounded">
            <?php foreach ($errors as $error): ?>
            <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
        <div class="mb-4 p-4 bg-green-200 text-green-800 rounded">
            <p><?php echo htmlspecialchars($success); ?></p>
        </div>
        <?php endif; ?>
        <div class="bg-white p-6 rounded-lg shadow">
            <form action="update_customer_profile.php" method="POST">
                <div class="mb-4">
                    <label for="name_mobile" class="block text-lg text-color3 mb-2">Full Name</label>
                    <input type="text" id="name_mobile" name="name"
                        value="<?php echo htmlspecialchars($customer['name']?? ''); ?>" required
                        class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                </div>
                <div class="mb-4">
                    <label for="age_mobile" class="block text-lg text-color3 mb-2">Age</label>
                    <input type="number" id="age_mobile" name="age"
                        value="<?php echo htmlspecialchars($customer['age']?? ''); ?>"
                        class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                </div>
                <div class="mb-4 hidden">
                    <label for="salary_mobile" class="block text-lg text-color3 mb-2">Salary</label>
                    <input type="number" step="0.01" id="salary_mobile" name="salary"
                        value="<?php echo htmlspecialchars($customer['salary']?? ''); ?>"
                        class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                </div>
                <div class="mb-4">
                    <label for="address_mobile" class="block text-lg text-color3 mb-2">Address</label>
                    <textarea id="address_mobile" name="address" rows="3"
                        class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3"><?php echo htmlspecialchars($customer['address']?? ''); ?></textarea>
                </div>
                <div class="mb-4 hidden">
                    <label for="department_mobile" class="block text-lg text-color3 mb-2">Department</label>
                    <input type="text" id="department_mobile" name="department"
                        value="<?php echo htmlspecialchars($customer['department']?? ''); ?>"
                        class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                </div>
                <div class="mb-4">
                    <label for="phone_number_mobile" class="block text-lg text-color3 mb-2">Phone Number</label>
                    <input type="text" id="phone_number_mobile" name="phone_number"
                        value="<?php echo htmlspecialchars($customer['phone_number']?? ''); ?>"
                        class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-color3">
                </div>
                <div class="flex justify-end">
                    <button type="submit"
                        class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-color3 transition-all">Update
                        Profile</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>