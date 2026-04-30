<?php
session_start();
require_once __DIR__ . '/koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Promo - Homestay Bali</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="home.php">HOMESTAY BALI</a>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="booking.php">Booking</a></li>
                <li class="nav-item"><a class="nav-link" href="profil.php">Profil</a></li>
                <li class="nav-item"><a class="nav-link active" href="promo.php">Promo</a></li>
            </ul>
        </div>
    </div>
</nav>
<div class="container py-5">
    <div class="text-center">
        <h1>Promo Homestay</h1>
        <p class="lead">Halaman promo sedang dalam pengembangan. Kembali ke <a href="home.php">beranda</a> atau <a href="booking.php">booking</a>.</p>
    </div>
</div>
</body>
</html>
