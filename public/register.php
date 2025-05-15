<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Registration | Credit & Saving System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>

<body class="bg-2 text-color2">

    <!-- Include the header -->
    <?php include 'header.php'; ?>

    <!-- Registration Form -->
    <section class="py-20 text-white text-center"
        style="background-image: url('images/bg3.png'); background-size: cover; background-repeat: no-repeat; background-position: center;">
        <div class="container mx-auto px-6 max-w-md">
            <h2 class="text-4xl font-bold mb-6">Create Your Account</h2>
            <p class="text-lg mb-6">Join Jigjiga University Credit & Saving System today!</p>

            <form action="process_register.php" method="POST" class="bg-primary text-color5 p-6 rounded-lg shadow-lg">
                <div class="mb-4">
                    <label for="full_name" class="block text-lg">Full Name</label>
                    <input type="text" name="full_name" id="full_name" required
                        class="w-full p-3 bg-secondary rounded-lg text-color2 focus:outline-none focus:ring focus:ring-color3">
                </div>
                <div class="mb-4">
                    <label for="username" class="block text-lg">Username</label>
                    <input type="text" name="username" id="username" required
                        class="w-full p-3 rounded-lg bg-secondary text-color2 focus:outline-none focus:ring focus:ring-color3">
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-lg">Email Address</label>
                    <input type="email" name="email" id="email" required
                        class="w-full p-3 rounded-lg bg-secondary text-color2 focus:outline-none focus:ring focus:ring-color3">
                </div>
                <div class="mb-4">
                    <label for="phone" class="block text-lg">Phone Number</label>
                    <input type="text" name="phone" id="phone" required
                        class="w-full p-3 rounded-lg bg-secondary text-color2 focus:outline-none focus:ring focus:ring-color3">
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-lg">Password</label>
                    <input type="password" name="password" id="password" required
                        class="w-full p-3 rounded-lg bg-secondary text-color2 focus:outline-none focus:ring focus:ring-color3">
                </div>
                <!-- Role is automatically set to "Customer" -->
                <input type="hidden" name="role" value="Customer">

                <button type="submit" style="box-shadow: 0 0 10px rgba(43, 39, 39, 0.9);"
                    class="mt-6 bg-color3 text-white px-6 py-3 rounded-lg hover:bg-color4 transition-all w-full">
                    Register Now
                </button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

</body>

</html>