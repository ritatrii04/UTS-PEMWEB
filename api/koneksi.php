<?php
// Database Connection Configuration
$host = "gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com";
$port = 4000;
$user = "3xHv9xaRno2ynL7.root";
$pass = "0MNShcmHrXByfywt";
$db   = "booking_homestay";

// TiDB Cloud Serverless REQUIRES SSL — buat objek mysqli dulu sebelum connect
$conn = mysqli_init();

if (!$conn) {
    die("Inisialisasi mysqli gagal.");
}

// Aktifkan SSL (MYSQLI_CLIENT_SSL)
// TiDB Cloud menggunakan sertifikat publik yang valid, jadi tidak perlu file CA lokal.
// Cukup set flag SSL agar koneksi terenkripsi.
mysqli_ssl_set($conn, null, null, null, null, null);

// Connect dengan port yang benar menggunakan MYSQLI_CLIENT_SSL, tanpa menyebutkan DB
$connected = mysqli_real_connect(
    $conn,
    $host,
    $user,
    $pass,
    null, // Jangan langsung pilih DB
    $port,
    null,
    MYSQLI_CLIENT_SSL
);

// Check connection
if (!$connected) {
    error_log("Database Connection Error: " . mysqli_connect_error(), 0);
    die(json_encode([
        "error" => "Koneksi ke database gagal.",
        "detail" => mysqli_connect_error()
    ]));
}

// Create DB if not exists
mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS `$db`");
mysqli_select_db($conn, $db);

// Create table users if not exists
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'user',
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    no_hp VARCHAR(20) NOT NULL
)");

// Create table kamar if not exists
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS kamar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kamar VARCHAR(100) NOT NULL,
    harga INT NOT NULL,
    deskripsi TEXT,
    foto VARCHAR(255)
)");

// Set charset to utf8mb4
mysqli_set_charset($conn, "utf8mb4");
?>