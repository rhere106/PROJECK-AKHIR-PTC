<?php
// Ambil data dari form
$username = $_POST['username'];
$password = $_POST['password'];

// Validasi input
if (empty($username) || empty($password)) {
    header("Location: login.php?error=Username dan password wajib diisi!");
    exit();
}

// Inisialisasi cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:5000/login");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['username' => $username, 'password' => $password]));

// Kirim permintaan ke Flask
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Decode respons JSON dari Flask
$data = json_decode($response, true);

// Proses respons
if ($http_code == 200) {
    // Login berhasil
    session_start();
    $_SESSION['username'] = $data['username'];
    $_SESSION['role'] = $data['role'];

    // Redirect ke dashboard sesuai role
    if ($data['role'] == 'admin') {
        header("Location: dashboard.php");
    } else {
        header("Location: D..M..dasbor.php");
    }
    exit();
} else {
    // Login gagal
    $error = $data['error'] ?? 'Terjadi kesalahan.';
    header("Location: login.php?error=" . urlencode($error));
    exit();
}
