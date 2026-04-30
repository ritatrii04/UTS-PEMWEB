<?php
require_once __DIR__ . '/koneksi.php';

if (!$conn) {
    die('Error: Database connection failed. Please check koneksi.php');
}

if (!isset($_SESSION['username']) || $_SESSION['role'] != "user") {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Beranda Pengguna - Homestay Bali</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hero-user {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url("https://images.unsplash.com/photo-1499793983690-e29da59ef1c2");
            background-size: cover;
            background-position: center;
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            border-radius: 0 0 20px 20px;
        }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard_user.php">HOMESTAY BALI</a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link active" href="dashboard_user.php">Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="booking.php">Pesan Kamar</a></li>
                <li class="nav-item"><a class="nav-link" href="riwayat_booking.php">Riwayat Saya</a></li>
                <li class="nav-item dropdown ms-lg-3">
                    <a class="nav-link dropdown-toggle btn btn-light text-primary fw-bold px-3" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        👋 <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profil.php">Profil Saya</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-3">
    <div class="hero-user shadow text-center">
        <div>
            <h1 class="fw-bold">Selamat Datang kembali, <span class="text-warning"><?php echo htmlspecialchars($_SESSION['username']); ?></span>!</h1>
            <p class="lead">Siap untuk liburan nyaman di Bali hari ini?</p>
            <a href="booking.php" class="btn btn-warning btn-lg fw-bold mt-2">Mulai Pesan Kamar</a>
        </div>
    </div>
</div>

<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <h3>Rekomendasi untuk Anda</h3>
        <a href="booking.php" class="text-decoration-none">Lihat Semua Kamar →</a>
    </div>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <img src="https://images.unsplash.com/photo-1505691938895-1758d7feb511" class="card-img-top" alt="Standard">
                <div class="card-body">
                    <h5 class="fw-bold">Standard Room</h5>
                    <p class="text-muted small">Fasilitas dasar yang nyaman dengan harga terjangkau.</p>
                    <p class="text-primary fw-bold fs-5">Rp 250.000 <span class="fs-6 text-muted fw-normal">/ malam</span></p>
                    <a href="booking.php" class="btn btn-outline-primary w-100">Pesan Sekarang</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <img src="https://images.unsplash.com/photo-1560185893-a55cbc8c57e8" class="card-img-top" alt="Deluxe">
                <div class="card-body">
                    <h5 class="fw-bold">Deluxe Room</h5>
                    <p class="text-muted small">Kamar luas dengan pemandangan langsung ke kolam renang.</p>
                    <p class="text-primary fw-bold fs-5">Rp 400.000 <span class="fs-6 text-muted fw-normal">/ malam</span></p>
                    <a href="booking.php" class="btn btn-outline-primary w-100">Pesan Sekarang</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>