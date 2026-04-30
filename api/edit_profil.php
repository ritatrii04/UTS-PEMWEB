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

$username = $_SESSION['username'];
$success = '';
$error = '';
$user = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');

    // Validate inputs
    if (empty($nama) || empty($email)) {
        $error = 'Nama dan email harus diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } else {
        $stmt = $conn->prepare("UPDATE users SET nama = ?, email = ?, no_hp = ? WHERE username = ?");
        if ($stmt) {
            $stmt->bind_param("ssss", $nama, $email, $no_hp, $username);
            if ($stmt->execute()) {
                $success = 'Profil berhasil diperbarui.';
            } else {
                $error = 'Terjadi kesalahan saat menyimpan perubahan.';
            }
            $stmt->close();
        } else {
            $error = 'Error prepare statement: ' . $conn->error;
        }
    }
}

// ambil data user dari database menggunakan prepared statement
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
if ($stmt) {
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    if (!$user) {
        die("User tidak ditemukan di database!");
    }
} else {
    die("Error prepare statement: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Profil - Homestay Bali</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="profil.php">HOMESTAY BALI</a>
        <a href="profil.php" class="btn btn-light btn-sm">Kembali</a>
    </div>
</nav>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm p-4">
                <h3>Edit Profil</h3>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($user['nama']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nomor HP</label>
                        <input type="text" name="no_hp" class="form-control" value="<?= htmlspecialchars($user['no_hp']) ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mb-2">Simpan Perubahan</button>
                    <a href="profil.php" class="btn btn-light w-100">Kembali ke Profil</a>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
