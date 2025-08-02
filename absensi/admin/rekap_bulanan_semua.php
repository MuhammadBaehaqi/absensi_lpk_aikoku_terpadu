<?php
include '../../includes/session.php';
include '../../includes/config.php';

$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');

// pagination
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// total siswa (untuk hitung total halaman)
$total_siswa = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM tb_pengguna WHERE role='siswa'"))[0];
$total_pages = ceil($total_siswa / $limit);

// ambil rekap per siswa (agregasi langsung di SQL, efisien)
$sql = "
    SELECT 
        p.id_pengguna,
        p.nama_lengkap,
        SUM(CASE WHEN a.keterangan = 'Hadir' THEN 1 ELSE 0 END)  AS Hadir,
        SUM(CASE WHEN a.keterangan = 'Izin' THEN 1 ELSE 0 END)   AS Izin,
        SUM(CASE WHEN a.keterangan = 'Sakit' THEN 1 ELSE 0 END)  AS Sakit,
        SUM(CASE WHEN a.keterangan = 'Alpha' THEN 1 ELSE 0 END)  AS Alpha
    FROM tb_pengguna p
    LEFT JOIN tb_absensi a 
        ON a.id_pengguna = p.id_pengguna
        AND MONTH(a.tanggal) = '$bulan'
        AND YEAR(a.tanggal)  = '$tahun'
    WHERE p.role = 'siswa'
    GROUP BY p.id_pengguna, p.nama_lengkap
    ORDER BY p.nama_lengkap ASC
    LIMIT $limit OFFSET $offset
";
$rekap = mysqli_query($koneksi, $sql);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Rekap Semua Siswa Bulanan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="../../img/logo.png">
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

        /* Dark mode */
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
    <?php include '../../includes/sidebar.php'; ?>
    <div class="content container">
        <div class="card shadow mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Rekap Absensi Semua Siswa</h5>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label>Bulan</label>
                        <select name="bulan" class="form-control" required>
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>" <?= $bulan == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' ?>>
                                    <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Tahun</label>
                        <input type="number" name="tahun" class="form-control" value="<?= htmlspecialchars($tahun) ?>"
                            required>
                    </div>
                    <div class="col-md-2">
                        <label>Tampilkan</label>
                        <select name="limit" class="form-control">
                            <?php foreach ([5, 10, 20, 50] as $opt): ?>
                                <option value="<?= $opt ?>" <?= ($limit == $opt ? 'selected' : '') ?>><?= $opt ?> data</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-primary w-100">Tampilkan</button>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <a href="rekap_bulanan_semua.php" class="btn btn-warning w-100">Reset</a>
                    </div>
                </form>
                <div class="table-responsive">
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
                            <?php
                            $no = $offset + 1;
                            while ($r = mysqli_fetch_assoc($rekap)): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($r['nama_lengkap']) ?></td>
                                    <td><?= (int) $r['Hadir'] ?></td>
                                    <td><?= (int) $r['Izin'] ?></td>
                                    <td><?= (int) $r['Sakit'] ?></td>
                                    <td><?= (int) $r['Alpha'] ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                    <nav>
                        <ul class="pagination">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                    <a class="page-link"
                                        href="?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>&limit=<?= $limit ?>&page=<?= $i ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                </div>
                <a href="/absensi/absensi/admin/cetak pdf/rekap_bulanan_semua_pdf.php?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" target="_blank"
                    class="btn btn-danger mt-3">
                    Cetak PDF Semua Siswa
                </a>
                <a href="../../admin/dashboard_admin.php" class="btn btn-secondary mt-3">Kembali</a>
            </div>
        </div>
    </div>
</body>

</html>