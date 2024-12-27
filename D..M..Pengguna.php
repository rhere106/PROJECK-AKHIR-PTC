<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENSI</title>
    <link rel="icon" type="image/x-icon" href="css/img/sensi.png">
    <link rel="stylesheet" href="css/styleDaftarPengguna.css">
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
                    <h3>DAFTAR PENGGUNA:</h3>
                    <table>
                    <tr>
                        <th>NO</th>
                        <th>PENGGUNA</th>
                        <!-- <th>AKSI</th> -->
                    </tr>
                    <tr>    
                        <td>001</td>
                        <td>Dosen</td>
                        <!-- <td><a href="daftarDosen.php" class="detail-link"><i class="fa-solid fa-eye"></i></a>  -->
                        
                    </tr>
                    <tr>
                        <td>002</td>
                        <td>Mahasiswa</td>
                        <!-- <td><a href="daftarMaha.php" class="detail-link"><i class="fa-solid fa-eye"></i></a> -->
                        
                    </tr>
                    <tr>
                        <td>003</td>
                        <td>Admin</td>
                        <!-- <td><a href="daftarAdmin.php" class="detail-link"><i class="fa-solid fa-eye"></i></a> -->
                        
                    </tr>
                    </table>
                    <!-- <div class="button">
                        <div class="button-1">
                        <a class="info" href="#">Tambah Data +</a> -->
                        <!-- <a class="del" href="#">Hapus</a> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <div class="popup" style="display: none;">
            <div class="container">
            <h5>Tambahkan Data</h5>
            <form action="" method="post">
            <table>
                <tr>
                    <td><label for="no">No</label></td>
                    <td><input type="text" id="no" placeholder=" "></td>
                </tr>
                <tr>
                    <td><label for="Pengguna">Pengguna</label></td>
                    <td><input type="text" id="pengguna" placeholder=" "></td>
                </tr>
            </table>
            <div class="button">
                    <button type="submit">Tambahkan</button>
                    <a href="daftarPengguna.php">Batalkan</a>
                </div>
            </form>
            </div>

            <script src="scriptDftrPengguna.js"></script>

        </div>
</body>