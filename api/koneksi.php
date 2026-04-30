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

// Connect dengan port yang benar menggunakan MYSQLI_CLIENT_SSL
$connected = mysqli_real_connect(
    $conn,
    $host,
    $user,
    $pass,
    $db,
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

// Set charset to utf8mb4
mysqli_set_charset($conn, "utf8mb4");
?>