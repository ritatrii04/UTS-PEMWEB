<?php
session_start();
require_once __DIR__ . '/koneksi.php';

session_unset();
session_destroy();
header("Location: home.php");
exit();
?>