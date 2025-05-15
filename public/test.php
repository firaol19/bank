<?php
$to = "firaolbekele00@gmail.com";
$subject = "Test Email";
$message = "This is a test email from PHP mail function.";
$headers = "From: firaolbekele30@gmail.com\r\n";
if (mail($to, $subject, $message, $headers)) {
    echo "Email sent successfully!";
} else {
    echo "Failed to send email.";
}
?>