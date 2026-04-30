<?php
// Database Connection Configuration
$host = "gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com";
$port = 4000;
$user = "3xHv9xaRno2ynL7.root";
$pass = "0MNShcmHrXByfywt";
$db   = "booking_homestay";  // Database name

// Create connection
$conn = mysqli_connect($host, $user, $pass, $db);

// Check connection
if (!$conn) {
    error_log("Database Connection Error: " . mysqli_connect_error(), 0);
    die("Koneksi ke database gagal. Hubungi administrator. Error: " . mysqli_connect_error());
}

// Set charset to utf8
mysqli_set_charset($conn, "utf8mb4");
?>