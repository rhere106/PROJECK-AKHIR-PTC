<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENSI</title>
    <link rel="icon" type="image/x-icon" href="css/img/sensi.png">
    <link rel="stylesheet" href="css/styleDMSubAbsen.css">
</head>
<body>
    <?php
    require 'header.php';
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
                            <th class="subpembahasan" colspan="4">MONITORING PERKULIAHAN</th>
                        </tr>
                        <tr>
                            <th>PROGRAM STUDI</th>
                            <td class="child-2">Ilmu Komputer</td>
                            <th>NAMA KELAS</th>
                            <td>IK22-A</td>
                        </tr>
                        <tr>
                            <th>SEMESTER</th>
                            <td>2024/2025</td>
                            <th>RUANGAN</th>
                            <td>MATEPPE 203</td>
                        </tr>
                        <tr>
                            <th>MATA KULIAH</th>
                            <td>Etika Profesi</td>
                            <th>JUMLAH PESERTA</th>
                            <td>20</td>
                        </tr>
                        <tr>
                            <th>DOSEN</th>
                            <td>Muh. Agus</td>
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
                                        <td>Senin</td>
                                        <td>08:00 - 10:00</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <table class="bottom">
                        <tr>
                            <th rowspan="2">NO</th>
                            <th rowspan="2">TANGGAL</th>
                            <th rowspan="2">POKOK PEMBAHASAN</th>
                            <th rowspan="2" class="child-3">SUB POKOK PEMBAHASAN</th>
                            <th colspan="16">JUMLAH MAHASISWA</th>
                        </tr>
                        <tr>
                            <th>Hadir</th>
                            <th>Tdk Hadir</th>
                            <th>Keterangan</th>
                        </tr>
                        <tr>
                            <th>1</th>
                            <td>26/08/2024</td>
                            <td>Rhere Azhary Budianty</td>
                            <td>✅✅✅✅✅✅✅✅✅✅✅</td>
                            <td>✅</td>
                            <td>✅</td>
                            <td>✅</td>
                        </tr>
                        <tr>
                            <th>2</th>
                            <td>221031066</td>
                            <td>Andi Riah Zahirah</td>
                            <td>✅✅✅✅✅✅✅✅✅✅✅</td>
                            <td>✅</td>
                            <td>✅</td>
                            <td>✅</td>
                        </tr>
                        <tr>
                            <th>3</th>
                            <td>221031066</td>
                            <td>abangbang</td>
                            <td>✅✅✅✅✅✅✅✅✅✅✅</td>
                            <td>✅</td>
                            <td>✅</td>
                            <td>✅</td>
                        </tr>
                        <tr>
                            <th>4</th>
                            <td>221031066</td>
                            <td></td>
                            <td>✅✅✅✅✅✅✅✅✅✅✅</td>
                            <td>✅</td>
                            <td>✅</td>
                            <td>✅</td>
                        </tr>
                        <tr>
                            <th>5</th>
                            <td>221031066</td>
                            <td> </td>
                            <td>✅✅✅✅✅✅✅✅✅✅✅</td>
                            <td>✅</td>
                            <td>✅</td>
                            <td>✅</td>
                        </tr>
                        <tr>
                            <th>6</th>
                            <td>221031066</td>
                            <td></td>
                            <td>✅✅✅✅✅✅✅✅✅✅✅</td>
                            <td>✅</td>
                            <td>✅</td>
                            <td>✅</td>
                        </tr>
                    </table>
                    </div>
                    <div class="button">
                        <!-- <a class="editna" href="#">Edit</a> -->
                        <div class="button-1">
                            <button>Unduh PDF</button>
                            <a href="" class="next">Next <i class="fa-solid fa-right-long"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
