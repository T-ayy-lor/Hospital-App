<?php
// database connection settings
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'hospital_db';

// connect database via mysqli
$conn = new mysqli($host, $user, $pass, $db);

// stop script if connection fails
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
echo "Connected successfully!";
?>