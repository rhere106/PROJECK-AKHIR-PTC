<?php

// Fungsi untuk menambahkan pengumuman ke Flask
function addAnnouncement($text) {
    $flask_url = 'http://127.0.0.1:5000/add_announcement'; // URL endpoint Flask untuk menambahkan pengumuman
    $data = json_encode(['text' => $text]); // Data yang akan dikirim dalam format JSON

    // Inisialisasi CURL
    $ch = curl_init($flask_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Mengembalikan hasil eksekusi
    curl_setopt($ch, CURLOPT_POST, true); // Metode POST
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']); // Header untuk JSON
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // Data yang dikirim

    // Eksekusi dan tangkap respons
    $response = curl_exec($ch);
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Status HTTP
    curl_close($ch); // Tutup CURL

    return $http_status === 200; // Kembalikan true jika berhasil
}

// Jika ada permintaan POST untuk menambahkan pengumuman
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['text'])) {
    $text = $_POST['text']; // Ambil teks dari form input
    if (empty($text)) {
        $error_message = "Teks tidak boleh kosong."; // Validasi input
    } else {
        if (addAnnouncement($text)) {
            header("Location: dashboard.php"); // Redirect ke halaman utama jika berhasil
            exit;
        } else {
            $error_message = "Gagal menambahkan pengumuman."; // Pesan error jika gagal
        }
    }
}

// Fungsi untuk mengambil pengumuman dari Flask
function getAnnouncements() {
    $flask_url = 'http://127.0.0.1:5000/get_announcements'; // URL endpoint Flask untuk mengambil pengumuman
    $ch = curl_init($flask_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Mengembalikan hasil eksekusi
    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true) ?? []; // Decode JSON ke array PHP
}

// Fungsi untuk menghapus pengumuman
function deleteAnnouncement($id) {
    $flask_url = "http://127.0.0.1:5000/delete_announcement/$id"; // URL endpoint Flask untuk menghapus pengumuman

    // Inisialisasi CURL
    $ch = curl_init($flask_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Mengembalikan hasil eksekusi
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE"); // Metode DELETE

    // Eksekusi dan tangkap respons
    $response = curl_exec($ch);
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Status HTTP
    curl_close($ch); // Tutup CURL

    return $http_status === 200; // Kembalikan true jika berhasil
}

// Jika ada permintaan POST untuk menghapus pengumuman
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $id = $_POST['delete_id']; // Ambil ID pengumuman dari form input
    if (deleteAnnouncement($id)) {
        header("Location: dashboard.php"); // Redirect ke halaman utama jika berhasil
        exit;
    } else {
        $error_message = "Gagal menghapus pengumuman."; // Pesan error jika gagal
    }
}

// Ambil daftar pengumuman untuk ditampilkan di halaman
$announcements = getAnnouncements();

require 'header.php'; // Memasukkan file header jika diperlukan
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENSI</title>
    <link rel="icon" type="image/x-icon" href="css/img/sensi.png">
    <link rel="stylesheet" href="css/styleDashhboard.css">
</head>
<body>
    <main>
        <div class="container">
            <!-- Sidebar kiri -->
            <div class="left">
                <a class="select" href="dashboard.php">Dashboard</a>
                <a class="select" href="statistikAbsensi.php">Statistik Absensi</a>
                <a class="option" href="rekapAbsensi.php">• Rekap Absensi</a>
                <a class="option" href="subPembahasan.php">• Sub Pembahasan</a>
                <a class="select" href="daftarPengguna.php">Daftar Pengguna</a>
                <a class="option" href="daftarDosen.php">• Dosen</a>
                <a class="option" href="daftarMaha.php">• Mahasiswa</a>
                <a class="option" href="daftarAdmin.php">• Admin</a>
            </div>
            <!-- Konten kanan -->
            <div class="right">
                <h3>PENGUMUMAN</h3>
                <div class="container-pengumuman">
                    <div class="slider" id="announcementContainer">
                        <!-- Tampilkan pengumuman -->
                        <?php
                        if ($announcements) {
                            foreach ($announcements as $announcement) {
                                echo "<div class='content' data-id='" . htmlspecialchars($announcement['id']) . "'>" . htmlspecialchars($announcement['text']) . "</div>";
                            }
                        } else {
                            echo "<div class='content'>Tidak ada pengumuman.</div>";
                        }
                        ?>
                    </div>
                </div>
                <div class="button">
                    <div class="button-1">
                        <!-- Tombol untuk menghapus pengumuman -->
                        <form method="POST" style="display: inline;">
                            <?php if (!empty($announcements)) : ?>
                                <input type="hidden" name="delete_id" value="<?php echo htmlspecialchars($announcements[0]['id']); ?>">
                                <button type="submit" class="del">Hapus</button>
                            <?php else : ?>
                                <button type="button" class="del" disabled>Tidak Ada Pengumuman</button>
                            <?php endif; ?>
                        </form>
                        <!-- Tombol untuk menambah pengumuman -->
                        <button class="info" id="addAnnouncementButton">Tambahkan Info +</button>
                    </div>
                    <div class="button-2">
                        <a href="" class="prev">Prev</a>
                        <a href="" class="next">Next</a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Form Pop-Up untuk Tambah Pengumuman -->
    <div class="popup" id="popupForm" style="display: none;">
        <div class="container">
            <h5>Tambah Pengumuman</h5>
            <?php if (!empty($error_message)): ?>
                <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
            <form method="POST">
                <textarea name="text" id="multiLineInput" placeholder="Ketik disini" required></textarea>
                <div class="button">
                    <button type="submit">Tambahkan</button>
                    <button type="button" id="cancelButton">Batalkan</button>
                </div>
            </form>
        </div>
    </div>
    <script src="scriptPopup.js"></script>
    <script src="scriptDashboard.js"></script>
    <script>
        // Variabel untuk elemen-elemen DOM
        const popupForm = document.getElementById('popupForm');
        const addButton = document.getElementById('addAnnouncementButton');
        const cancelButton = document.getElementById('cancelButton');

        // Menampilkan form pop-up
        addButton.addEventListener('click', () => popupForm.style.display = 'block');
        cancelButton.addEventListener('click', () => popupForm.style.display = 'none');
    </script>
</body>
</html>
