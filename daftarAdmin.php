<?php
// Mengambil data admin dari API Flask
$flask_api_url = "http://127.0.0.1:5000/get_admin"; // URL API Flask
$response = file_get_contents($flask_api_url);

// Cek apakah respons valid
if ($response === FALSE) {
    die('Gagal menghubungi API Flask');
}

// Decode JSON response dari Flask
$admin_data = json_decode($response, true);


// Proses edit admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'edit') {
    $flask_api_url = "http://127.0.0.1:5000/edit_admin/" . $_POST['admin_id'];
    $data = array(
        'username' => $_POST['username'],
        'email' => $_POST['email'],
        'gender' => $_POST['gender']
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

    header('Location: daftarAdmin.php?success=true');
    exit;
}

// Proses hapus admin
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_id'])) {
    $flask_api_url = "http://127.0.0.1:5000/delete_admin/" . $_GET['delete_id'];

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

    header('Location: daftarAdmin.php?deleted=true');
    exit;
}


// Notifikasi jika berhasil
if (isset($_GET['success'])) {
    echo "<script>alert('Data berhasil diperbarui!');</script>";
} elseif (isset($_GET['deleted'])) {
    echo "<script>alert('Data berhasil dihapus!');</script>";
}

// require 'session_start.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENSI</title>
    <link rel="icon" type="image/x-icon" href="css/img/sensi.png">
    <link rel="stylesheet" href="css/styleDaftarAdmin.css">
    <style>
        /* Style untuk pop-up modal */
        #editAdminModal {
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

        #editAdminModal h3 {
            margin-bottom: 20px;
        }

        #editAdminModal input {
            width: 100%;
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        #editAdminModal button {
            padding: 10px 15px;
            border: none;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        #editAdminModal button:nth-child(2) {
            background-color: red;
        }

        #editAdminModal button:hover {
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
    require 'header.php';
    ?>

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
                    <h3>DAFTAR ADMIN</h3>
                    <table>
                        <tr>
                            <th>NO</th>
                            <th>USERNAME</th>
                            <th>EMAIL</th>
                            <th>GENDER</th>
                            <th>AKSI    </th>
                        </tr>
                        <?php
                        // Loop untuk menampilkan data admin
                        if (!is_array($admin_data) || empty($admin_data)) {
                            echo "<tr><td colspan='8'>Data admin tidak ditemukan</td></tr>";
                        } else {
                            $no = 1;
                            foreach ($admin_data as $admin) {
                                // Pastikan key 'id' tersedia untuk setiap entri
                                if (!isset($admin['id'])) {
                                    continue; // Lewati entri jika tidak ada key 'id'
                                }
                        
                                echo "<tr>";
                                echo "<td>{$no}</td>";
                                echo "<td>{$admin['username']}</td>";
                                echo "<td>{$admin['email']}</td>";
                                echo "<td>{$admin['gender']}</td>";
                                echo "<td>
                                    <a href='#' onclick='showEditModal(\"{$admin['id']}\", \"{$admin['username']}\", \"{$admin['email']}\", \"{$admin['gender']}\")' class='detail-link'>
                                        <i class='fa-solid fa-pen-to-square'></i>
                                    </a> |
                                    <a href='daftarAdmin.php?delete_id={$admin['id']}' class='detail-link'>
                                        <i class='fa-solid fa-trash'></i>
                                    </a>
                                </td>";
                                echo "</tr>";
                                $no++;
                            }
                        }
                        
                        
                        ?>
                    </table>
                    <div class="button">
                        <div class="button-1">
                        <a class="info" href="registAdmin.php">Tambah Data +</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    
    <!-- Pop-Up Edit Admin -->
    <div class="modal-overlay" id="modalOverlay"></div>
    <div id="editAdminModal">
    <form method="POST">
        <h3>Edit Data Admin</h3>
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="admin_id" id="editAdminId">
        <label>Username:</label>    
        <input type="text" name="username" id="editUsername" required>
        <label>Email:</label>
        <input type="email" name="email" id="editEmail" required>
        <label>Gender:</label>
        <input type="text" name="gender" id="editGender" required>
        <button type="submit">Simpan</button>
        <button type="button" onclick="closeEditModal()">Batal</button>
    </form>
</div>

    <script>
        function showEditModal(id, username, email, gender) {
            document.getElementById('editAdminId').value = id;
            document.getElementById('editUsername').value = username;
            document.getElementById('editEmail').value = email;
            document.getElementById('editGender').value = gender;
            document.getElementById('editAdminModal').style.display = 'block';
            document.getElementById('modalOverlay').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editAdminModal').style.display = 'none';
            document.getElementById('modalOverlay').style.display = 'none';
        }

    </script>
</body>
</html>
