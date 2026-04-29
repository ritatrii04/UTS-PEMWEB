<?php
include 'koneksi.php';

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = 'user';

    // 1. Cek apakah password dan konfirmasi password cocok
    if ($password !== $confirm_password) {
        echo "<script>alert('Konfirmasi password tidak cocok!'); window.location='register.php';</script>";
        exit();
    }

    // 2. Cek apakah username sudah ada di database
    $check_user = $conn->prepare("SELECT username FROM user WHERE username = ?");
    $check_user->bind_param("s", $username);
    $check_user->execute();
    $result = $check_user->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Username sudah digunakan! Silakan pilih yang lain.'); window.location='register.php';</script>";
    } else {
        // 3. Enkripsi Password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // 4. Masukkan data
        $stmt = $conn->prepare("INSERT INTO user (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $hashed_password, $role);

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