<?php
session_start();
require_once __DIR__ . '/koneksi.php';

if (!$conn) {
    die('Error: Database connection failed. Please check koneksi.php');
}

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'user') {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Riwayat Booking - Homestay Bali</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard_user.php">HOMESTAY BALI</a>
        <a href="dashboard_user.php" class="btn btn-light btn-sm">Kembali</a>
    </div>
</nav>
<div class="container py-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Riwayat Booking</h2>
            <p class="card-text">Fitur riwayat booking akan ditambahkan di sini. Untuk sementara, kembali ke <a href="dashboard_user.php">Dashboard</a>.</p>
        </div>
    </div>
</div>
</body>
</html>
