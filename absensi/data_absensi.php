<?php
include '../includes/session.php';
include '../includes/config.php';

// Dropdown siswa
$siswa = mysqli_query($koneksi, "SELECT * FROM tb_pengguna WHERE role='siswa'");

// Ambil filter
$nama_filter = $_GET['id_pengguna'] ?? '';
$tgl_awal = $_GET['tgl_awal'] ?? '';
$tgl_akhir = $_GET['tgl_akhir'] ?? '';
$status_filter = $_GET['status'] ?? '';
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Query data absensi (dengan LIMIT)
$sql = "SELECT a.*, p.nama_lengkap 
        FROM tb_absensi a 
        JOIN tb_pengguna p ON a.id_pengguna = p.id_pengguna 
        WHERE 1";

if (!empty($nama_filter)) {
    $sql .= " AND a.id_pengguna = '$nama_filter'";
}
if (!empty($tgl_awal) && !empty($tgl_akhir)) {
    $sql .= " AND a.tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'";
}
if (!empty($status_filter)) {
    if ($status_filter == 'Belum Absen') {
        $sql .= " AND a.jam_masuk IS NULL AND a.keterangan = ''";
    } elseif ($status_filter == 'Hadir') {
        $sql .= " AND (a.keterangan = 'Hadir' OR a.keterangan = 'Terlambat' OR a.keterangan = 'Hadir (Lupa Absen)')";
    } else {
        $sql .= " AND a.keterangan = '$status_filter'";
    }
}

$sql .= " ORDER BY a.tanggal DESC LIMIT $limit OFFSET $offset";
$data_absensi = mysqli_query($koneksi, $sql);

// Hitung total data (untuk pagination)
$sql_total = "SELECT COUNT(*) as total 
              FROM tb_absensi a 
              JOIN tb_pengguna p ON a.id_pengguna = p.id_pengguna 
              WHERE 1";
if (!empty($status_filter)) {
    if ($status_filter == 'Belum Absen') {
        $sql_total .= " AND a.jam_masuk IS NULL AND a.keterangan = ''";
    } elseif ($status_filter == 'Hadir') {
        $sql_total .= " AND (a.keterangan = 'Hadir' OR a.keterangan = 'Terlambat' OR a.keterangan = 'Hadir (Lupa Absen)')";
    } else {
        $sql_total .= " AND a.keterangan = '$status_filter'";
    }
}

if (!empty($nama_filter)) {
    $sql_total .= " AND a.id_pengguna = '$nama_filter'";
}
if (!empty($tgl_awal) && !empty($tgl_akhir)) {
    $sql_total .= " AND a.tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'";
}

$total_result = mysqli_query($koneksi, $sql_total);
$total_data = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_data / $limit);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data Absensi Siswa (Filter)</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="../img/logo.png">
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
        }

        .main-content {
            margin-left: 250px;
            padding: 80px 20px 20px;
            width: 100%;
        }

        @media (max-width: 991.98px) {
            .main-content {
                margin-left: 0;
            }
        }

        .dark-mode .table {
            background-color: #1f1f1f;
            color: #f1f1f1;
        }

        .dark-mode .table th {
            background-color: #2c2f33;
            color: #ffffff;
        }

        .dark-mode .table td {
            background-color: #1f1f1f;
            color: #f1f1f1;
            border-color: #444;
        }

        .dark-mode .table tbody tr:hover {
            background-color: #2a2a2a;
        }

        .dark-mode .table-bordered th,
        .dark-mode .table-bordered td {
            border: 1px solid #444 !important;
        }

        .dark-mode .form-control {
            background-color: #2c2c2c;
            color: #fff;
            border: 1px solid #555;
        }

        .dark-mode .form-control::placeholder {
            color: #aaa;
        }

        /* Warna latar dan teks card dalam mode gelap */
        .dark-mode .card {
            background-color: #1f1f1f;
            color: #f1f1f1;
        }

        /* Warna header card */
        .dark-mode .card-header {
            background-color: #2c2f33 !important;
            color: #fff !important;
        }

        /* Warna isi body card */
        .dark-mode .card-body {
            background-color: #1f1f1f;
            color: #f1f1f1;
            border-color: #444;
        }

        /* Tambahan hover dan border */
        .dark-mode .card,
        .dark-mode .card-body,
        .dark-mode .card-header {
            border-color: #444;
        }

        .table-responsive {
            overflow-x: scroll !important;
            scrollbar-width: auto;
            /* Firefox */
        }

        .table-responsive::-webkit-scrollbar {
            height: 8px;
            background-color: #ccc;
            /* warna track */
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background-color: #888;
            /* warna thumb */
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <?php include '../includes/sidebar.php'; ?>
    <div class="content container">
        <div class="card shadow mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Lihat Data Absensi</h5>
            </div>
            <div class="card-body">
                <!-- Filter Form -->
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label>Nama Siswa</label>
                        <select name="id_pengguna" class="form-control">
                            <option value="">-- Semua Siswa --</option>
                            <?php while ($row = mysqli_fetch_assoc($siswa)): ?>
                                <option value="<?= $row['id_pengguna'] ?>" <?= ($nama_filter == $row['id_pengguna']) ? 'selected' : '' ?>>
                                    <?= $row['nama_lengkap'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Status Absensi</label>
                        <select name="status" class="form-control">
                            <option value="">-- Semua Status --</option>
                            <option value="Hadir" <?= ($_GET['status'] ?? '') == 'Hadir' ? 'selected' : '' ?>>Hadir
                            </option>
                            <option value="Izin" <?= ($_GET['status'] ?? '') == 'Izin' ? 'selected' : '' ?>>Izin</option>
                            <option value="Sakit" <?= ($_GET['status'] ?? '') == 'Sakit' ? 'selected' : '' ?>>Sakit
                            </option>
                            <option value="Alpha" <?= ($_GET['status'] ?? '') == 'Alpha' ? 'selected' : '' ?>>Alpha
                            </option>
                            <option value="Belum Absen" <?= ($_GET['status'] ?? '') == 'Belum Absen' ? 'selected' : '' ?>>
                                Belum
                                Absen</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Dari Tanggal</label>
                        <input type="date" name="tgl_awal" class="form-control" value="<?= $tgl_awal ?>">
                    </div>
                    <div class="col-md-2">
                        <label>Sampai Tanggal</label>
                        <input type="date" name="tgl_akhir" class="form-control" value="<?= $tgl_akhir ?>">
                    </div>
                    <div class="col-md-2">
                        <label>Tampilkan</label>
                        <select name="limit" class="form-control">
                            <?php foreach ([5, 10, 20, 50] as $opt): ?>
                                <option value="<?= $opt ?>" <?= ($limit == $opt) ? 'selected' : '' ?>><?= $opt ?> data</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button class="btn btn-primary w-100">Filter</button>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <a href="data_absensi.php" class="btn btn-warning w-100 ms-1">Reset</a>
                    </div>
                </form>

                <!-- Tabel Absensi -->
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Tanggal</th>
                                <th>Jam Masuk</th>
                                <th>Jam Pulang</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = $offset + 1;
                            while ($row = mysqli_fetch_assoc($data_absensi)): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $row['nama_lengkap'] ?></td>
                                    <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                                    <td>
                                        <?= $row['jam_masuk'] ? $row['jam_masuk'] : '<span class="badge bg-secondary">Belum Absen Masuk</span>' ?>
                                    </td>
                                    <td>
                                        <?= $row['jam_pulang'] ? $row['jam_pulang'] : '<span class="badge bg-secondary">Belum Absen Pulang</span>' ?>
                                    </td>
                                    <td>
                                        <?php
                                        $tanggal = $row['tanggal'];
                                        $jamMasuk = strtotime($row['jam_masuk'] ?? '00:00:00');
                                        $batasTerlambat = strtotime('10:00:00');

                                        // Cek apakah ada izin yang disetujui
                                        $cekIzin = mysqli_query($koneksi, "
                                            SELECT jenis FROM tb_pengajuan_izin 
                                            WHERE id_pengguna = '{$row['id_pengguna']}' 
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
                                            // Cek apakah izin/sakit ditolak
                                            $cekTolak = mysqli_query($koneksi, "
                                                SELECT jenis FROM tb_pengajuan_izin 
                                                WHERE id_pengguna = '{$row['id_pengguna']}' 
                                                AND tanggal = '$tanggal' 
                                                AND status = 'Ditolak' 
                                                ORDER BY id_pengajuan DESC LIMIT 1
                                            ");
                                            if (mysqli_num_rows($cekTolak) > 0) {
                                                $tolak = mysqli_fetch_assoc($cekTolak);
                                                $jenisTolak = htmlspecialchars($tolak['jenis']);
                                                echo "<span class='badge bg-danger'>Alpha ❌ ($jenisTolak Ditolak)</span>";
                                            }

                                            // Jika tidak ada izin yang ditolak, cek koreksi
                                            else {
                                                $cekKoreksi = mysqli_query($koneksi, "
                                                    SELECT status FROM tb_koreksi_absen 
                                                    WHERE id_pengguna = '{$row['id_pengguna']}' 
                                                    AND tanggal = '$tanggal' 
                                                    ORDER BY id_koreksi DESC LIMIT 1
                                                ");
                                                if (mysqli_num_rows($cekKoreksi) > 0) {
                                                    $kor = mysqli_fetch_assoc($cekKoreksi);
                                                    if ($kor['status'] == 'Menunggu') {
                                                        echo '<span class="badge bg-warning text-dark">Menunggu Koreksi ⚠️</span>';
                                                    } elseif ($kor['status'] == 'Disetujui') {
                                                        echo '<span class="badge bg-info text-dark">Koreksi Diterima 📝</span>';
                                                    } else {
                                                        echo '<span class="badge bg-danger">Alpha ❌</span>';
                                                    }
                                                } else {
                                                    echo '<span class="badge bg-danger">Alpha ❌</span>';
                                                }
                                            }

                                        } elseif (!empty($row['jam_masuk'])) {
                                            if ($row['keterangan'] == 'Hadir (Lupa Absen)') {
                                                echo '<span class="badge bg-info text-dark">Hadir (Lupa Absen) 📝</span>';
                                            } else {
                                                if ($jamMasuk > $batasTerlambat) {
                                                    echo '<span class="badge bg-warning text-dark">Terlambat ⏰</span>';
                                                } else {
                                                    echo '<span class="badge bg-success">Hadir ✅</span>';
                                                }
                                            }
                                        } elseif (empty($row['jam_masuk']) && $row['keterangan'] == '') {
                                            echo '<span class="badge bg-secondary">Belum Absen Masuk</span>';
                                        } elseif (!empty($row['jam_masuk']) && empty($row['jam_pulang'])) {
                                            echo '<span class="badge bg-secondary">Belum Absen Pulang</span>';
                                        } else {
                                            echo '<span class="badge bg-dark">-</span>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                    <!-- Navigasi Pagination -->
                    <nav>
                        <ul class="pagination">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                    <a class="page-link"
                                        href="?id_pengguna=<?= $nama_filter ?>&tgl_awal=<?= $tgl_awal ?>&tgl_akhir=<?= $tgl_akhir ?>&limit=<?= $limit ?>&status=<?= $status_filter ?>&page=<?= $i ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                </div>

                <!-- Tombol Ekspor -->
                <div class="mt-3">
                    <a href="export_pdf_manual.php" target="_blank" class="btn btn-danger mb-2">Export PDF Manual</a>
                    <a href="export_excel.php" class="btn btn-success mb-2">Export ke Excel</a>
                    <a href="../admin/dashboard_admin.php" class="btn btn-secondary mb-2">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>