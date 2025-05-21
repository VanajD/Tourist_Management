<?php
// Database connection parameters
$servername = "sql211.infinityfree.com";
$username = "if0_38333020";
$password = "AjbOdkjP5Y"; // Your password
$dbname = "if0_38333020_travel";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("<div class='alert alert-danger text-center'>Connection failed: " . $conn->connect_error . "</div>");
}
?>
