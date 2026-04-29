<?php
session_start();
require_once __DIR__ . '/koneksi.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != "admin") {
    header("Location: login.php");
    exit();
}

// --- LOGIKA MENGAMBIL DATA DARI API BPS ---
// FIX: Tambah opsi cURL agar bisa jalan di localhost (XAMPP) yang tidak punya SSL cert
$apiUrl = "https://webapi.bps.go.id/v1/api/list/model/data/lang/ind/domain/0000/var/331/th/114/key/f1b206fd07552deedbb288944bb624c9";
$apiData = null;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
// FIX: Tambah 2 baris ini agar cURL tidak error SSL di XAMPP localhost
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
$response = curl_exec($ch);
$curlError = curl_error($ch);
curl_close($ch);

if ($response) {
    $apiData = json_decode($response, true);
}
// ------------------------------------------

// LOGIKA TAMBAH KAMAR
if (isset($_POST['tambah_kamar'])) {
    $nama    = mysqli_real_escape_string($conn, $_POST['nama_kamar']);
    $harga   = (int) $_POST['harga'];
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $foto    = mysqli_real_escape_string($conn, $_POST['foto']);

    $query = "INSERT INTO kamar (nama_kamar, harga, deskripsi, foto) VALUES ('$nama', '$harga', '$deskripsi', '$foto')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Kamar berhasil ditambahkan!');</script>";
    } else {
        echo "<script>alert('Gagal menambah kamar: " . mysqli_error($conn) . "');</script>";
    }
}

// LOGIKA HAPUS KAMAR
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM kamar WHERE id = $id");
    header("Location: dashboard.admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Kelola Homestay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; }
        .navbar-brand { font-weight: 700; letter-spacing: 1px; }
        .card { border-radius: 12px; }
        .stat-badge {
            background: linear-gradient(135deg, #0d6efd, #0dcaf0);
            color: white;
            border-radius: 10px;
            padding: 12px 18px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .stat-badge .label { font-size: 0.85rem; opacity: 0.9; }
        .stat-badge .value { font-size: 1.3rem; font-weight: 700; }
        .table th { background-color: #343a40; color: white; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark mb-4 shadow">
    <div class="container">
        <a class="navbar-brand"><i class="fas fa-home me-2"></i>Admin Panel Homestay</a>
        <a href="logout.php" class="btn btn-outline-danger btn-sm">
            <i class="fas fa-sign-out-alt me-1"></i>Logout
        </a>
    </div>
</nav>

<div class="container">

    <!-- SECTION: Statistik BPS -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold text-primary"><i class="fas fa-chart-bar me-2"></i>Statistik BPS (Wisatawan)</h5>
                    <p class="text-muted small mb-3">Data otomatis dari WebAPI BPS — Variabel Wisatawan Mancanegara</p>

                    <?php if ($apiData && isset($apiData['data-array']) && count($apiData['data-array']) > 0): ?>
                        <div class="row">
                            <?php 
                            $count = 0;
                            foreach ($apiData['data-array'] as $item): 
                                if ($count >= 5) break;
                                $colors = ['primary','info','success','warning','secondary'];
                                $color  = $colors[$count % count($colors)];
                            ?>
                            <div class="col-md-4 mb-2">
                                <div class="stat-badge" style="background: linear-gradient(135deg, var(--bs-<?php echo $color; ?>), #0dcaf0)">
                                    <span class="label">Data ke-<?php echo $count + 1; ?></span>
                                    <span class="value"><?php echo number_format($item); ?></span>
                                </div>
                            </div>
                            <?php 
                                $count++;
                            endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Gagal memuat data dari API BPS.
                            <?php if (!empty($curlError)): ?>
                                <br><small class="text-muted">Error: <?php echo htmlspecialchars($curlError); ?></small>
                            <?php endif; ?>
                            <br><small>Pastikan koneksi internet aktif dan ekstensi <code>php_curl</code> aktif di <code>php.ini</code>.</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION: Form & Tabel Kamar -->
    <div class="row">

        <!-- Form Tambah Kamar -->
        <div class="col-md-5">
            <div class="card shadow-sm p-4 mb-4">
                <h5 class="fw-bold mb-3"><i class="fas fa-plus-circle me-2 text-success"></i>Tambah Kamar Baru</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nama Kamar</label>
                        <input type="text" name="nama_kamar" class="form-control" placeholder="Contoh: Suite Room" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga per Malam (Rp)</label>
                        <input type="number" name="harga" class="form-control" placeholder="Contoh: 500000" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Link Foto Kamar</label>
                        <input type="text" name="foto" class="form-control" placeholder="https://link-gambar.com/foto.jpg">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3" placeholder="Deskripsi singkat tentang kamar..."></textarea>
                    </div>
                    <button type="submit" name="tambah_kamar" class="btn btn-success w-100">
                        <i class="fas fa-save me-2"></i>Simpan Kamar
                    </button>
                </form>
            </div>
        </div>

        <!-- Tabel Daftar Kamar -->
        <div class="col-md-7">
            <div class="card shadow-sm p-4">
                <h5 class="fw-bold mb-3"><i class="fas fa-list me-2 text-primary"></i>Daftar Kamar Homestay</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Foto</th>
                                <th>Nama Kamar</th>
                                <th>Harga/Malam</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $tampil = mysqli_query($conn, "SELECT * FROM kamar ORDER BY id ASC");
                            $no = 1;
                            if (mysqli_num_rows($tampil) > 0) {
                                while ($k = mysqli_fetch_assoc($tampil)) {
                                    // FIX: Gunakan 'nama_kamar' bukan 'kamar'
                                    $foto_url = !empty($k['foto']) 
                                        ? htmlspecialchars($k['foto']) 
                                        : 'https://via.placeholder.com/50x40?text=No+Foto';
                                    echo "
                                    <tr>
                                        <td>{$no}</td>
                                        <td>
                                            <img src='{$foto_url}' 
                                                 alt='foto kamar' 
                                                 style='width:60px;height:45px;object-fit:cover;border-radius:6px;'
                                                 onerror=\"this.src='https://via.placeholder.com/60x45?text=Error'\">
                                        </td>
                                        <td><strong>" . htmlspecialchars($k['kamar']) . "</strong></td>
                                        <td>Rp " . number_format($k['harga'], 0, ',', '.') . "</td>
                                        <td>
                                            <a href='edit.kamar.php?id={$k['id']}' class='btn btn-sm btn-info text-white me-1'>
                                                <i class='fas fa-edit'></i> Edit
                                            </a>
                                            <a href='dashboard.admin.php?hapus={$k['id']}' 
                                               class='btn btn-sm btn-danger'
                                               onclick=\"return confirm('Yakin hapus kamar ini?')\">
                                                <i class='fas fa-trash'></i> Hapus
                                            </a>
                                        </td>
                                    </tr>";
                                    $no++;
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center text-muted'>Belum ada data kamar.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>