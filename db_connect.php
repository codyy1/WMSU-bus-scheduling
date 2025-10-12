<?php
// Database connection parameters
$servername = "localhost";
$username = "root"; // CHANGE THIS
$password = ""; // CHANGE THIS
$dbname = "wmsu_transport"; // Use the name you created

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session for login management
session_start();

?>
