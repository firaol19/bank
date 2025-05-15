<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services | Credit & Saving System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>

<body class="bg-2 text-color2">

    <!-- Include the header -->
    <?php include 'header.php'; ?>

    <!-- Services Introduction -->
    <section class="py-20 bg-primary text-white text-center">
        <div class="container mx-auto px-6">
            <h2 class="text-4xl font-bold mb-6">Our Services</h2>
            <p class="text-lg max-w-2xl mx-auto">
                Welcome to **Jigjiga University Credit & Saving System**, where we provide **secure, efficient, and
                accessible**
                financial solutions to our members. Our system ensures **fast transactions, reliable loan processing,
                and digital banking services**.
            </p>
        </div>
    </section>

    <!-- Service Features -->
    <section class="py-16 bg-secondary text-color5 text-center">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold mb-6">What We Offer</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div style="box-shadow: 0 0 15px #a0c878;"
                    class="p-6 bg-3 rounded-lg  transform hover:scale-105 transition-all duration-300">
                    <img src="images/register.png" alt="Register" class="w-20 mx-auto mb-4">
                    <h3 class="font-bold text-lg">Online Member Registration</h3>
                    <p>Securely enroll as a member and access our financial services.</p>
                </div>
                <div
                    class="p-6 bg-primary text-white rounded-lg shadow-md transform hover:scale-105 transition-all duration-300">
                    <img src="images/loan.png" alt="Loan Approval" class="w-20 mx-auto mb-4">
                    <h3 class="font-bold text-lg">Loan Request & Approvals</h3>
                    <p>Apply for loans online and track approvals instantly.</p>
                </div>
                <div style="box-shadow: 0 0 15px #a0c878;"
                    class="p-6 bg-3 rounded-lg  transform hover:scale-105 transition-all duration-300">
                    <img src="images/deposit.png" alt="Deposit & Withdraw" class="w-20 mx-auto mb-4">
                    <h3 class="font-bold text-lg">Deposits & Withdrawals</h3>
                    <p>Perform transactions securely and check your balance anytime.</p>
                </div>
                <div
                    class="p-6 bg-primary text-white rounded-lg shadow-md transform hover:scale-105 transition-all duration-300">
                    <img src="images/transfer.png" alt="Money Transfer" class="w-20 mx-auto mb-4">
                    <h3 class="font-bold text-lg">Money Transfers</h3>
                    <p>Instantly transfer money between accounts securely.</p>
                </div>
                <div style="box-shadow: 0 0 15px #a0c878;"
                    class="p-6 bg-3 rounded-lg shadow-md transform hover:scale-105 transition-all duration-300">
                    <img src="images/statement.png" alt="Financial Statements" class="w-20 mx-auto mb-4">
                    <h3 class="font-bold text-lg">Financial Statements</h3>
                    <p>View transaction details and account statements on demand.</p>
                </div>
                <div
                    class="p-6 bg-primary text-white rounded-lg shadow-md transform hover:scale-105 transition-all duration-300">
                    <img src="images/calculator.png" alt="EMI Calculator" class="w-20 mx-auto mb-4">
                    <h3 class="font-bold text-lg">EMI Calculator</h3>
                    <p>Calculate loan repayment installments with our EMI tool.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-16 bg-3 text-center">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold mb-6 text-color3">How It Works</h2>
            <div class="text-lg max-w-2xl mx-auto">
                ✅ **Step 1:** Register and verify your account. <br>
                ✅ **Step 2:** Log in and explore financial services. <br>
                ✅ **Step 3:** Apply for loans, transfers, and withdrawals. <br>
                ✅ **Step 4:** Track your financial transactions anytime.
            </div>
        </div>
    </section>

    <!-- FAQs Section -->
    <section class="py-16 bg-primary text-white text-center">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold mb-6">Frequently Asked Questions (FAQs)</h2>
            <div class="text-lg max-w-2xl mx-auto">
                <p><strong>Who can register?</strong> Any employee of Jigjiga University.</p>
                <p><strong>How do I request a loan?</strong> Log in and submit a loan request.</p>
                <p><strong>Is my data secure?</strong> Yes, all transactions are encrypted.</p>
                <p><strong>Can I view past transactions?</strong> Yes, your financial statements are available anytime.
                </p>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-16 bg-secondary text-color5 text-center">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold mb-6">Start Managing Your Savings Now!</h2>
            <p class="text-lg max-w-2xl mx-auto mb-5">Join the **Jigjiga University Credit & Saving System** and take
                control
                of your financial future.</p>
            <a href="register.php"
                class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-color3 transition-all duration-300 ease-in-out mt-4">
                Register Now
            </a>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

</body>

</html>