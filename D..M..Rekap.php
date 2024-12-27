<?php
// Mengambil data maaha dari API Flask
$flask_api_url = "http://127.0.0.1:5000/get_ceklok"; // URL API Flask
$response = file_get_contents($flask_api_url);

// Cek apakah respons valid
if ($response === FALSE) {
    die('Gagal menghubungi API Flask');
}

// Decode JSON response dari Flask
$ceklok_data = json_decode($response, true);

// Mengambil data absen dari API Flask
$flask_api_url = "http://127.0.0.1:5000/get_absen"; // URL API Flask
$response = file_get_contents($flask_api_url);

// Cek apakah respons valid
if ($response === FALSE) {
    die('Gagal menghubungi API Flask');
}

// Decode JSON response dari Flask
$absen_data = json_decode($response, true);


if (isset($_POST['download_pdf'])) {
    $flask_api_url = "http://127.0.0.1:5000/generate_pdf";// URL Flask untuk generate PDF
    $pdf_content = file_get_contents($flask_api_url);

    if ($pdf_content === FALSE) {
        die('Gagal mengunduh PDF dari server Flask');
    }

    // Kirim file PDF ke pengguna
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="rekap_absen.pdf"');
    echo $pdf_content;
    exit;
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
    <link rel="stylesheet" href="css/styleRekapAbsensi.css">
</head>
<body>
    <?php
    require 'D..M..Header.php';
    require 'session_start.php'; 
    ?>

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
                <table>
                        <tr>
                            <th class="daftarhadir" colspan="4">DAFTAR HADIR</th>
                        </tr>
                        <?php if (!empty($absen_data)): ?>
                            <?php foreach ($absen_data as $absen): ?>
                                <?php 
                                    // Check if all required fields are not NULL or 'N/A'
                                    if ($absen['programStudi'] !== 'N/A' && $absen['namaKelas'] !== 'N/A' && 
                                        $absen['semester'] !== 'N/A' && $absen['ruangan'] !== 'N/A' && 
                                        $absen['mataKuliah'] !== 'N/A' && $absen['jumlahPeserta'] !== 'N/A' && 
                                        $absen['dosen'] !== 'N/A' && $absen['programStudi'] !== NULL && 
                                        $absen['namaKelas'] !== NULL && $absen['semester'] !== NULL && 
                                        $absen['ruangan'] !== NULL && $absen['mataKuliah'] !== NULL && 
                                        $absen['jumlahPeserta'] !== NULL && $absen['dosen'] !== NULL): 
                                    ?>
                                <tr>
                                    <th>PROGRAM STUDI</th>
                                    <td class="child-2"><?= htmlspecialchars($absen['programStudi']) ?></td>
                                    <th>NAMA KELAS</th>
                                    <td><?= htmlspecialchars($absen['namaKelas']) ?></td>
                                </tr>
                                <tr>
                                    <th>SEMESTER</th>
                                    <td><?= htmlspecialchars($absen['semester']) ?></td>
                                    <th>RUANGAN</th>
                                    <td><?= htmlspecialchars($absen['ruangan']) ?></td>
                                </tr>
                                <tr>
                                    <th>MATA KULIAH</th>
                                    <td><?= htmlspecialchars($absen['mataKuliah']) ?></td>
                                    <th>JUMLAH PESERTA</th>
                                    <td><?= htmlspecialchars($absen['jumlahPeserta']) ?></td>
                                </tr>
                                <tr>
                                    <th>DOSEN</th>
                                    <td><?= htmlspecialchars($absen['dosen']) ?></td>
                                    <td class="inner">
                                        <table class="inner-table">
                                        <tr>
                                            <th>HARI</th>
                                            <th>JAM</th>
                                        </tr>
                                        </table>
                                    </td>
                                    <td class="inner">
                                        <table class="inner-table">
                                        <tr>
                                            <td><?= htmlspecialchars($absen['hari']) ?></td>
                                            <td><?= htmlspecialchars($absen['jam']) ?></td>
                                        </tr>
                                        </table>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">Tidak ada data absen.</td>
                            </tr>
                        <?php endif; ?>
                    </table>
                    <table class="bottom">
                        <tr>
                            <th rowspan="2">NO</th>
                            <th rowspan="2">NIM</th>
                            <th rowspan="2" class="child-3">NAMA MAHASISWA</th>
                            <th colspan="16">TINDAKAN</th>
                        </tr>
                        <tr>
                            <th>1</th>
                            <th>2</th>
                            <th>3</th>
                            <th>4</th>
                            <th>5</th>
                            <th>6</th>
                            <th>7</th>
                            <th>8</th>
                            <th>9</th>
                            <th>10</th>
                            <th>11</th>
                            <th>12</th>
                            <th>13</th>
                            <th>14</th>
                            <th>15</th>
                            <th>16</th>
                        </tr>
                        <tr>
                        <?php
// Loop untuk menampilkan data mahasiswa
if (is_array($ceklok_data) || is_object($ceklok_data)) {
    $no = 1;
    foreach ($ceklok_data as $ceklok) {
        echo "<tr>";
        echo "<td>{$no}</td>";
        echo "<td>{$ceklok['nim']}</td>";
        echo "<td>{$ceklok['username']}</td>";

        // Logika untuk menampilkan simbol checklist berdasarkan absen_count
        $absen_count = intval($ceklok['absen_count'] ?? 0); // Ambil nilai absen_count
        for ($i = 1; $i <= 16; $i++) {
            if ($i <= $absen_count) {
                echo "<td>&#10003;</td>"; // Simbol checklist (✓)
            } else {
                echo "<td></td>";
            }
        }
        echo "</tr>";
        $no++;
    }
} else {
    echo "<tr><td colspan='19'>Data mahasiswa tidak ditemukan</td></tr>";
}
?>
                    </table>
                </div>
                <!-- <div class="button">
                    <a class="edit" href="#">Edit</a> -->
                    <div class="button-1">
                        <form method="post" action="">
                            <button type="submit" name="download_pdf">Unduh PDF</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $programStudi = $_POST['programStudi'];
    $semester = $_POST['semester'];
    $mataKuliah = $_POST['mataKuliah'];
    $dosen = $_POST['dosen'];
    $namaKelas = $_POST['namaKelas'];
    $ruangan = $_POST['ruangan'];
    $jumlahPeserta = $_POST['jumlahPeserta'];
    $hari = $_POST['hari'];
    $jam = $_POST['jam'];

    // Validasi password dan konfirmasi password

        // Kirim data ke Flask
        $flask_url = 'http://127.0.0.1:5000/absen'; // URL Flask untuk tambahkan data 
        $data = json_encode([
            'programStudi' => $programStudi,
            'semester' => $semester,
            'mataKuliah' => $mataKuliah,
            'dosen' => $dosen,
            'namaKelas' => $namaKelas,
            'ruangan' => $ruangan,
            'jumlahPeserta' => $jumlahPeserta,
            'hari' => $hari,
            'jam' => $jam
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
            $success_message = "Data absen berhasil ditambahkan!";
        } else {
            $error_message = "Gagal menambahkan Data.";
        }
    
}
?>
    </main>
    <div class="popup" style="display: none;">
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
                    <td><label for="programStudi">Program Studi:</label></td>
                    <td><input type="programStudi" name="programStudi" required></td>
                </tr>
                <tr>
                    <td><label for="semester">Semester:</label></td>
                    <td><input type="number" id="semester" name="semester" required></td>
                </tr>
                <tr>
                    <td><label for="mataKuliah">Mata Kuliah:</label></td>
                    <td><input type="text" id="mataKuliah" name="mataKuliah" required></td>
                </tr>
                <tr>
                    <td><label for="dosen">Dosen:</label></td>
                    <td><input type="text" id="dosen" name="dosen" required></td>
                </tr>
                <tr>
                    <td><label for="namaKelas">Nama Kelas:</label></td>
                    <td><input type="text" id="namaKelas" name="namaKelas" required></td>
                </tr>
                <tr>
                    <td><label for="ruangan">Ruangan:</label></td>
                    <td><input type="text" id="ruangan" name="ruangan" required></td>
                </tr>
                <tr>
                    <td><label for="jumlahPeserta">Jumlah Peserta:</label></td>
                    <td><input type="number" id="jumlahPeserta" name="jumlahPeserta" required></td>
                </tr>
                <tr>
                    <td><label for="hari">Hari:</label></td>
                    <td>
                        <select id="hari" name="hari" required>
                            <option value="Senin">Senin</option>
                            <option value="Selasa">Selasa</option>
                            <option value="Rabu">Rabu</option>
                            <option value="Kamis">Kamis</option>
                            <option value="Jumat">Jumat</option>
                            <option value="Sabtu">Sabtu</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="jam">Jam:</label></td>
                    <td><input type="time" id="jam" name="jam" required></td>
                </tr>
            </table>
            <div class="button-2"> 
              <button type ="submit">Simpan Perubahan</button>
              <a href="rekapAbsensi.php">Batalkan</a>
            </div>
            </form>
        </div>
    </div>       
    <script src="scriptPopupp2.js"></script>
</body>
</html>