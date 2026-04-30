<?php
session_start();
require_once __DIR__ . '/koneksi.php';

if (!$conn) {
    die('Error: Database connection failed. Please check koneksi.php');
}

// 1. PROTEKSI HALAMAN: Hanya Admin yang boleh masuk
if (!isset($_SESSION['username']) || $_SESSION['role'] != "admin") {
    header("Location: login.php");
    exit();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}

// 2. LOGIKA TAMBAH KAMAR
if (isset($_POST['tambah_kamar'])) {
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        echo "<script>alert('Token CSRF tidak valid. Silakan muat ulang halaman.');</script>";
    } else {
        $nama = trim($_POST['nama_kamar'] ?? '');
    $harga = isset($_POST['harga']) ? (int)$_POST['harga'] : 0;
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $foto = trim($_POST['foto'] ?? '');

    if (empty($nama) || $harga <= 0) {
        echo "<script>alert('Nama kamar dan harga harus diisi dengan benar!');</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO kamar (nama_kamar, harga, deskripsi, foto) VALUES (?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("siss", $nama, $harga, $deskripsi, $foto);
            if ($stmt->execute()) {
                echo "<script>alert('Kamar berhasil ditambahkan!'); window.location='dashboard.admin.php';</script>";
            } else {
                echo "<script>alert('Gagal menambah kamar: " . addslashes($stmt->error) . "');</script>";
            }
            $stmt->close();
        }
    }
    }
}

// 3. LOGIKA HAPUS KAMAR
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    if (empty($_GET['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_GET['csrf_token'])) {
        echo "<script>alert('Token CSRF tidak valid.'); window.location='get_data.php';</script>";
    } else {
        if ($id > 0) {
            $stmt = $conn->prepare("DELETE FROM kamar WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                header("Location: dashboard.admin.php");
                exit();
            }
            $stmt->close();
        }
    }
    }
}

// 4. LOGIKA AMBIL DATA API BPS (Sensus/Data Wisatawan)
$apiUrl = "https://webapi.bps.go.id/v1/api/list/model/data/lang/ind/domain/0000/var/331/th/114/key/f1b206fd07552deedbb288944bb624c9";
$apiData = null;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Bypass SSL Localhost
$response = curl_exec($ch);
curl_close($ch);

if ($response) {
    $apiData = json_decode($response, true);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - Homestay Bali</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7fa; }
        .sidebar { height: 100vh; background: #212529; color: white; position: fixed; width: 250px; }
        .main-content { margin-left: 250px; padding: 30px; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .nav-link { color: #adb5bd; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { color: white; background: #343a40; border-radius: 8px; }
    </style>
</head>
<body>

<div class="sidebar d-flex flex-column p-3">
    <h4 class="text-center fw-bold text-primary mb-4">ADMIN PANEL</h4>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item"><a href="#" class="nav-link active mb-2"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
        <li><a href="booking.php" class="nav-link mb-2"><i class="bi bi-calendar-check me-2"></i> Pesanan</a></li>
        <li><a href="#" class="nav-link mb-2"><i class="bi bi-people me-2"></i> Pengguna</a></li>
        <li><hr class="border-secondary"></li>
        <li><a href="logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
    </ul>
    <div class="small text-secondary">Login sebagai: <strong><?= $_SESSION['username'] ?></strong></div>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Ringkasan Homestay</h2>
        <span class="badge bg-primary px-3 py-2">Update: <?= date('d M Y') ?></span>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card p-3 bg-white text-dark">
                <div class="d-flex align-items-center">
                    <div class="bg-primary-subtle p-3 rounded-circle me-3"><i class="bi bi-house-door text-primary fs-4"></i></div>
                    <div>
                        <p class="text-muted small mb-0">Total Kamar</p>
                        <?php
                        $stmt_count = $conn->prepare("SELECT COUNT(*) as total FROM kamar");
                        if ($stmt_count) {
                            $stmt_count->execute();
                            $result_count = $stmt_count->get_result();
                            $row_count = $result_count->fetch_assoc();
                            echo "<h4 class=\"fw-bold mb-0\">{$row_count['total']}</h4>";
                            $stmt_count->close();
                        } else {
                            echo "<h4 class=\"fw-bold mb-0\">0</h4>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="bg-success-subtle p-3 rounded-circle me-3"><i class="bi bi-graph-up-arrow text-success fs-4"></i></div>
                    <div>
                        <p class="text-muted small mb-0">Okupansi BPS</p>
                        <h4 class="fw-bold mb-0"><?= $apiData['data'][0]['val'] ?? '0' ?>%</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card p-4">
                <h5 class="fw-bold mb-4">Kelola Kamar Baru</h5>
                <form action="" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <div class="mb-3">
                        <label class="form-label small">Nama Kamar</label>
                        <input type="text" name="nama_kamar" class="form-control" placeholder="E.g: Suite Room" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Harga / Malam (Rp)</label>
                        <input type="number" name="harga" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">URL Foto Kamar</label>
                        <input type="text" name="foto" class="form-control" placeholder="https://image-url.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Deskripsi Singkat</label>
                        <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                    </div>
                    <button type="submit" name="tambah_kamar" class="btn btn-primary w-100 fw-bold py-2 shadow-sm">Simpan Kamar</button>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card p-4">
                <h5 class="fw-bold mb-4">Daftar Kamar Homestay</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Kamar</th>
                                <th>Harga</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt_kamar = $conn->prepare("SELECT * FROM kamar ORDER BY id DESC");
                            if ($stmt_kamar) {
                                $stmt_kamar->execute();
                                $result_kamar = $stmt_kamar->get_result();
                                if ($result_kamar && mysqli_num_rows($result_kamar) > 0) {
                                    while($k = $result_kamar->fetch_assoc()):  
                                    ?>
                            <tr>
                                <td class="fw-semibold text-primary"><?= htmlspecialchars($k['nama_kamar']) ?></td>
                                <td>Rp <?= number_format($k['harga'], 0, ',', '.') ?></td>
                                <td class="text-center">
                                    <a href="edit_kamar.php?id=<?= $k['id'] ?>" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="get_data.php?hapus=<?= $k['id'] ?>&csrf_token=<?= $_SESSION['csrf_token'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin hapus kamar ini?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12">
            <div class="card p-4 border-start border-primary border-4">
                <h5 class="fw-bold"><i class="bi bi-info-circle me-2"></i> Data Referensi BPS (API Nasional)</h5>
                <p class="text-muted small">Data ini diambil langsung dari server BPS menggunakan API.</p>
                
                <?php if ($apiData && isset($apiData['data'])): ?>
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered table-sm">
                            <thead class="table-dark">
                                <tr>
                                    <th>Tahun/Variabel</th>
                                    <th>Nilai / Persentase</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($apiData['data'], 0, 5) as $item): ?>
                                    <tr>
                                        <td>Data BPS Seri ID <?= $item['vervar'] ?></td>
                                        <td class="fw-bold text-success"><?= $item['val'] ?>%</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning py-2">Gagal memuat data BPS. Pastikan koneksi internet aktif.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>