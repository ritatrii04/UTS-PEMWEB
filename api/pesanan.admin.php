<?php
require_once __DIR__ . '/koneksi.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != "admin") {
    header("Location: login.php");
    exit();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}

// Update status pesanan
if (isset($_GET['action']) && isset($_GET['id']) && isset($_GET['csrf_token'])) {
    if (!hash_equals($_SESSION['csrf_token'], $_GET['csrf_token'])) {
        echo "<script>alert('Token CSRF tidak valid.'); window.location='pesanan.admin.php';</script>";
    } else {
        $id = (int)$_GET['id'];
        $action = $_GET['action'];
        
        $new_status = 'pending';
        if ($action == 'confirm') $new_status = 'confirmed';
        elseif ($action == 'cancel') $new_status = 'cancelled';
        
        if ($id > 0 && in_array($new_status, ['confirmed', 'cancelled', 'pending'])) {
            $stmt = $conn->prepare("UPDATE pesanan SET status = ? WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param("si", $new_status, $id);
                $stmt->execute();
                $stmt->close();
                header("Location: pesanan.admin.php");
                exit();
            }
        }
    }
}

// Hapus pesanan
if (isset($_GET['delete']) && isset($_GET['csrf_token'])) {
    if (!hash_equals($_SESSION['csrf_token'], $_GET['csrf_token'])) {
        echo "<script>alert('Token CSRF tidak valid.'); window.location='pesanan.admin.php';</script>";
    } else {
        $id = (int)$_GET['delete'];
        if ($id > 0) {
            $stmt = $conn->prepare("DELETE FROM pesanan WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $stmt->close();
                header("Location: pesanan.admin.php");
                exit();
            }
        }
    }
}

// Ambil semua data pesanan
$query = "SELECT p.*, k.nama_kamar, u.nama, u.no_hp 
          FROM pesanan p 
          JOIN kamar k ON p.kamar_id = k.id 
          JOIN users u ON p.username = u.username 
          ORDER BY p.tanggal_pesan DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Pesanan - Admin Homestay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; }
        .navbar-brand { font-weight: 700; letter-spacing: 1px; }
        .card { border-radius: 12px; }
        .table th { background-color: #343a40; color: white; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark mb-4 shadow">
    <div class="container">
        <a class="navbar-brand" href="dashboard.admin.php"><i class="fas fa-home me-2"></i>Admin Panel Homestay</a>
        <div>
            <a href="dashboard.admin.php" class="btn btn-outline-light btn-sm me-2">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">
                <i class="fas fa-sign-out-alt me-1"></i>Logout
            </a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="card shadow-sm p-4 mb-4">
        <h4 class="fw-bold text-primary mb-4"><i class="fas fa-calendar-check me-2"></i>Daftar Semua Pesanan</h4>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Pemesan</th>
                        <th>Kamar</th>
                        <th>Tgl Check-in</th>
                        <th>Tgl Check-out</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if ($result && $result->num_rows > 0): 
                        $no = 1;
                        while ($row = $result->fetch_assoc()):
                            $status_badge = '<span class="badge bg-warning text-dark">Pending</span>';
                            if ($row['status'] == 'confirmed') $status_badge = '<span class="badge bg-success">Terkonfirmasi</span>';
                            elseif ($row['status'] == 'cancelled') $status_badge = '<span class="badge bg-danger">Dibatalkan</span>';
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>
                            <strong><?= htmlspecialchars($row['nama']) ?></strong><br>
                            <small class="text-muted"><i class="fas fa-phone me-1"></i><?= htmlspecialchars($row['no_hp']) ?></small>
                        </td>
                        <td><span class="text-primary fw-semibold"><?= htmlspecialchars($row['nama_kamar']) ?></span></td>
                        <td><?= date('d M Y', strtotime($row['tanggal_checkin'])) ?></td>
                        <td><?= date('d M Y', strtotime($row['tanggal_checkout'])) ?></td>
                        <td><strong>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></strong></td>
                        <td><?= $status_badge ?></td>
                        <td>
                            <div class="btn-group" role="group">
                                <?php if ($row['status'] != 'confirmed'): ?>
                                <a href="pesanan.admin.php?action=confirm&id=<?= $row['id'] ?>&csrf_token=<?= $_SESSION['csrf_token'] ?>" class="btn btn-sm btn-success" title="Konfirmasi">
                                    <i class="fas fa-check"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if ($row['status'] != 'cancelled'): ?>
                                <a href="pesanan.admin.php?action=cancel&id=<?= $row['id'] ?>&csrf_token=<?= $_SESSION['csrf_token'] ?>" class="btn btn-sm btn-warning" title="Batalkan">
                                    <i class="fas fa-times"></i>
                                </a>
                                <?php endif; ?>
                                
                                <a href="pesanan.admin.php?delete=<?= $row['id'] ?>&csrf_token=<?= $_SESSION['csrf_token'] ?>" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Yakin hapus data pesanan ini?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php 
                        endwhile; 
                    else:
                    ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">Belum ada pesanan yang masuk.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
