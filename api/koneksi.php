<?php
// Database Connection Configuration
$host = "gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com";
$port = 4000;
$user = "3xHv9xaRno2ynL7.root";
$pass = "0MNShcmHrXByfywt";
$db   = "booking_homestay";

// TiDB Cloud Serverless REQUIRES SSL — buat objek mysqli dulu sebelum connect
$conn = mysqli_init();

if (!$conn) {
    die("Inisialisasi mysqli gagal.");
}

// Aktifkan SSL (MYSQLI_CLIENT_SSL)
// TiDB Cloud menggunakan sertifikat publik yang valid, jadi tidak perlu file CA lokal.
// Cukup set flag SSL agar koneksi terenkripsi.
mysqli_ssl_set($conn, null, null, null, null, null);

// Connect dengan port yang benar menggunakan MYSQLI_CLIENT_SSL, tanpa menyebutkan DB
$connected = mysqli_real_connect(
    $conn,
    $host,
    $user,
    $pass,
    null, // Jangan langsung pilih DB
    $port,
    null,
    MYSQLI_CLIENT_SSL
);

// Check connection
if (!$connected) {
    error_log("Database Connection Error: " . mysqli_connect_error(), 0);
    die(json_encode([
        "error" => "Koneksi ke database gagal.",
        "detail" => mysqli_connect_error()
    ]));
}

// Create DB if not exists
mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS `$db`");
mysqli_select_db($conn, $db);

// Create table users if not exists
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'user',
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    no_hp VARCHAR(20) NOT NULL
)");

// Create table kamar if not exists
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS kamar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kamar VARCHAR(100) NOT NULL,
    harga INT NOT NULL,
    deskripsi TEXT,
    foto VARCHAR(255)
)");

// Create table pesanan if not exists
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS pesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    kamar_id INT NOT NULL,
    tanggal_pesan DATETIME DEFAULT CURRENT_TIMESTAMP,
    tanggal_checkin DATE,
    tanggal_checkout DATE,
    total_harga INT NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    FOREIGN KEY (kamar_id) REFERENCES kamar(id) ON DELETE CASCADE,
    FOREIGN KEY (username) REFERENCES users(username) ON DELETE CASCADE
)");

// Set charset to utf8mb4
mysqli_set_charset($conn, "utf8mb4");

// Create table sessions if not exists
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(128) PRIMARY KEY,
    data TEXT NOT NULL,
    last_access INT UNSIGNED NOT NULL
)");

// Custom session handler using the existing connection
class DBSessionHandler implements SessionHandlerInterface {
    private $conn;
    public function __construct($conn) {
        $this->conn = $conn;
    }
    public function open(string $path, string $name): bool { return true; }
    public function close(): bool { return true; }
    
    #[\ReturnTypeWillChange]
    public function read(string $id) {
        $stmt = $this->conn->prepare("SELECT data FROM sessions WHERE id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            return $row['data'];
        }
        return "";
    }
    public function write(string $id, string $data): bool {
        $time = time();
        $stmt = $this->conn->prepare("REPLACE INTO sessions (id, data, last_access) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $id, $data, $time);
        return $stmt->execute();
    }
    public function destroy(string $id): bool {
        $stmt = $this->conn->prepare("DELETE FROM sessions WHERE id = ?");
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }
    
    #[\ReturnTypeWillChange]
    public function gc(int $max_lifetime) {
        $old = time() - $max_lifetime;
        $stmt = $this->conn->prepare("DELETE FROM sessions WHERE last_access < ?");
        $stmt->bind_param("i", $old);
        return $stmt->execute();
    }
}

$handler = new DBSessionHandler($conn);
session_set_save_handler($handler, true);
session_start();
?>