<?php 
require_once __DIR__ . '/koneksi.php';

if (!$conn) {
    die('Error: Database connection failed. Please check koneksi.php');
}

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Mencari user berdasarkan username
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Cek password 
        if ($password == $row['password'] || password_verify($password, $row['password'])) {
            $_SESSION['username'] = $row['username'];
            $_SESSION['role']     = $row['role']; 
            
            // Redirect sesuai ROLE
            if ($row['role'] == 'admin') {
                header("Location: dashboard.admin.php");
            } else {
                header("Location: dashboard_user.php");
            }
            exit();
        } else {
            echo "<script>alert('Password salah!'); window.location='login.php';</script>";
        }
    } else {
        echo "<script>alert('Username tidak ditemukan!'); window.location='login.php';</script>";
    }
} else {
    // Jika ada yang mencoba akses file ini langsung dari URL
    header("Location: login.php");
    exit();
}
?>