<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Endpoint Python untuk mendapatkan profil
$get_profile_url = "http://localhost:5000/get_profile";
$update_profile_url = "http://localhost:5000/update_profile";

// Ambil data profil dari Python
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $get_profile_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['role' => $role, 'username' => $username]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);

$response = curl_exec($ch);
curl_close($ch);

$profile = json_decode($response, true);

if (!$profile || isset($profile['error'])) {
    echo '<p>Profil tidak ditemukan atau terjadi kesalahan.</p>';
    exit();
}

// Tangani form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updatedData = [
        'username' => $_POST['username'],
        'email' => $_POST['email'],
        'password' => !empty($_POST['new_password']) ? $_POST['new_password'] : $profile['password'],
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $update_profile_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'role' => $role,
        'id' => $profile['id'],
        'data' => $updatedData
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);

    if (isset($result['error'])) {
        $error = $result['error'];
    } else {
        $success = "Profil berhasil diperbarui.";
        $_SESSION['username'] = $updatedData['username'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil</title>
    <link rel="icon" type="image/x-icon" href="css/img/sensi.png">
    <link rel="stylesheet" href="css/styleEditProfil.css">
</head>
<body>
<?php require 'header.php'; ?>
<main>
    <div class="container">
        <div class="left">
            <a class="select" href="dashboard.php">Dashboard</a>
            <a class="select" href="statistikAbsensi.php">Statistik Absensi</a>
            <a class="option" href="rekapAbsensi.php">&bull; Rekap Absensi</a>
            <a class="option" href="subPembahasan.php">&bull; Sub Pembahasan</a>
            <a class="select" href="daftarPengguna.php">Daftar Pengguna</a>
            <a class="option" href="daftarDosen.php">&bull; Dosen</a>
            <a class="option" href="daftarMaha.php">&bull; Mahasiswa</a>
            <a class="option" href="daftarAdmin.php">&bull; Admin</a>
        </div>
        <div class="right">
            <div class="content">
                <h3>Edit Profil</h3>
                <?php if (isset($error)): ?>
                    <p style="color: red;"> <?= htmlspecialchars($error) ?> </p>
                <?php endif; ?>
                <?php if (isset($success)): ?>
                    <p style="color: green;"> <?= htmlspecialchars($success) ?> </p>
                <?php endif; ?>

                <div class="content-profile">
                    <img src="css/img/blank-profile-picture-973460_960_720.webp" alt="Profile Picture">
                    <form action="" method="post">
                        <div class="form">
                            <div class="label">
                                <label for="">Nama Lengkap</label>
                                <label for="">Alamat Email</label>
                                <label for="">Kata Sandi Lama</label>
                                <label for="">Kata Sandi Baru</label>
                            </div>
                            <div class="input">
                                <input type="text" name="username" value="<?= htmlspecialchars($profile['username']) ?>" required>
                                <input type="email" name="email" value="<?= htmlspecialchars($profile['email']) ?>" required>
                                <input type="password" name="old_password" placeholder="Masukkan kata sandi lama" required>
                                <input type="password" name="new_password" placeholder="Masukkan kata sandi baru">
                            </div>
                        </div>
                        <button type="submit">Perbarui Profil</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
</body>
</html>
