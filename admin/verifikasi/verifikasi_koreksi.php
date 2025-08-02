<?php
include '../../includes/config.php';
include '../../includes/session.php';

// === Pagination Setup ===
$limitOptions = [5, 10, 15, 20, 25, 30];
$limit = isset($_GET['limit']) && in_array((int) $_GET['limit'], $limitOptions) ? (int) $_GET['limit'] : 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// === Proses verifikasi admin ===
if (isset($_GET['aksi']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $aksi = $_GET['aksi'];

    // Ambil data koreksi
    $ambil = mysqli_query($koneksi, "SELECT * FROM tb_koreksi_absen WHERE id_koreksi='$id'");
    $data = mysqli_fetch_assoc($ambil);
    $id_pengguna = $data['id_pengguna'];
    $tanggal = $data['tanggal'];
    $waktu_koreksi = $data['waktu_koreksi'];
    $jam_masuk_koreksi = date('H:i:s', strtotime($waktu_koreksi));

    if ($aksi == 'setuju') {
        mysqli_query($koneksi, "UPDATE tb_koreksi_absen SET status='Disetujui' WHERE id_koreksi='$id'");

        $keterangan = (strtotime($jam_masuk_koreksi) >= strtotime('18:00:00')) ? "Hadir (Lupa Absen)" : NULL;

        mysqli_query($koneksi, "UPDATE tb_absensi 
            SET keterangan=" . ($keterangan ? "'$keterangan'" : "NULL") . ", jam_masuk='$jam_masuk_koreksi' 
            WHERE id_pengguna='$id_pengguna' AND tanggal='$tanggal'");
    } elseif ($aksi == 'tolak') {
        mysqli_query($koneksi, "UPDATE tb_koreksi_absen SET status='Ditolak' WHERE id_koreksi='$id'");
    }

    $redirect = "verifikasi_koreksi.php?page=$page&limit=$limit";
    header("Location: $redirect");
    exit;
}

// === Total Data & Pagination ===
$total_query = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM tb_koreksi_absen");
$total_data = mysqli_fetch_assoc($total_query)['total'];
$total_pages = ceil($total_data / $limit);

// === Ambil Data Koreksi dengan Pagination ===
$query = mysqli_query($koneksi, "
    SELECT k.*, p.nama_lengkap 
    FROM tb_koreksi_absen k 
    JOIN tb_pengguna p ON k.id_pengguna = p.id_pengguna 
    ORDER BY k.tanggal DESC, k.waktu_koreksi DESC 
    LIMIT $start, $limit
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Verifikasi Koreksi Kehadiran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .dark-mode .btn-success {
            background-color: #198754;
            border-color: #198754;
            color: white;
        }

        .dark-mode .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
            color: white;
        }

        .dark-mode .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
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

        .dark-mode .verified-text {
            color: #ccc !important;
            /* abu muda agar kontras */
        }
    </style>
</head>

<body>
    <?php include '../../includes/sidebar.php'; ?>
    <div class="content container">
        <div class="card shadow mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Verifikasi Koreksi Kehadiran</h5>
            </div>
            <div class="card-body">
                <!-- Form pilih limit -->
                <form method="GET" class="mb-3">
                    <label for="limit" class="form-label">Tampilkan</label>
                    <select name="limit" id="limit" class="form-select d-inline-block w-auto"
                        onchange="this.form.submit()">
                        <?php foreach ($limitOptions as $opt): ?>
                            <option value="<?= $opt ?>" <?= $opt == $limit ? 'selected' : '' ?>><?= $opt ?></option>
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
                                <th>Waktu Koreksi</th>
                                <th>Alasan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = $start + 1;
                            while ($row = mysqli_fetch_assoc($query)): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $row['nama_lengkap'] ?></td>
                                    <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                                    <td><?= date('H:i', strtotime($row['waktu_koreksi'])) ?></td>
                                    <td><?= htmlspecialchars($row['alasan']) ?></td>
                                    <td>
                                        <?php
                                        if ($row['status'] == 'Disetujui') {
                                            echo '<span class="badge bg-success">Disetujui</span>';
                                        } elseif ($row['status'] == 'Ditolak') {
                                            echo '<span class="badge bg-danger">Ditolak</span>';
                                        } else {
                                            echo '<span class="badge bg-warning text-dark">Menunggu</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($row['status'] == 'Menunggu'): ?>
                                            <a href="?aksi=setuju&id=<?= $row['id_koreksi'] ?>&page=<?= $page ?>&limit=<?= $limit ?>"
                                                class="btn btn-success btn-sm">Setujui</a>
                                            <a href="?aksi=tolak&id=<?= $row['id_koreksi'] ?>&page=<?= $page ?>&limit=<?= $limit ?>"
                                                class="btn btn-danger btn-sm">Tolak</a>
                                        <?php else: ?>
                                            <span class="text-muted verified-text">Sudah diverifikasi</span>
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
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $page - 1 ?>&limit=<?= $limit ?>">Sebelumnya</a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled"><span class="page-link">Sebelumnya</span></li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&limit=<?= $limit ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $page + 1 ?>&limit=<?= $limit ?>">Berikutnya</a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled"><span class="page-link">Berikutnya</span></li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
                <a href="../dashboard_admin.php" class="btn btn-secondary mt-3">Kembali ke Dashboard</a>
            </div>
        </div>
    </div>
</body>

</html>