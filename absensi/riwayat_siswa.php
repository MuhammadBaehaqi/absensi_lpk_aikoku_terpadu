<?php
include '../includes/session.php';
include '../includes/config.php';

$id_pengguna = $_SESSION['id_pengguna'];

// Ambil limit dari GET, default 5
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 5;

// Ambil halaman sekarang dari GET, default 1
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Ambil total data
$total_result = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM tb_absensi WHERE id_pengguna='$id_pengguna'");
$total_data = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_data / $limit);

// Ambil data sesuai halaman
$query = mysqli_query($koneksi, "SELECT * FROM tb_absensi WHERE id_pengguna='$id_pengguna' ORDER BY tanggal DESC LIMIT $start, $limit");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <title>Riwayat Absensi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="../img/logo.png">
    <style>
        .logo-img {
            height: 40px;
            margin-right: 10px;
        }

        /* Tambahan CSS responsif untuk HP */
        @media (max-width: 576px) {
            .navbar .navbar-text {
                font-size: 0.8rem;
            }

            h2 {
                font-size: 1.2rem;
            }

            .logo-img {
                height: 30px;
                margin-right: 5px;
            }

            .btn {
                font-size: 0.8rem;
                padding: 8px 10px;
            }

            .alert,
            .card,
            .alert-info {
                margin-left: 10px;
                margin-right: 10px;
            }

            .card-title {
                font-size: 1rem;
            }

            ol {
                padding-left: 1rem;
            }
        }

        .logo-img {
            height: 40px;
        }

        @media (max-width: 576px) {
            .navbar .navbar-brand {
                font-size: 1rem;
            }

            .navbar .text-white {
                font-size: 0.85rem;
            }

            .btn.btn-outline-light {
                font-size: 0.75rem;
                padding: 4px 10px;
            }
        }

        .logo-img {
            height: 40px;
        }

        @media (max-width: 576px) {
            .navbar .navbar-brand {
                font-size: 1rem;
            }

            .navbar .text-white,
            .navbar .small {
                font-size: 0.85rem;
            }

            .btn.btn-outline-light {
                font-size: 0.75rem;
                padding: 4px 10px;
            }
        }

        .table-responsive {
            overflow-x: scroll !important;
            /* Paksa scroll horizontal */
            scrollbar-width: auto;
            /* Untuk Firefox */
        }

        /* Untuk Chrome, Edge, Safari */
        .table-responsive::-webkit-scrollbar {
            height: 8px;
            /* Atur tinggi scrollbar horizontal */
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #888;
            /* Warna thumb scrollbar */
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container text-white">
            <!-- Desktop View (Logo kiri, Salam kanan) -->
            <div class="d-none d-md-flex justify-content-between align-items-center w-100">
                <div class="d-flex align-items-center">
                    <img src="../img/logo.png" alt="Logo LPK" class="logo-img me-2">
                    <span class="navbar-brand mb-0 h5">LPK AIKOKU TERPADU</span>
                </div>
                <div class="text-end small">
                    <div>いらっしゃいませ, <?= $_SESSION['username']; ?> (<?= $_SESSION['role']; ?>)</div>
                    <a href="../auth/logout.php" class="btn btn-outline-light btn-sm mt-1">Logout</a>
                </div>
            </div>

            <!-- Mobile View (Semua tengah, logo lebih besar, teks tengah) -->
            <div class="d-block d-md-none w-100 text-center">
                <img src="../img/logo.png" alt="Logo LPK" class="mb-1" style="height: 45px;"> <!-- DIBESARKAN -->
                <div class="fw-bold text-white" style="font-size: 1rem;">LPK AIKOKU TERPADU</div>
                <div class="small">いらっしゃいませ, <?= $_SESSION['username']; ?> (<?= $_SESSION['role']; ?>)</div>
                <a href="../auth/logout.php" class="btn btn-outline-light btn-sm mt-1">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h3>Riwayat Absensi</h3>
        <!-- Show per page selector -->
        <form method="GET" class="d-flex align-items-center mb-3">
            <label class="me-2">Tampilkan</label>
            <select name="limit" onchange="this.form.submit()" class="form-select w-auto me-2">
                <?php foreach ([5, 10, 15, 20, 25] as $opt): ?>
                    <option value="<?= $opt ?>" <?= $limit == $opt ? 'selected' : '' ?>><?= $opt ?></option>
                <?php endforeach; ?>
            </select>
            <label>data</label>
        </form>
        <div class="table-responsive mb-4 pb-2">

            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Tanggal</th>
                        <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                        <th>Status Kehadiran</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($query)): ?>
                        <tr>
                            <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>

                            <!-- Jam Masuk -->
                            <td>
                                <?= !empty($row['jam_masuk']) ? $row['jam_masuk'] : '<span class="badge bg-secondary">Belum Absen Masuk</span>' ?>
                            </td>

                            <!-- Jam Pulang -->
                            <td>
                                <?= !empty($row['jam_pulang']) ? $row['jam_pulang'] : '<span class="badge bg-secondary">Belum Absen Pulang</span>' ?>
                            </td>
                            <!-- Status -->
                            <!-- Status -->
<td>
<?php
                                    $tanggal = $row['tanggal'];
                                    $jam_masuk = strtotime($row['jam_masuk'] ?? '00:00:00');
                                    $batas_terlambat = strtotime('10:00:00');

                                    // Cek izin yang diterima
                                    $cekIzin = mysqli_query($koneksi, "
    SELECT jenis FROM tb_pengajuan_izin 
    WHERE id_pengguna = '$id_pengguna' 
    AND tanggal = '$tanggal' 
    AND status = 'Diterima' 
    ORDER BY id_pengajuan DESC LIMIT 1
");

                                    if (mysqli_num_rows($cekIzin) > 0) {
                                        $izin = mysqli_fetch_assoc($cekIzin);
                                        $jenis = htmlspecialchars($izin['jenis']);
                                        $warna = $jenis == 'Sakit' ? 'bg-info text-dark' : 'bg-warning text-dark';
                                        echo "<span class='badge $warna'>$jenis ✅</span>";

                                    } elseif ($row['keterangan'] == 'Alpha') {
                                        // Koreksi
                                        $cekKoreksi = mysqli_query($koneksi, "
        SELECT status FROM tb_koreksi_absen 
        WHERE id_pengguna = '$id_pengguna' 
        AND tanggal = '$tanggal' 
        ORDER BY id_koreksi DESC LIMIT 1
    ");

                                        if (mysqli_num_rows($cekKoreksi) > 0) {
                                            $kor = mysqli_fetch_assoc($cekKoreksi);
                                            if ($kor['status'] == 'Ditolak') {
                                                echo '<span class="badge bg-danger">Alpha ❌ (Koreksi Ditolak)</span>';
                                            } elseif ($kor['status'] == 'Disetujui') {
                                                echo '<span class="badge bg-info text-dark">Koreksi Diterima 📝</span>';
                                            } elseif ($kor['status'] == 'Menunggu') {
                                                echo '<span class="badge bg-warning text-dark">Menunggu Koreksi ⚠️</span>';
                                            } else {
                                                echo '<span class="badge bg-danger">Alpha ❌</span>';
                                            }
                                        } else {
                                            // Jika tidak ada koreksi, cek apakah izin ditolak
                                            $izinDitolak = mysqli_query($koneksi, "
            SELECT jenis FROM tb_pengajuan_izin 
            WHERE id_pengguna = '$id_pengguna' 
            AND tanggal = '$tanggal' 
            AND status = 'Ditolak' 
            ORDER BY id_pengajuan DESC LIMIT 1
        ");
                                            if (mysqli_num_rows($izinDitolak) > 0) {
                                                $izin = mysqli_fetch_assoc($izinDitolak);
                                                $jenis = htmlspecialchars($izin['jenis']);
                                                echo "<span class='badge bg-danger'>Alpha ❌ ($jenis Ditolak)</span>";
                                            } else {
                                                echo '<span class="badge bg-danger">Alpha ❌</span>';
                                            }
                                        }

                                    } elseif (!empty($row['jam_masuk'])) {
                                        if ($row['keterangan'] == 'Hadir (Lupa Absen)') {
                                            echo '<span class="badge bg-info text-dark">Hadir (Lupa Absen) 📝</span>';
                                        } else {
                                            if ($jam_masuk > $batas_terlambat) {
                                                echo '<span class="badge bg-warning text-dark">Terlambat ⏰</span>';
                                            } else {
                                                echo '<span class="badge bg-success">Hadir ✅</span>';
                                            }
                                        }
                                    } else {
                                        echo '<span class="badge bg-secondary">Belum Absen</span>';
                                    }
                                    ?>
                            </td>

                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <!-- Pagination -->
            <nav>
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&limit=<?= $limit ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
            <a href="../dashboard_siswa.php" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
</body>

</html>