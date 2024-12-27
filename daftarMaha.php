<?php
// Mengambil data mahasiswa dari API Flask
$flask_api_url = "http://127.0.0.1:5000/get_mahasiswa"; // URL API Flask
$response = file_get_contents($flask_api_url);

// Cek apakah respons valid
if ($response === FALSE) {
    die('Gagal menghubungi API Flask');
}

// Decode JSON response dari Flask
$mahasiswa_data = json_decode($response, true);

// Proses edit mahasiswa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'edit') {
    $flask_api_url = "http://127.0.0.1:5000/edit_mahasiswa/" . $_POST['mahasiswa_id'];
    $data = array(
        'nim' => $_POST['nim'],
        'username' => $_POST['username'],
        'gender' => $_POST['gender'],
        'prodi' => $_POST['prodi'],
        'jurusan' => $_POST['jurusan'],
        'email' => $_POST['email']
    );

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/json",
            'method'  => 'PATCH',
            'content' => json_encode($data),
        ),
    );
    $context = stream_context_create($options);
    $result = file_get_contents($flask_api_url, false, $context);

    if ($result === FALSE) {
        die('Error updating data.');
    }

    header('Location: daftarMaha.php?success=true');
    exit;
}

// Proses delete mahasiswa
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_id'])) {
    $flask_api_url = "http://127.0.0.1:5000/delete_mahasiswa/" . $_GET['delete_id'];

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/json",
            'method'  => 'DELETE',
        ),
    );
    $context = stream_context_create($options);
    $result = file_get_contents($flask_api_url, false, $context);

    if ($result === FALSE) {
        die('Error deleting data.');
    }

    header('Location: daftarMaha.php?deleted=true');
    exit;
}


// Notifikasi jika berhasil
if (isset($_GET['success'])) {
    echo "<script>alert('Data berhasil diperbarui!');</script>";
} elseif (isset($_GET['deleted'])) {
    echo "<script>alert('Data berhasil dihapus!');</script>";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENSI</title>
    <link rel="icon" type="image/x-icon" href="css/img/sensi.png">
    <link rel="stylesheet" href="css/styleDaftarMaha.css">
    <style>
        /* Style untuk pop-up modal */
        #editMahaModal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            z-index: 1000;
        }

        #editMahaModal h3 {
            margin-bottom: 20px;
        }

        #editMahaModal input {
            width: 100%;
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        #editMahaModal button {
            padding: 10px 15px;
            border: none;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        #editMahaModal button:nth-child(2) {
            background-color: red;
        }

        #editMahaModal button:hover {
            opacity: 0.9;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
    </style>
</head>
<body>
    <?php
    require 'header.php'; ?>

    <main>
        <div class="container">
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
            <div class="right">
                <div class="content">
                    <h3>DAFTAR MAHASISWA</h3>
                    <table>
                        <tr>
                            <th>NO</th>
                            <th>NIM</th>
                            <th>USERNAME</th>
                            <th>GENDER</th>
                            <th>PRODI</th>
                            <th>JURUSAN</th>
                            <th>EMAIL</th>
                            <th>AKSI</th>
                        </tr>
                        <?php
                        // Loop untuk menampilkan data mahasiswa lengkap
                        if (!is_array($mahasiswa_data) || empty($mahasiswa_data)) {
                            echo "<tr><td colspan='8'>Data mahasiswa tidak ditemukan</td></tr>";
                        } else {
                            $no = 1;
                            foreach ($mahasiswa_data as $mahasiswa) {
                                // Seleksi data mahasiswa yang lengkap dan tidak menggunakan placeholder
                                if (
                                    !empty($mahasiswa['nim']) && $mahasiswa['nim'] !== 'N/A' &&
                                    !empty($mahasiswa['username']) && $mahasiswa['username'] !== 'N/A' &&
                                    !empty($mahasiswa['gender']) && $mahasiswa['gender'] !== 'N/A' &&
                                    !empty($mahasiswa['prodi']) && $mahasiswa['prodi'] !== 'N/A' &&
                                    !empty($mahasiswa['jurusan']) && $mahasiswa['jurusan'] !== 'N/A' &&
                                    !empty($mahasiswa['email']) && $mahasiswa['email'] !== 'N/A'
                                ) {
                                    echo "<tr>";
                                    echo "<td>{$no}</td>";
                                    echo "<td>{$mahasiswa['nim']}</td>";
                                    echo "<td>{$mahasiswa['username']}</td>";
                                    echo "<td>{$mahasiswa['gender']}</td>";
                                    echo "<td>{$mahasiswa['prodi']}</td>";
                                    echo "<td>{$mahasiswa['jurusan']}</td>";
                                    echo "<td>{$mahasiswa['email']}</td>";
                                    echo "<td>
                                            <a href='#' onclick='showEditModal(\"{$mahasiswa['id']}\", \"{$mahasiswa['nim']}\", \"{$mahasiswa['username']}\", \"{$mahasiswa['gender']}\", \"{$mahasiswa['prodi']}\", \"{$mahasiswa['jurusan']}\", \"{$mahasiswa['email']}\")' class='detail-link'>
                                                <i class='fa-solid fa-pen-to-square'></i>
                                            </a> |
                                            <a href='daftarMaha.php?delete_id={$mahasiswa['id']}' class='detail-link'>
                                                <i class='fa-solid fa-trash'></i>
                                            </a>
                                        </td>";
                                    echo "</tr>";
                                    $no++;
                                }
                            }
                        }
                            // Jika tidak ada data mahasiswa lengkap
                        //     if ($no === 1) {
                        //         echo "<tr><td colspan='8'>Tidak ada data mahasiswa yang lengkap</td></tr>";
                        //     }
                        // } else {
                        //     echo "<tr><td colspan='8'>Data mahasiswa tidak ditemukan</td></tr>";
                        // }
                        ?>
                    </table>
                    <div class="button">
                        <div class="button-1">
                            <a class="info" href="registMahasis.php">Tambah Data +</a>
                        </div>
                    </div>
                </div>
        </div>
    </main>

    <!-- Pop-Up Edit Mahasiswa -->
    <div class="modal-overlay" id="modalOverlay"></div>
    <div id="editMahaModal">
    <form method="POST">
        <h3>Edit Data Mahasiswa</h3>
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="mahasiswa_id" id="editMahasiswaId">
        <label>NIM:</label>
        <input type="text" name="nim" id="editNim" required>
        <label>Username:</label>
        <input type="text" name="username" id="editUsername" required>
        <label>Gender:</label>
        <input type="text" name="gender" id="editGender" required>
        <label>Prodi:</label>
        <input type="text" name="prodi" id="editProdi" required>
        <label>Jurusan:</label>
        <input type="text" name="jurusan" id="editJurusan" required>
        <label>Email:</label>
        <input type="email" name="email" id="editEmail" required>
        <button type="submit">Simpan</button>
        <button type="button" onclick="closeEditModal()">Batal</button>
    </form>
</div>

<script>
    function showEditModal(id, nim, username, gender, prodi, jurusan, email) {
        document.getElementById('editMahasiswaId').value = id;
        document.getElementById('editNim').value = nim;
        document.getElementById('editUsername').value = username;
        document.getElementById('editGender').value = gender;
        document.getElementById('editProdi').value = prodi;
        document.getElementById('editJurusan').value = jurusan;
        document.getElementById('editEmail').value = email;
        document.getElementById('editMahaModal').style.display = 'block';
        document.getElementById('modalOverlay').style.display = 'block';
    }

    function closeEditModal() {
        document.getElementById('editMahaModal').style.display = 'none';
        document.getElementById('modalOverlay').style.display = 'none';
    }
</script>


</body>
</html>
