<!-- header.php -->
<header class="bg-primary text-white py-4">
    <div class="container flex justify-between items-center px-6 md:px-20">
        <!-- Logo -->
        <h1 class="text-xl font-bold">Credit & Saving System</h1>

        <!-- Desktop Navigation -->
        <nav class="hidden md:block">
            <ul class="flex space-x-10">
                <li><a href="index.php" class="text-white hover:text-color3">Home</a></li>
                <li><a href="login.php" class="text-white hover:text-color3">Login</a></li>
                <li><a href="about.php" class="text-white hover:text-color3">About</a></li>
                <li><a href="services.php" class="text-white hover:text-color3">Services</a></li>
                <li><a href="contact.php" class="text-white hover:text-color3">Contact</a></li>
            </ul>
        </nav>

        <!-- Mobile Menu Button -->
        <button id="menu-btn" class="block md:hidden text-white focus:outline-none">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                </path>
            </svg>
        </button>
    </div>

    <!-- Mobile Menu (Hidden by default) -->
    <nav id="mobile-menu" class="hidden md:hidden bg-secondary text-color5 p-4">
        <ul class="space-y-4 text-center">
            <li><a href="index.php" class="block text-white hover:text-color3">Home</a></li>
            <li><a href="login.php" class="block text-white hover:text-color3">Login</a></li>
            <li><a href="about.php" class="block text-white hover:text-color3">About</a></li>
            <li><a href="services.php" class="block text-white hover:text-color3">Services</a></li>
            <li><a href="contact.php" class="block text-white hover:text-color3">Contact</a></li>
        </ul>
    </nav>
</header>

<!-- JavaScript for Mobile Menu Toggle -->
<script>
const menuBtn = document.getElementById('menu-btn');
const mobileMenu = document.getElementById('mobile-menu');

menuBtn.addEventListener('click', () => {
    mobileMenu.classList.toggle('hidden');
});
</script>