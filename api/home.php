<?php
require_once __DIR__ . '/koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Homestay Bali</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: white; color: black; }
        .hero {
            background: url("https://images.unsplash.com/photo-1507525428034-b723cf961d3e");
            background-size: cover;
            background-position: center;
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .search-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="home.php">HOMESTAY BALI</a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link active" href="home.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="booking.php">Booking</a></li>
                <li class="nav-item"><a class="nav-link" href="profil.php">Profil</a></li>
                <li class="nav-item"><a class="nav-link" href="promo.php">Promo</a></li>
           
                
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

<div class="hero">
    <div class="search-box text-center container">
        <h2 class="mb-4">Cari Homestay Terbaik di Bali</h2>
        <div class="row g-2">
            <div class="col-md-3"><input type="text" class="form-control" placeholder="Lokasi"></div>
            <div class="col-md-3"><input type="date" class="form-control"></div>
            <div class="col-md-3"><input type="date" class="form-control"></div>
            <div class="col-md-2">
                <select class="form-control">
                    <option>1 Tamu</option><option>2 Tamu</option><option>3 Tamu</option>
                </select>
            </div>
            <div class="col-md-1"><button class="btn btn-warning w-100">Cari</button></div>
        </div>
    </div>
</div>

<div class="container mt-5 mb-5">
    <h3 class="text-center mb-4">Pilihan Kamar Homestay</h3>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <img src="https://images.unsplash.com/photo-1505691938895-1758d7feb511" class="card-img-top" alt="Standard">
                <div class="card-body text-center">
                    <h5>Standard Room</h5>
                    <p class="text-primary fw-bold">Rp 250.000 / malam</p>
                    <a href="booking.php" class="btn btn-warning w-100">Pesan Sekarang</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <img src="https://images.unsplash.com/photo-1560185893-a55cbc8c57e8" class="card-img-top" alt="Deluxe">
                <div class="card-body text-center">
                    <h5>Deluxe Room</h5>
                    <p class="text-primary fw-bold">Rp 400.000 / malam</p>
                    <a href="booking.php" class="btn btn-warning w-100">Pesan Sekarang</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <img src="https://images.unsplash.com/photo-1560448075-bb485b067938" class="card-img-top" alt="Family">
                <div class="card-body text-center">
                    <h5>Family Room</h5>
                    <p class="text-primary fw-bold">Rp 550.000 / malam</p>
                    <a href="booking.php" class="btn btn-warning w-100">Pesan Sekarang</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>