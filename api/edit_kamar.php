<?php
session_start();
require_once __DIR__ . '/koneksi.php';

if (!$conn) {
    die('Error: Database connection failed. Please check koneksi.php');
}

if (!isset($_SESSION['username']) || $_SESSION['role'] != "admin") {
    header("Location: login.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die("ID Kamar tidak valid!");
}

// Ambil data kamar
$stmt = $conn->prepare("SELECT * FROM kamar WHERE id = ?");
if ($stmt) {
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $kamar = $result->fetch_assoc();
    $stmt->close();

    if (!$kamar) {
        die("Kamar tidak ditemukan!");
    }
} else {
    die("Error prepare statement: " . $conn->error);
}

// Update kamar jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_kamar'])) {
    // CSRF check
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
        $error_msg = "Token CSRF tidak valid. Silakan muat ulang halaman.";
    } else {
        $nama = trim($_POST['nama_kamar'] ?? '');
    $harga = isset($_POST['harga']) ? (int)$_POST['harga'] : 0;
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $foto = trim($_POST['foto'] ?? '');

    if (empty($nama) || $harga <= 0) {
        $error_msg = "Nama kamar dan harga harus diisi dengan benar!";
    } else {
        $stmt = $conn->prepare("UPDATE kamar SET nama_kamar = ?, harga = ?, deskripsi = ?, foto = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("sissi", $nama, $harga, $deskripsi, $foto, $id);
            if ($stmt->execute()) {
                echo "<script>alert('Kamar berhasil diperbarui!'); window.location='dashboard.admin.php';</script>";
                exit();
            } else {
                $error_msg = "Gagal memperbarui kamar: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error_msg = "Error prepare statement: " . $conn->error;
        }
    }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Kamar - Homestay Bali</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.admin.php">HOMESTAY BALI</a>
        <a href="dashboard.admin.php" class="btn btn-light btn-sm">Kembali</a>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm p-4">
                <h3 class="fw-bold mb-4">Edit Kamar</h3>
                
                <?php if (isset($error_msg)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error_msg) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <div class="mb-3">
                        <label class="form-label">Nama Kamar</label>
                        <input type="text" name="nama_kamar" class="form-control" value="<?= htmlspecialchars($kamar['nama_kamar'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga per Malam (Rp)</label>
                        <input type="number" name="harga" class="form-control" value="<?= htmlspecialchars($kamar['harga'] ?? '') ?>" required min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">URL Foto Kamar</label>
                        <input type="text" name="foto" class="form-control" value="<?= htmlspecialchars($kamar['foto'] ?? '') ?>" placeholder="https://...">
                        <?php if (!empty($kamar['foto'])): ?>
                            <small class="text-muted">Preview:</small><br>
                            <img src="<?= htmlspecialchars($kamar['foto']) ?>" alt="preview" style="width: 150px; height: 100px; object-fit: cover; border-radius: 5px; margin-top: 5px;" onerror="this.src='https://via.placeholder.com/150x100?text=Error'">
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="4"><?= htmlspecialchars($kamar['deskripsi'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" name="update_kamar" class="btn btn-primary w-100">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
