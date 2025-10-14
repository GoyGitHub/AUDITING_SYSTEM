<?php
$host = "localhost";        
$username = "root";         
$password = "";             // No password
$database = "audit_system";  

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Optional: echo "Connected successfully";
?>
