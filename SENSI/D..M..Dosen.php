<?php
// Mengambil data dosen dari API Flask
$flask_api_url = "http://127.0.0.1:5000/get_dosen"; // URL API Flask
$response = file_get_contents($flask_api_url);

// Cek apakah respons valid
if ($response === FALSE) {
    die('Gagal menghubungi API Flask');
}

// Decode JSON response dari Flask
$dosen_data = json_decode($response, true);

// Proses edit dosen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'edit') {
    $flask_api_url = "http://127.0.0.1:5000/edit_dosen/" . $_POST['dosen_id'];
    $data = array(
        'username' => $_POST['username'],
        'gender' => $_POST['gender'],
        'nuptk' => $_POST['nuptk'],
        'homebase' => $_POST['homebase'],
        'nama_matakuliah' => $_POST['matakuliah'],
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

    header('Location: daftarDosen.php?success=true');
    exit;
}

// Proses hapus dosen
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_id'])) {
    $flask_api_url = "http://127.0.0.1:5000/delete_dosen/" . $_GET['delete_id'];

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

    header('Location: daftarDosen.php?deleted=true');
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
    <link rel="stylesheet" href="css/styleDaftarrDosen.css">
    <style>
        /* Style untuk pop-up modal */
        #editDosenModal {
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

        #editDosenModal h3 {
            margin-bottom: 20px;
        }

        #editDosenModal input {
            width: 100%;
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        #editDosenModal button {
            padding: 10px 15px;
            border: none;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        #editDosenModal button:nth-child(2) {
            background-color: red;
        }

        #editDosenModal button:hover {
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
    <?php require 'D..M..Header.php'; ?>

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
                <div class="content">
                    <h3>DAFTAR DOSEN</h3>
                    <table>
                    <tr>
                            <th>NO</th>
                            <th>USERNAME</th>
                            <th>GENDER</th>
                            <th>NUPTK</th>
                            <th>HOMEBASE</th>
                            <th>MATAKULIAH</th>
                            <th>EMAIL</th>
                            <!-- <th>AKSI</th> -->
                        </tr>
<?php
                        // Loop untuk menampilkan data dosen lengkap
                        if (!is_array($dosen_data) || empty($dosen_data)) {
                            echo "<tr><td colspan='8'>Data dosen tidak ditemukan</td></tr>";
                        } else {
                            $no = 1;
                            foreach ($dosen_data as $dosen) {
                                if (
                                    !empty($dosen['username']) && $dosen['username'] !== 'N/A' &&
                                    !empty($dosen['gender']) && $dosen['gender'] !== 'N/A' &&
                                    !empty($dosen['nuptk']) && $dosen['nuptk'] !== 'N/A' &&
                                    !empty($dosen['homebase']) && $dosen['homebase'] !== 'N/A' &&
                                    !empty($dosen['nama_matakuliah']) && $dosen['nama_matakuliah'] !== 'N/A' &&
                                    !empty($dosen['email']) && $dosen['email'] !== 'N/A'
                                ) {
                                    echo "<tr>";
                                    echo "<td>{$no}</td>";
                                    echo "<td>{$dosen['username']}</td>";
                                    echo "<td>{$dosen['gender']}</td>";
                                    echo "<td>{$dosen['nuptk']}</td>";
                                    echo "<td>{$dosen['homebase']}</td>";
                                    echo "<td>{$dosen['nama_matakuliah']}</td>";
                                    echo "<td>{$dosen['email']}</td>";
                                    echo "<td>
                                       
                                    </td>";
                                    echo "</tr>";
                                    $no++;
                                }
                            }
                        }
                        
                        
                        ?>
                    </table>
                    <!-- <div class="button">
                        <div class="button-1">
                            <a class="info" href="registDosen.php">Tambah Data +</a> -->
                        </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Pop-Up Edit Dosen -->
    <div class="modal-overlay" id="modalOverlay"></div>
    <div id="editDosenModal">
        <form method="POST">
            <h3>Edit Data Dosen</h3>
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="dosen_id" id="editDosenId">
            <label>Username:</label>
            <input type="text" name="username" id="editUsername" required>
            <label>Gender:</label>
            <input type="text" name="gender" id="editGender" required>
            <label>NUPTK:</label>
            <input type="text" name="nuptk" id="editNuptk" required>
            <label>Homebase:</label>
            <input type="text" name="homebase" id="editHomebase" required>
            <label>Mata Kuliah:</label>
            <input type="text" name="matakuliah" id="editMatakuliah" required>
            <label>Email:</label>
            <input type="email" name="email" id="editEmail" required>
            <button type="submit">Simpan</button>
            <button type="button" onclick="closeEditModal()">Batal</button>
        </form>
    </div>

    <script>
        function showEditModal(id, username, gender, nuptk, homebase, matakuliah, email) {
            document.getElementById('editDosenId').value = id;
            document.getElementById('editUsername').value = username;
            document.getElementById('editGender').value = gender;
            document.getElementById('editNuptk').value = nuptk;
            document.getElementById('editHomebase').value = homebase;
            document.getElementById('editMatakuliah').value = matakuliah;
            document.getElementById('editEmail').value = email;
            document.getElementById('editDosenModal').style.display = 'block';
            document.getElementById('modalOverlay').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editDosenModal').style.display = 'none';
            document.getElementById('modalOverlay').style.display = 'none';
        }
    </script>
</body>
</html>
