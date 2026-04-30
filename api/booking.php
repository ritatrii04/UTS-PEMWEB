<?php
require_once __DIR__ . '/koneksi.php';

$lokasi = trim($_GET['lokasi'] ?? '');
$tipe = trim($_GET['tipe'] ?? '');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking - Homestay Bali</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="home.php">HOMESTAY BALI</a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
                <li class="nav-item"><a class="nav-link active" href="booking.php">Booking</a></li>
                <li class="nav-item"><a class="nav-link" href="profil.php">Profil</a></li>
                <?php if(isset($_SESSION['username'])): ?>
                    <li class="nav-item"><a class="btn btn-success ms-lg-3" href="<?php echo ($_SESSION['role'] == 'admin') ? 'dashboard.admin.php' : 'dashboard_user.php'; ?>">Dashboard</a></li>
                    <li class="nav-item"><a class="btn btn-danger ms-2" href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="btn btn-warning ms-lg-3" href="login.php">Masuk</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container my-5">
    <?php if ($lokasi || $tipe): ?>
        <div class="alert alert-info">
            <strong>Hasil Pencarian:</strong>
            <?php if ($lokasi) echo 'Lokasi: ' . htmlspecialchars($lokasi) . '. '; ?>
            <?php if ($tipe) echo 'Tipe: ' . htmlspecialchars($tipe) . '.'; ?>
        </div>
    <?php endif; ?>
    <h2 class="mb-4">Tersedia untuk Anda</h2>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2" class="card-img-top" alt="Kamar">
                <div class="card-body">
                    <h5 class="card-title fw-bold">Standard Room</h5>
                    <p class="card-text text-muted">Kamar nyaman dengan AC dan sarapan pagi.</p>
                    <h5 class="text-primary mb-3">Rp 300.000 <small class="text-muted fs-6">/malam</small></h5>
                    <button class="btn btn-primary w-100">Pilih Kamar</button>
                </div>
            </div>
        </div>
        </div>
</div>

</body>
</html>