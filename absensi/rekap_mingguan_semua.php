<?php
include '../includes/session.php';
include '../includes/config.php';

$tgl_awal = $_GET['tgl_awal'] ?? '';
$tgl_akhir = $_GET['tgl_akhir'] ?? '';
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$rekap = [];
$total_data = 0;
$total_pages = 1;

if (!empty($tgl_awal) && !empty($tgl_akhir)) {
    $siswa_query = mysqli_query($koneksi, "SELECT COUNT(*) FROM tb_pengguna WHERE role='siswa'");
    $total_data = mysqli_fetch_row($siswa_query)[0];
    $total_pages = ceil($total_data / $limit);

    $siswa = mysqli_query($koneksi, "SELECT * FROM tb_pengguna WHERE role='siswa' LIMIT $limit OFFSET $offset");

    while ($s = mysqli_fetch_assoc($siswa)) {
        $id = $s['id_pengguna'];
        $nama = $s['nama_lengkap'];

        $rekap[$id] = [
            'nama' => $nama,
            'Hadir' => 0,
            'Izin' => 0,
            'Sakit' => 0,
            'Alpha' => 0
        ];

        $q = mysqli_query($koneksi, "
            SELECT keterangan, COUNT(*) AS total 
            FROM tb_absensi 
            WHERE id_pengguna='$id' AND tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'
            GROUP BY keterangan
        ");
        while ($r = mysqli_fetch_assoc($q)) {
            $rekap[$id][$r['keterangan']] = $r['total'];
        }
    }
}
?>


<!DOCTYPE html>
<html>

<head>
    <title>Rekap Mingguan Semua Siswa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="../img/logo.png">
    <style>
        .main-content {
            margin-left: 250px;
            padding: 80px 20px 20px;
            width: 100%;
        }

         /* Warna tabel saat dark mode */
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
            /* <- ini penting! agar td tidak putih */
            color: #f1f1f1;
            border-color: #444;
            /* agar tidak terlalu terang */
        }

        /* Hover baris */
        .dark-mode .table tbody tr:hover {
            background-color: #2a2a2a;
        }

        .dark-mode .table-bordered th,
        .dark-mode .table-bordered td {
            border: 1px solid #444 !important;
        }

        .dark-mode .form-control::placeholder {
            color: #aaa;
        }
        /* Form input dan select saat dark mode */
        .dark-mode .form-control {
            background-color: #2c2c2c;
            color: #fff;
            border: 1px solid #555;
        }

        /* Placeholder agar lebih redup */
        .dark-mode .form-control::placeholder {
            color: #aaa;
        }

        .dark-mode .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .dark-mode .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .dark-mode .btn-success {
            background-color: #198754;
            border-color: #198754;
        }

        .dark-mode .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
    </style>
</head>

<body>
    <?php include '../includes/sidebar.php'; ?>
    <div class="main-content container mt-5">
        <h3>Rekap Absensi Mingguan Semua Siswa</h3>
    
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <label>Dari Tanggal</label>
                <input type="date" name="tgl_awal" class="form-control" value="<?= $tgl_awal ?>" required>
            </div>
            <div class="col-md-3">
                <label>Sampai Tanggal</label>
                <input type="date" name="tgl_akhir" class="form-control" value="<?= $tgl_akhir ?>" required>
            </div>
            <div class="col-md-3">
                <label>Tampilkan</label>
                <select name="limit" class="form-control">
                    <?php foreach ([5, 10, 20, 50] as $opt): ?>
                        <option value="<?= $opt ?>" <?= ($limit == $opt ? 'selected' : '') ?>><?= $opt ?> data</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-primary w-100">Tampilkan</button>
            </div>
        </form>
    
        <?php if (!empty($rekap)): ?>
            <h5 class="mb-3">Periode: <?= date('d-m-Y', strtotime($tgl_awal)) ?> s.d.
                <?= date('d-m-Y', strtotime($tgl_akhir)) ?>
            </h5>
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Hadir</th>
                        <th>Izin</th>
                        <th>Sakit</th>
                        <th>Alpha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = $offset + 1;
                    foreach ($rekap as $r): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $r['nama'] ?></td>
                            <td><?= $r['Hadir'] ?></td>
                            <td><?= $r['Izin'] ?></td>
                            <td><?= $r['Sakit'] ?></td>
                            <td><?= $r['Alpha'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
    
            <!-- Pagination -->
            <nav>
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                            <a class="page-link"
                                href="?tgl_awal=<?= $tgl_awal ?>&tgl_akhir=<?= $tgl_akhir ?>&limit=<?= $limit ?>&page=<?= $i ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
    
            <a href="rekap_mingguan_semua_pdf.php?tgl_awal=<?= $tgl_awal ?>&tgl_akhir=<?= $tgl_akhir ?>" target="_blank"
                class="btn btn-danger">Cetak PDF</a>
        <?php elseif (!empty($tgl_awal)): ?>
            <div class="alert alert-warning">Tidak ada data absensi dalam minggu ini.</div>
        <?php endif; ?>
    
        <a href="../admin/dashboard_admin.php" class="btn btn-secondary mt-3">Kembali</a>
    </div>
    </body>
    
    </html>