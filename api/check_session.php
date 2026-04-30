<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');

if (isset($_SESSION['username'])) {
    echo json_encode([
        'loggedIn'  => true,
        'username'  => $_SESSION['username'],
        'role'      => $_SESSION['role']
    ]);
} else {
    echo json_encode(['loggedIn' => false]);
}
?>
