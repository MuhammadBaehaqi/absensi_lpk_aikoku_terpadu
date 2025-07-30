<?php
include '../includes/session.php';
include '../includes/config.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Pagination setup
$limitOptions = [5, 10, 15, 20, 25];
$limit = isset($_GET['limit']) && in_array((int) $_GET['limit'], $limitOptions) ? (int) $_GET['limit'] : 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Aksi Terima / Tolak
if (isset($_GET['id']) && isset($_GET['aksi'])) {
    $id = $_GET['id'];
    $aksi = $_GET['aksi'];

    if ($aksi == 'terima') {
        $pengajuan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM tb_pengajuan_izin WHERE id_pengajuan='$id'"));
        $id_pengguna = $pengajuan['id_pengguna'];
        $tanggal = $pengajuan['tanggal'];
        $keterangan = $pengajuan['jenis'];

        $cek = mysqli_query($koneksi, "SELECT * FROM tb_absensi WHERE id_pengguna='$id_pengguna' AND tanggal='$tanggal'");
        if (mysqli_num_rows($cek) > 0) {
            mysqli_query($koneksi, "UPDATE tb_absensi SET keterangan='$keterangan' WHERE id_pengguna='$id_pengguna' AND tanggal='$tanggal'");
        } else {
            mysqli_query($koneksi, "INSERT INTO tb_absensi (id_pengguna, tanggal, keterangan) VALUES ('$id_pengguna', '$tanggal', '$keterangan')");
        }

        mysqli_query($koneksi, "UPDATE tb_pengajuan_izin SET status='Diterima' WHERE id_pengajuan='$id'");
    } elseif ($aksi == 'tolak') {
        mysqli_query($koneksi, "UPDATE tb_pengajuan_izin SET status='Ditolak' WHERE id_pengajuan='$id'");
    }

    header("Location: verifikasi_izin.php?page=$page&limit=$limit");
    exit;
}

// Total Data
$total_query = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM tb_pengajuan_izin");
$total_data = mysqli_fetch_assoc($total_query)['total'];
$total_pages = ceil($total_data / $limit);

// Data Utama
$data = mysqli_query($koneksi, "
    SELECT z.*, u.nama_lengkap, a.jam_masuk, a.jam_pulang 
    FROM tb_pengajuan_izin z 
    JOIN tb_pengguna u ON z.id_pengguna = u.id_pengguna
    LEFT JOIN tb_absensi a ON a.id_pengguna = z.id_pengguna AND a.tanggal = z.tanggal
    ORDER BY z.tanggal DESC
    LIMIT $start, $limit
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Verifikasi Pengajuan Izin</title>
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

        .dark-mode .btn-success,
        .dark-mode .btn-danger,
        .dark-mode .btn-secondary {
            color: white;
        }

        .dark-mode .table-bordered th,
        .dark-mode .table-bordered td {
            border: 1px solid #444 !important;
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
                <h5 class="mb-0">Verifikasi Izin/Sakit</h5>
            </div>
            <div class="card-body">
                <!-- Form Limit -->
                <form method="GET" class="mb-3">
                    <label for="limit" class="form-label">Tampilkan</label>
                    <select name="limit" id="limit" class="form-select d-inline-block w-auto"
                        onchange="this.form.submit()">
                        <?php foreach ($limitOptions as $opt): ?>
                            <option value="<?= $opt ?>" <?= $opt == $limit ? 'selected' : '' ?>>
                                <?= $opt ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span>data per halaman</span>
                    <input type="hidden" name="page" value="<?= $page ?>">
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Alasan</th>
                                <th>Jam Masuk</th>
                                <th>Jam Pulang</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = $start + 1; ?>
                            <?php while ($row = mysqli_fetch_assoc($data)): ?>
                                <tr>
                                    <td>
                                        <?= $no++ ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($row['nama_lengkap']) ?>
                                    </td>
                                    <td>
                                        <?= date('d-m-Y', strtotime($row['tanggal'])) ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($row['jenis']) ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['alasan']) ?>
                                    </td>
                                    <td>
                                        <?= $row['jam_masuk'] ?? '-' ?>
                                    </td>
                                    <td>
                                        <?= $row['jam_pulang'] ?? '-' ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['status']) ?>
                                    </td>
                                    <td>
                                        <?php if ($row['status'] == 'Menunggu'): ?>
                                            <a href="?id=<?= $row['id_pengajuan'] ?>&aksi=terima&page=
                        <?= $page ?>&limit=
                        <?= $limit ?>" class="btn btn-success btn-sm">Terima</a>
                                            <a href="?id=<?= $row['id_pengajuan'] ?>&aksi=tolak&page=<?= $page ?>&limit=<?= $limit ?>"
                                                class="btn btn-danger btn-sm">Tolak</a>
                                        <?php else: ?>
                                            <em>-</em>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <nav>
                        <ul class="pagination">
                            <?php if ($page > 1): ?>
                                <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>&limit=
                <?= $limit ?>">Sebelumnya</a></li>
                            <?php else: ?>
                                <li class="page-item disabled"><span class="page-link">Sebelumnya</span></li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=
                    <?= $i ?>&limit=
                    <?= $limit ?>">
                                        <?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                                <li class="page-item"><a class="page-link"
                                        href="?page=<?= $page + 1 ?>&limit=<?= $limit ?>">Berikutnya</a></li>
                            <?php else: ?>
                                <li class="page-item disabled"><span class="page-link">Berikutnya</span></li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
                <a href="dashboard_admin.php" class="btn btn-secondary mt-3">Kembali</a>
            </div>
        </div>
    </div>
</body>

</html>