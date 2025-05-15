<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | Credit & Saving System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>

<body class="bg-2 text-color2">

    <!-- Include the header -->
    <?php include 'header.php'; ?>

    <!-- Contact Section -->
    <section class="py-20 bg-primary text-white text-center">
        <div class="container mx-auto px-6">
            <h2 class="text-4xl font-bold mb-6">Contact Us</h2>
            <p class="text-lg max-w-2xl mx-auto">
                Get in touch with **Jigjiga University Credit & Saving System** for inquiries, support, or feedback. Our
                team is here to assist you!
            </p>
        </div>
    </section>

    <!-- Contact Information -->
    <section class="py-16 bg-secondary text-color5 text-center">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold mb-6">Our Contact Details</h2>
            <p class="text-lg">📍 **Location:** Jigjiga University, Ethiopian Somali Regional State</p>
            <p class="text-lg">📧 **Email:** <a href="mailto:support@creditsavingsystem.com"
                    class="text-color3 hover:underline">support@creditsavingsystem.com</a></p>
            <p class="text-lg">📞 **Phone:** +123-456-7890</p>
            <p class="text-lg">🕒 **Operating Hours:** Monday - Friday (9:00 AM - 5:00 PM)</p>
        </div>
    </section>

    <!-- Contact Form -->
    <section class="py-16 bg-3 text-color2">
        <div class="container mx-auto px-6 max-w-2xl text-center">
            <h2 class="text-3xl font-bold mb-6 text-color3">Send Us a Message</h2>
            <form action="" method="POST" class="bg-primary text-white p-6 rounded-lg shadow-lg">
                <div class="mb-4">
                    <label for="name" class="block text-lg">Full Name</label>
                    <input type="text" name="name" id="name" required
                        class="w-full p-3 rounded-lg bg-secondary text-color1 focus:outline-none focus:ring focus:ring-color3">
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-lg">Email Address</label>
                    <input type="email" name="email" id="email" required
                        class="w-full p-3 rounded-lg bg-secondary text-color2 focus:outline-none focus:ring focus:ring-color3">
                </div>
                <div class="mb-4">
                    <label for="subject" class="block text-lg">Subject</label>
                    <select name="subject" id="subject" required
                        class="w-full p-3 rounded-lg bg-secondary text-color2 focus:outline-none focus:ring focus:ring-color3">
                        <option value="General Inquiry">General Inquiry</option>
                        <option value="Support Request">Support Request</option>
                        <option value="Loan Assistance">Loan Assistance</option>
                        <option value="Complaint">Complaint</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="message" class="block text-lg">Message</label>
                    <textarea name="message" id="message" rows="5" required
                        class="w-full p-3 rounded-lg bg-secondary text-color2 focus:outline-none focus:ring focus:ring-color3"></textarea>
                </div>
                <button type="submit" style="box-shadow: 0 0 10px rgba(43, 39, 39, 0.9);"
                    class="bg-color3 text-white px-6 py-3 rounded-lg hover:bg-color4 transition-all w-full">
                    Send Message
                </button>
            </form>
        </div>
    </section>

    <!-- Embedded Google Map -->
    <section class="py-16 bg-secondary text-color5 text-center">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold mb-6">Visit Us</h2>
            <iframe class="mx-auto w-full max-w-lg h-60 rounded-lg shadow-lg"
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4044346.0626959665!2d41.854745377352836!3d9.297713929797601!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x165bb1d0f4bd826d%3A0x36a5e00e36b2e9ff!2sJigjiga%20University!5e0!3m2!1sen!2set!4v1625503300000"
                allowfullscreen="" loading="lazy">
            </iframe>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

</body>

</html>