<?php
$servername = "localhost"; // Change this if using a remote database
$username = "root"; // Your database username
$password = "firaol@1995"; // Your database password
$dbname = "bank"; // Your database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Set character encoding to UTF-8 to avoid issues with special characters
$conn->set_charset("utf8");
?>