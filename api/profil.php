<?php
require_once __DIR__ . '/koneksi.php';

if (!$conn) {
    die('Error: Database connection failed. Please check koneksi.php');
}

// cek login user
if (!isset($_SESSION['username']) || $_SESSION['role'] != "user") {
    header("Location: login.php");
    exit();
}

// ambil data user dari database menggunakan prepared statement
$username = $_SESSION['username'];
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
    <title>Profil Saya - Homestay Bali</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard_user.php">HOMESTAY BALI</a>
        <a href="dashboard_user.php" class="btn btn-light btn-sm">Kembali</a>
    </div>
</nav>

<div class="container">
    <div class="row">

        <!-- FOTO PROFIL -->
        <div class="col-md-4">
            <div class="card text-center p-4 shadow-sm">
                <img src="default.png" class="rounded-circle mx-auto mb-3" width="120">
                
                <h4><?= htmlspecialchars($user['nama']); ?></h4>
                <p class="text-muted">Member Homestay</p>

                <button class="btn btn-outline-primary btn-sm">Ubah Foto</button>
            </div>
        </div>

        <!-- DATA USER -->
        <div class="col-md-8">
            <div class="card p-4 shadow-sm">
                <h3>Informasi Pribadi</h3>
                <hr>

                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Nama Lengkap</div>
                    <div class="col-sm-8 fw-bold">
                        <?= htmlspecialchars($user['nama']); ?>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Email</div>
                    <div class="col-sm-8 fw-bold">
                        <?= htmlspecialchars($user['email']); ?>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Nomor HP</div>
                    <div class="col-sm-8 fw-bold">
                        <?= htmlspecialchars($user['no_hp']); ?>
                    </div>
                </div>

                <a href="edit_profil.php" class="btn btn-primary w-25">
                    Edit Profil
                </a>
            </div>
        </div>

    </div>
</div>

</body>
</html>