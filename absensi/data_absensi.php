<?php
include '../includes/session.php';
include '../includes/config.php';

// Dropdown siswa
$siswa = mysqli_query($koneksi, "SELECT * FROM tb_pengguna WHERE role='siswa'");

// Ambil filter
$nama_filter = $_GET['id_pengguna'] ?? '';
$tgl_awal = $_GET['tgl_awal'] ?? '';
$tgl_akhir = $_GET['tgl_akhir'] ?? '';
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

$sql .= " ORDER BY a.tanggal DESC LIMIT $limit OFFSET $offset";
$data_absensi = mysqli_query($koneksi, $sql);

// Hitung total data (untuk pagination)
$sql_total = "SELECT COUNT(*) as total 
              FROM tb_absensi a 
              JOIN tb_pengguna p ON a.id_pengguna = p.id_pengguna 
              WHERE 1";

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
    </style>
</head>

<body>
    <?php include '../includes/sidebar.php'; ?>
    <div class="main-content container mt-5">
        <h3>Data Absensi Siswa (Filter)</h3>

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
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Tanggal</th>
                    <th>Jam Masuk</th>
                    <th>Jam Pulang</th>
                    <th>Keterangan</th>
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
                        <td><?= $row['jam_masuk'] ?? '-' ?></td>
                        <td><?= $row['jam_pulang'] ?? '-' ?></td>
                        <td><?= $row['keterangan'] ?></td>
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
                            href="?id_pengguna=<?= $nama_filter ?>&tgl_awal=<?= $tgl_awal ?>&tgl_akhir=<?= $tgl_akhir ?>&limit=<?= $limit ?>&page=<?= $i ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>

        <!-- Tombol Ekspor -->
        <a href="export_pdf_manual.php" target="_blank" class="btn btn-danger mb-3">Export PDF Manual</a>
        <a href="export_excel.php" class="btn btn-success mb-3">Export ke Excel</a>
        <a href="../admin/dashboard_admin.php" class="btn btn-secondary">Kembali</a>
    </div>
</body>

</html>