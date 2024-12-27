<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $username = $_POST['username'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $password = $_POST['password'];
    $nuptk = $_POST['nuptk'];
    $confirm_password = $_POST['confirm_password'];
    $homebase = $_POST['homebase'];
    $matakuliah = $_POST['matakuliah'];

    // Validasi password dan konfirmasi password
    if ($password !== $confirm_password) {
        $error_message = "Password dan konfirmasi password tidak cocok.";
    } else {
        // Kirim data ke Flask
        $flask_url = 'http://127.0.0.1:5000/register_dosen'; // URL Flask untuk register dosen
        $data = json_encode([
            'username' => $username,
            'email' => $email,
            'gender' => $gender,
            'password' => $password,
            'nuptk' => $nuptk,
            'homebase' => $homebase,
            'matakuliah' => $matakuliah
        ]);

        $ch = curl_init($flask_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_status === 200) {
            $success_message = "Dosen berhasil ditambahkan!";
        } else {
            $error_message = "Gagal menambahkan dosen.";
        }
    }
}
require 'header1.php';
require 'session_start.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENSI</title>
    <link rel="icon" type="image/x-icon" href="css/img/sensi.png">
    <link rel="stylesheet" href="css/styleResgisttDosen.css">
    <link
      rel="stylesheet"  
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
</head>
<body> 

    <main>
        <div class="container">
            <img src="css/img/sensi.png" alt="Logo SENSI">
            <h3>Buat Akun</h3>

            <?php if (!empty($success_message)): ?>
                <p style="color: green;"><?php echo $success_message; ?></p>
            <?php elseif (!empty($error_message)): ?>
                <p style="color: red;"><?php echo $error_message; ?></p>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="text" name="homebase" placeholder="Homebase" required>
                    <input type="text" name="matakuliah" placeholder="Matakuliah" required>
                </div>
                <div class="form-group">
                    <select name="gender" required>
                        <option value="" disabled selected>Gender</option>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <input type="text" name="nuptk" placeholder="NUPTK" required>
                    <input type="password" name="confirm_password" placeholder="Konfirmasi Password" required>
                </div>
                <button type="submit">Tambahkan Dosen</button>
            </form>
            <a href="daftarDosen.php" class="next"><i class="fa-solid fa-angles-left"></i></a>
        </div>
    </main>
</body>
</html>
