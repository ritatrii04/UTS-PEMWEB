<?php
require_once __DIR__ . '/koneksi.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}

// Handle Booking Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_kamar'])) {
    if (!isset($_SESSION['username'])) {
        echo "<script>alert('Silakan login terlebih dahulu untuk memesan kamar!'); window.location='login.php';</script>";
        exit();
    }
    
    // Validasi CSRF
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("<script>alert('Token CSRF tidak valid!'); window.location='booking.php';</script>");
    }

    $kamar_id = (int)$_POST['kamar_id'];
    $checkin = trim($_POST['checkin'] ?? '');
    $checkout = trim($_POST['checkout'] ?? '');
    $username = $_SESSION['username'];

    if (empty($checkin) || empty($checkout)) {
        echo "<script>alert('Tanggal check-in dan check-out harus diisi!');</script>";
    } else {
        // Cek tanggal valid
        $dtIn = new DateTime($checkin);
        $dtOut = new DateTime($checkout);
        if ($dtOut <= $dtIn) {
            echo "<script>alert('Tanggal check-out harus lebih dari check-in!');</script>";
        } else {
            $interval = $dtIn->diff($dtOut);
            $days = $interval->days;
            if ($days < 1) $days = 1;

            // Ambil harga kamar
            $stmt = $conn->prepare("SELECT harga FROM kamar WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param("i", $kamar_id);
                $stmt->execute();
                $res = $stmt->get_result();
                $kamar = $res->fetch_assoc();
                $stmt->close();

                if ($kamar) {
                    $total_harga = $kamar['harga'] * $days;
                    // Insert pesanan
                    $stmt_pesan = $conn->prepare("INSERT INTO pesanan (username, kamar_id, tanggal_checkin, tanggal_checkout, total_harga, status) VALUES (?, ?, ?, ?, ?, 'pending')");
                    if ($stmt_pesan) {
                        $stmt_pesan->bind_param("sissi", $username, $kamar_id, $checkin, $checkout, $total_harga);
                        if ($stmt_pesan->execute()) {
                            echo "<script>alert('Pemesanan berhasil! Silakan cek riwayat Anda.'); window.location='riwayat_booking.php';</script>";
                            exit();
                        } else {
                            echo "<script>alert('Gagal memesan: " . addslashes($stmt_pesan->error) . "');</script>";
                        }
                        $stmt_pesan->close();
                    }
                } else {
                    echo "<script>alert('Kamar tidak ditemukan!');</script>";
                }
            }
        }
    }
}

$lokasi = trim($_GET['lokasi'] ?? '');
$tipe = trim($_GET['tipe'] ?? '');

// Filter pencarian
$query = "SELECT * FROM kamar WHERE 1=1";
$params = [];
$types = "";

if ($lokasi) {
    // Karena kita tidak punya kolom lokasi, kita cari di deskripsi atau nama
    $query .= " AND (nama_kamar LIKE ? OR deskripsi LIKE ?)";
    $likeLokasi = "%" . $lokasi . "%";
    $params[] = $likeLokasi;
    $params[] = $likeLokasi;
    $types .= "ss";
}

if ($tipe && $tipe !== 'Pilih Tipe...') {
    $query .= " AND nama_kamar LIKE ?";
    $likeTipe = "%" . $tipe . "%";
    $params[] = $likeTipe;
    $types .= "s";
}

$query .= " ORDER BY id DESC";

$stmt_kamar = $conn->prepare($query);
if ($stmt_kamar && $types !== "") {
    $stmt_kamar->bind_param($types, ...$params);
}
if ($stmt_kamar) {
    $stmt_kamar->execute();
    $result_kamar = $stmt_kamar->get_result();
} else {
    $result_kamar = false;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking - Homestay Bali</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="../index.html">HOMESTAY BALI</a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link" href="../index.html">Home</a></li>
                <li class="nav-item"><a class="nav-link active" href="booking.php">Booking</a></li>
                <?php if(isset($_SESSION['username'])): ?>
                    <li class="nav-item"><a class="nav-link" href="profil.php">Profil</a></li>
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
    <?php if ($lokasi || ($tipe && $tipe !== 'Pilih Tipe...')): ?>
        <div class="alert alert-info">
            <strong>Hasil Pencarian:</strong>
            <?php if ($lokasi) echo 'Kata Kunci/Lokasi: ' . htmlspecialchars($lokasi) . '. '; ?>
            <?php if ($tipe && $tipe !== 'Pilih Tipe...') echo 'Tipe: ' . htmlspecialchars($tipe) . '.'; ?>
            <a href="booking.php" class="alert-link ms-2">Reset Filter</a>
        </div>
    <?php endif; ?>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold m-0">Tersedia untuk Anda</h2>
    </div>
    
    <div class="row g-4">
        <?php 
        if ($result_kamar && $result_kamar->num_rows > 0): 
            while($k = $result_kamar->fetch_assoc()):
                $foto_url = !empty($k['foto']) ? htmlspecialchars($k['foto']) : 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2';
        ?>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <img src="<?= $foto_url ?>" class="card-img-top" alt="Kamar" style="height: 250px; object-fit: cover;" onerror="this.src='https://images.unsplash.com/photo-1560448204-e02f11c3d0e2'">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fw-bold"><?= htmlspecialchars($k['nama_kamar']) ?></h5>
                    <p class="card-text text-muted small flex-grow-1"><?= htmlspecialchars(substr($k['deskripsi'] ?? 'Fasilitas terbaik untuk kenyamanan Anda.', 0, 100)) ?>...</p>
                    <h5 class="text-primary mb-3">Rp <?= number_format($k['harga'], 0, ',', '.') ?> <small class="text-muted fs-6">/ malam</small></h5>
                    
                    <?php if (isset($_SESSION['username'])): ?>
                        <button class="btn btn-primary w-100 fw-bold" data-bs-toggle="modal" data-bs-target="#modalBooking<?= $k['id'] ?>">Pesan Sekarang</button>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline-primary w-100 fw-bold">Login untuk Memesan</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Modal Booking -->
        <div class="modal fade" id="modalBooking<?= $k['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold">Pesan <?= htmlspecialchars($k['nama_kamar']) ?></h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="">
                        <div class="modal-body">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                            <input type="hidden" name="kamar_id" value="<?= $k['id'] ?>">
                            
                            <div class="alert alert-info py-2 small">
                                Harga per malam: <strong>Rp <?= number_format($k['harga'], 0, ',', '.') ?></strong>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Tanggal Check-in</label>
                                <input type="date" name="checkin" class="form-control" required min="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tanggal Check-out</label>
                                <input type="date" name="checkout" class="form-control" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" name="book_kamar" class="btn btn-primary fw-bold">Konfirmasi Pemesanan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php 
            endwhile;
        else: 
        ?>
            <div class="col-12 text-center py-5">
                <div class="text-muted mb-3"><i class="bi bi-search" style="font-size: 3rem;"></i></div>
                <h5>Tidak ada kamar yang cocok dengan pencarian Anda.</h5>
                <p>Coba gunakan kata kunci yang berbeda atau reset filter pencarian.</p>
                <a href="booking.php" class="btn btn-outline-primary mt-2">Lihat Semua Kamar</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>