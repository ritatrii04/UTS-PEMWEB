<?php
require_once __DIR__ . '/koneksi.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'user') {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];

// Ambil riwayat pesanan
$query = "SELECT p.*, k.nama_kamar, k.foto FROM pesanan p 
          JOIN kamar k ON p.kamar_id = k.id 
          WHERE p.username = ? 
          ORDER BY p.tanggal_pesan DESC";
$stmt = $conn->prepare($query);
if ($stmt) {
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = false;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Riwayat Booking - Homestay Bali</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .status-pending { color: #fd7e14; background-color: #fff3cd; padding: 5px 10px; border-radius: 20px; font-weight: bold; font-size: 0.85rem; }
        .status-confirmed { color: #198754; background-color: #d1e7dd; padding: 5px 10px; border-radius: 20px; font-weight: bold; font-size: 0.85rem; }
        .status-cancelled { color: #dc3545; background-color: #f8d7da; padding: 5px 10px; border-radius: 20px; font-weight: bold; font-size: 0.85rem; }
    </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard_user.php">HOMESTAY BALI</a>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link" href="dashboard_user.php">Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="booking.php">Pesan Kamar</a></li>
                <li class="nav-item"><a class="nav-link active" href="riwayat_booking.php">Riwayat Saya</a></li>
                <li class="nav-item ms-lg-3"><a class="btn btn-light text-primary fw-bold btn-sm" href="profil.php">Profil Saya</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold m-0"><i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Booking Anda</h2>
        <a href="booking.php" class="btn btn-primary fw-bold"><i class="bi bi-plus-lg me-2"></i>Pesan Kamar Lagi</a>
    </div>

    <?php if ($result && $result->num_rows > 0): ?>
        <div class="row g-4">
            <?php while ($row = $result->fetch_assoc()): 
                $foto_url = !empty($row['foto']) ? htmlspecialchars($row['foto']) : 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2';
                
                $status_class = 'status-pending';
                $status_text = 'Menunggu Konfirmasi';
                if ($row['status'] == 'confirmed') {
                    $status_class = 'status-confirmed';
                    $status_text = 'Terkonfirmasi';
                } elseif ($row['status'] == 'cancelled') {
                    $status_class = 'status-cancelled';
                    $status_text = 'Dibatalkan';
                }
                
                $dtIn = new DateTime($row['tanggal_checkin']);
                $dtOut = new DateTime($row['tanggal_checkout']);
                $interval = $dtIn->diff($dtOut);
                $malam = $interval->days ?: 1;
            ?>
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="row g-0 h-100">
                        <div class="col-md-4">
                            <img src="<?= $foto_url ?>" class="img-fluid rounded-start h-100" alt="Kamar" style="object-fit: cover;" onerror="this.src='https://images.unsplash.com/photo-1560448204-e02f11c3d0e2'">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body d-flex flex-column h-100">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title fw-bold m-0 text-primary"><?= htmlspecialchars($row['nama_kamar']) ?></h5>
                                    <span class="<?= $status_class ?>"><?= $status_text ?></span>
                                </div>
                                <p class="text-muted small mb-3">Dipesan pada: <?= date('d M Y H:i', strtotime($row['tanggal_pesan'])) ?></p>
                                
                                <div class="row mb-3 flex-grow-1">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Check-in</small>
                                        <strong><?= date('d M Y', strtotime($row['tanggal_checkin'])) ?></strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Check-out</small>
                                        <strong><?= date('d M Y', strtotime($row['tanggal_checkout'])) ?></strong>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-end mt-auto pt-3 border-top">
                                    <div>
                                        <small class="text-muted">Durasi: <?= $malam ?> Malam</small>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted d-block">Total Pembayaran</small>
                                        <strong class="fs-5 text-dark">Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-5">
                <i class="bi bi-calendar-x text-muted mb-3 d-block" style="font-size: 4rem;"></i>
                <h4 class="fw-bold">Belum Ada Riwayat Pesanan</h4>
                <p class="text-muted mb-4">Anda belum pernah melakukan pemesanan kamar homestay. Yuk mulai rencanakan liburan Anda!</p>
                <a href="booking.php" class="btn btn-primary btn-lg fw-bold px-5">Lihat Daftar Kamar</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
