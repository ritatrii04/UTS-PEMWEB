<?php
require_once __DIR__ . '/koneksi.php';

if (!$conn) {
    die('Error: Database connection failed. Please check koneksi.php');
}

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $role = 'user';

    // 1. Cek apakah password dan konfirmasi password cocok
    if ($password !== $confirm_password) {
        echo "<script>alert('Konfirmasi password tidak cocok!'); window.location='register.php';</script>";
        exit();
    }

    // 2. Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Format email tidak valid!'); window.location='register.php';</script>";
        exit();
    }

    // 3. Validate password strength (min 6 chars)
    if (strlen($password) < 6) {
        echo "<script>alert('Password minimal harus 6 karakter!'); window.location='register.php';</script>";
        exit();
    }

    // 4. Cek apakah username sudah ada di database
    $check_user = $conn->prepare("SELECT username FROM users WHERE username = ?");
    $check_user->bind_param("s", $username);
    $check_user->execute();
    $result = $check_user->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Username sudah digunakan! Silakan pilih yang lain.'); window.location='register.php';</script>";
    } else {
        // 3. Enkripsi Password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // 4. Masukkan data
        $stmt = $conn->prepare("INSERT INTO users (username, password, role, nama, email, no_hp) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $username, $hashed_password, $role, $nama, $email, $no_hp);

        if ($stmt->execute()) {
            echo "<script>alert('Pendaftaran Berhasil! Silakan Login.'); window.location='login.php';</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan sistem.'); window.location='register.php';</script>";
        }
    }
} else {
    header("Location: register.php");
    exit();
}
?>