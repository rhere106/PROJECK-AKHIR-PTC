<?php
// Fungsi untuk menambahkan pengumuman ke Flask
function addAnnouncement($text) {
    $flask_url = 'http://127.0.0.1:5000/add_announcement';
    $data = json_encode(['text' => $text]);

    $ch = curl_init($flask_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $response = curl_exec($ch);
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $http_status === 200;
}
// Jika ada permintaan POST untuk menambahkan pengumuman
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['text'])) {
    $text = $_POST['text']; // Ambil teks dari form
    if (empty($text)) {
        $error_message = "Teks tidak boleh kosong.";
    } else {
        if (addAnnouncement($text)) {
            header("Location: dashboard.php"); // Redirect setelah berhasil
            exit;
        } else {
            $error_message = "Gagal menambahkan pengumuman.";
        }
    }
}


// Fungsi untuk mengambil pengumuman dari Flask
function getAnnouncements() {
    $flask_url = 'http://127.0.0.1:5000/get_announcements';
    $ch = curl_init($flask_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true) ?? [];
}

// Fungsi untuk menghapus pengumuman
function deleteAnnouncement($id) {
    $flask_url = "http://127.0.0.1:5000/delete_announcement/$id";

    $ch = curl_init($flask_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

    $response = curl_exec($ch);
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $http_status === 200;
}

// Jika ada permintaan POST untuk menghapus pengumuman
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $id = $_POST['delete_id']; // Ambil ID dari form
    if (deleteAnnouncement($id)) {
        header("Location: dashboard.php"); // Redirect setelah berhasil
        exit;
    } else {
        $error_message = "Gagal menghapus pengumuman.";
    }
}

// Ambil pengumuman untuk ditampilkan
$announcements = getAnnouncements();

require 'D..M..Header.php';
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENSI</title>
    <link rel="icon" type="image/x-icon" href="css/img/sensi.png">
    <link rel="stylesheet" href="css/styleDashboard.css">
</head>
<body>
    <main>
        <div class="container">
            <div class="left">
                <a class="select" href="D..M..dasbor.php">Dashboard</a>
                <a class="select" href="D..M..statik.php">Statistik Absensi</a>
                <a class="option" href="D..M..Rekap.php">• Rekap Absensi</a>
                <a class="option" href="D..M..SubAbsen.php">• Sub Pembahasan</a>
                <a class="select" href="D..M..Pengguna.php">Daftar Pengguna</a>
                <a class="option" href="D..M..Dosen.php">• Dosen</a>
                <a class="option" href="D..M..Maha.php">• Mahasiswa</a>
            </div>
            <div class="right">
                <h3>PENGUMUMAN</h3>
                <div class="container-pengumuman">
                    <div class="slider" id="announcementContainer">
                        <?php
                        if ($announcements) {
                            foreach ($announcements as $announcement) {
                                echo "<div class='content'>" . htmlspecialchars($announcement['text']) . "</div>";
                            }
                        } else {
                            echo "<div class='content'>Tidak ada pengumuman.</div>";
                        }
                        ?>
                    </div>
                </div>
                <div class="button">
                    <div class="button-1">
                        <form method="POST" style="display: inline;">
                            <?php if (!empty($announcements)) : ?>
                                <input type="hidden" name="delete_id" value="<?php echo htmlspecialchars($announcements[0]['id']); ?>">
                                <!-- <button type="submit" class="del">Hapus</button> -->
                            <?php else : ?>
                                <button type="button" class="del" disabled>Tidak Ada Pengumuman</button>
                            <?php endif; ?>
                        </form>
                        <!-- <button class="info" id="addAnnouncementButton">Tambahkan Info +</button> -->
                    </div>

                    <div class="button-2">
                        <a href="" class="prev"><i class="fa-solid fa-left-long"></i> Prev</a>
                        <a href="" class="next">Next<i class="fa-solid fa-right-long"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </main>

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
    const popupForm = document.getElementById('popupForm');
    const addButton = document.getElementById('addAnnouncementButton');
    const cancelButton = document.getElementById('cancelButton');
    const deleteButton = document.querySelector('.del');
    const slider = document.getElementById('announcementContainer');

    addButton.addEventListener('click', () => popupForm.style.display = 'block');
    cancelButton.addEventListener('click', () => popupForm.style.display = 'none');

    // Fungsi untuk menghapus pengumuman pertama di slider
    deleteButton.addEventListener('click', async (event) => {
        event.preventDefault();

        const firstAnnouncement = slider.querySelector('.content');
        if (!firstAnnouncement) {
            alert('Tidak ada pengumuman untuk dihapus.');
            return;
        }

        const announcementId = firstAnnouncement.getAttribute('data-id');
        if (!announcementId) {
            alert('ID pengumuman tidak ditemukan.');
            return;
        }

        const confirmDelete = confirm('Apakah Anda yakin ingin menghapus pengumuman pertama?');
        if (!confirmDelete) return;

        // Kirim permintaan DELETE ke Flask
        const response = await fetch(`/delete_announcement/${announcementId}`, {
            method: 'DELETE',
        });

        if (response.ok) {
            alert('Pengumuman berhasil dihapus.');
            firstAnnouncement.remove(); // Hapus elemen dari DOM
        } else {
            alert('Gagal menghapus pengumuman.');
        }
    });
</script>

</body>
</html>
