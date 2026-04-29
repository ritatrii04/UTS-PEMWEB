<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Akun - Homestay Bali</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f7f6; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .register-box { background: white; padding: 40px; border-radius: 15px; width: 450px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
<div class="register-box">
    <h3 class="fw-bold mb-2 text-center text-primary">Daftar Akun</h3>
    <p class="text-muted text-center mb-4">Lengkapi data untuk memesan homestay</p>
    
    <form action="proses_register.php" method="POST">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" placeholder="Buat username" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Buat password" required>
        </div>
        <div class="mb-4">
            <label class="form-label">Konfirmasi Password</label>
            <input type="password" name="confirm_password" class="form-control" placeholder="Ulangi password" required>
        </div>
        <button type="submit" name="register" class="btn btn-primary w-100 fw-bold py-2 mb-3">DAFTAR SEKARANG</button>
    </form>
    
    <div class="text-center">
        <p class="small mb-0">Sudah punya akun? <a href="login.php" class="text-decoration-none fw-bold">Login di sini</a></p>
        <a href="home.php" class="text-decoration-none text-muted small mt-2 d-block">← Kembali ke Beranda</a>
    </div>
</div>
</body>
</html>