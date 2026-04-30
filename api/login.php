<?php 
session_start();
require_once __DIR__ . '/koneksi.php';

// Cegah user yang sudah login kembali ke halaman login
if (isset($_SESSION['username'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: dashboard.admin.php");
    } else {
        header("Location: dashboard_user.php");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Homestay Bali</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f7f6; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-box { background: white; padding: 40px; border-radius: 15px; width: 400px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
<div class="login-box text-center">
    <h3 class="fw-bold mb-4 text-primary">Login Homestay</h3>
    <form action="proses_login.php" method="POST">
        <div class="mb-3 text-start">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" placeholder="Masukkan Username" required>
        </div>
        <div class="mb-3 text-start">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Masukkan Password" required>
        </div>
        
        <button type="submit" name="login" class="btn btn-warning w-100 fw-bold py-2 mb-3">MASUK</button>
    </form>
    
    <div class="text-center">
        <p class="small mb-0">Belum punya akun? <a href="register.php" class="text-decoration-none fw-bold text-primary">Daftar Akun Baru</a></p>
        <a href="../index.html" class="text-decoration-none text-muted small mt-2 d-block">← Kembali ke Beranda</a>
    </div>
</div>
</body>
</html>