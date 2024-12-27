<?php
// Mengambil data statik dari API Flask
$flask_api_url = "http://127.0.0.1:5000/get_statik"; // URL API Flask
$response = file_get_contents($flask_api_url);

// Cek apakah respons valid
if ($response === FALSE) {
    die('Gagal menghubungi API Flask');
}

// Decode JSON response dari Flask
$statik_data2 = json_decode($response, true);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $flask_api_url = "http://127.0.0.1:5000/delete_statistik/" . $delete_id;

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/json",
            'method'  => 'DELETE',
        ),
    );
    $context = stream_context_create($options);
    $result = @file_get_contents($flask_api_url, false, $context);

    if ($result === FALSE) {
        $error = error_get_last();
        echo "<script>alert('Error deleting data: {$error['message']}');</script>";
    } else {
        header('Location: statistikAbsensi.php?deleted=true');
        exit;
    }
}

if (isset($_GET['deleted'])) {
    echo "<script>alert('Data statistik berhasil dihapus!');</script>";
}


require 'session_start.php'; 
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENSI</title>
    <link rel="icon" type="image/x-icon" href="css/img/sensi.png">
    <link rel="stylesheet" href="css/styleStatikAbsensii.css">
</head>
<body>
    <?php
    require 'header.php' 
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
                    <h3>DATA ABSENSI</h3>
                    <table>
                        <tr>
                            <th>NO</th>
                            <th>MATAKULIAH</th>
                            <th>KELAS</th>
                            <th>TINDAKAN</th>
                        </tr>
                        <?php
                        // Loop untuk menampilkan data statik
                        if (is_array($statik_data2) && !empty($statik_data2)) {
                            $no = 1;
                            foreach ($statik_data2 as $statik) {
                                if (!isset($statik['id'])) continue; // Skip if ID is not present
                        
                                echo "<tr>";
                                echo "<td>{$no}</td>";
                                echo "<td>{$statik['matakuliah']}</td>";
                                echo "<td>{$statik['kelas']}</td>";
                                echo "<td>
                                    <a href='rekapAbsensi.php?plat=Adam' class='detail-link'><i class='fa-solid fa-eye'></i></a>
                                    <a href='statistikAbsensi.php?delete_id={$statik['id']}' class='detail-link'>
                                        <i class='fa-solid fa-trash'></i>
                                    </a>
                                </td>";
                                echo "</tr>";
                                $no++;
                            }
                        } else {
                            echo "<tr><td colspan='4'>Data statik tidak ditemukan</td></tr>";
                        }
                        
                        ?>
                        

                    </table>
                    <div class="button">
                        <div class="button-1">
                        <a class="info" href="#">Tambah Data +</a>
                        <!-- <a class="del" href="#">Hapus</a> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $kelas = $_POST['kelas'];
    $matakuliah = $_POST['matakuliah'];
    

    // Validasi password dan konfirmasi password

        // Kirim data ke Flask
        $flask_url = 'http://127.0.0.1:5000/statik'; // URL Flask untuk tambahkan data 
        $data = json_encode([
            'kelas' => $kelas,
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
            $success_message = "Data statik berhasil ditambahkan!";
        } else {
            $error_message = "Gagal menambahkan Data.";
        }
    
}
?>

    </main>
    <div class="popup" style="display: none ;">
            <div class="container">
            <h5>Tambahkan Data</h5>
            <?php if (!empty($success_message)): ?>
                <p style="color: green;"><?php echo $success_message; ?></p>
            <?php elseif (!empty($error_message)): ?>
                <p style="color: red;"><?php echo $error_message; ?></p>
            <?php endif; ?>

            <form action="" method="POST">
            <table>
                <tr>
                    <td><label for="kelas">Kelas</label></td>
                    <td><input type="text" id="kelas" name="kelas" required></td>
                </tr>
                <tr>
                    <td><label for="matakuliah">Mata kuliah</label></td>
                    <td><input type="text" id="matakuliah" name="matakuliah" required></td>
                </tr>
            </table>
            <div class="button">
                    <button type="submit">Tambahkan</button>
                    <a href="statistikAbsensi.php">Batalkan</a>
                </div>
            </form>
            </div>

        </div>
        <!-- Popup Konfirmasi Hapus -->
    <div class="popup-hapus" id="sampah" style="display: none ;">
        <div class="ayam">
            <h5>Hapus Data</h5>
            <p>Apakah anda yakin ingin menghapus data ini ?</p>
            <div class="button-hapusnya">
                <button type="delete">Hapus</button>
                <a href="statistikAbsensi.php">Batalkan</a>
            </div>
        </div>
    </div>        
        <script src="scriptPopup11.js"></script>    
</body>
</html>



