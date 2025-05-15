<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credit & Saving System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.10.2/dist/cdn.min.js" defer></script>
</head>

<body class="bg-2 text-color2">

    <!-- Include the header -->
    <?php include 'header.php'; ?>

    <!-- Hero Section with Background Image -->
    <section class="relative bg-secondary text-color5 text-center py-20 bg-cover bg-center"
        style="background-image: url('<?php echo "storage/images/hero-bg.jpg"; ?>');">
        <div class="container mx-auto">
            <h2 class="text-4xl font-bold mb-4 animate-fadeIn">Empowering Financial Growth</h2>
            <p class="text-lg mb-6 animate-slideUp">Secure credit and saving solutions at your fingertips.</p>
            <a href="login.php"
                class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-color3 transition-all duration-300 ease-in-out animate-bounce">
                Login Now
            </a>
        </div>
    </section>

    <!-- Services Section with Hover Effects -->
    <section class="py-10 bg-3">
        <div class="container mx-auto text-center">
            <h2 class="text-2xl font-bold mb-6 text-color3 animate-slideUp">Our Services</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 px-6">
                <div
                    class="p-6 bg-primary text-white rounded-lg shadow-md transform transition-all duration-300 hover:scale-105">
                    <img src="images/transfer.png" alt=" Transfer Money" class="w-30 mx-auto mb-4">
                    <h3 class="font-bold text-lg">Transfer Money Anywhere</h3>
                    <p>You can transfer money anywhere in the world securely.</p>
                </div>
                <div
                    class="p-6 bg-secondary text-color5 rounded-lg shadow-md transform transition-all duration-300 hover:scale-105">
                    <img src="images/loan.png" alt="Loans & Approvals" class="w-30 mx-auto mb-4">
                    <h3 class="font-bold text-lg">Loans & Approvals</h3>
                    <p>Easy loan application process with secure approvals.</p>
                </div>
                <div
                    class="p-6 bg-primary text-white rounded-lg shadow-md transform transition-all duration-300 hover:scale-105">
                    <img src="images/deposit.png" alt="Deposits & Withdrawals" class="w-30 mx-auto mb-4 rounded-lg">
                    <h3 class="font-bold text-lg">Deposits & Withdrawals</h3>
                    <p>Effortlessly deposit and withdraw funds anytime.</p>
                </div>
            </div>
        </div>
    </section>
    <!-- Testimonials Section with Carousel -->
    <section class="py-10 bg-primary text-white text-center" x-data="{ active: 0 }">
        <div class="container mx-auto">
            <h2 class="text-2xl font-bold mb-6">What Our Members Say</h2>
            <div class="relative max-w-2xl mx-auto overflow-hidden h-20">
                <div class="absolute w-full transition-all duration-500"
                    :class="{ 'opacity-100 translate-x-0': active === 0, 'opacity-0 -translate-x-full': active === 1 }">
                    <p class="text-lg italic">"This system has made transactions so much easier and secure!" - Yordanos
                        Yitbarek</p>
                </div>
                <div class="absolute w-full transition-all duration-500"
                    :class="{ 'opacity-100 translate-x-0': active === 1, 'opacity-0 translate-x-full': active === 0 }">
                    <p class="text-lg italic">"I can now manage my savings efficiently from anywhere!" - Firaol Bekele
                    </p>
                </div>
            </div>
            <div class="flex justify-center mt-4">
                <button class="mx-2 bg-white text-color1 px-4 py-2 rounded-lg" @click="active = 0">1</button>
                <button class="mx-2 bg-white text-color1 px-4 py-2 rounded-lg" @click="active = 1">2</button>
            </div>
        </div>
    </section>

    <!-- Latest News Section -->
    <section class="py-10 bg-3 text-center">
        <div class="container mx-auto">
            <h2 class="text-2xl font-bold mb-6 text-color3 animate-slideUp">Latest Updates</h2>
            <p>New loan features available! Apply now to secure your future.</p>
        </div>
    </section>

    <!-- Footer -->

    <?php include 'footer.php'; ?>
</body>

</html>