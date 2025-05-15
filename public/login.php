<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Credit & Saving System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>

<body>

    <!-- Include the header -->
    <?php include 'header.php'; ?>

    <div class="bg-2 text-color2 flex justify-center items-center min-h-screen"
        style="background-image: url('images/bg3.png'); background-size: cover; background-repeat: no-repeat; background-position: center;">
        <div class=" bg-primary text-white p-8 rounded-lg shadow-lg w-96 text-center">
            <h2 class="text-2xl font-bold mb-4">Welcome Back</h2>
            <p class="text-lg mb-6">Login to your account</p>

            <form action="loginProcess.php" method="POST">
                <label for="username" class="block text-lg mb-2">Username</label>
                <input type="text" name="username" id="username" required
                    class="w-full p-3 rounded-lg bg-secondary text-color2 focus:outline-none focus:ring focus:ring-color3">

                <label for="password" class="block text-lg mt-4 mb-2">Password</label>
                <input type="password" name="password" id="password" required
                    class="w-full p-3 rounded-lg bg-secondary text-color2 focus:outline-none focus:ring focus:ring-color3">

                <button type="submit" style="box-shadow: 0 0 10px rgba(43, 39, 39, 0.9);"
                    class="mt-6   text-white px-6 py-3 rounded-lg hover:bg-color4 transition-all w-full">
                    Login
                </button>
            </form>

            <p class="mt-4 text-sm">Don' t have an account? <a href="register.php" class=" hover:underline">Sign
                    Up</a></p>
        </div>
    </div>

</body>

</html>