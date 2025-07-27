<?php
include '../includes/session.php';
include '../includes/config.php';

$siswa = mysqli_query($koneksi, "SELECT * FROM tb_pengguna WHERE role='siswa'");

$id_pengguna = $_GET['id_pengguna'] ?? '';
$tgl_awal = $_GET['tgl_awal'] ?? '';
$tgl_akhir = $_GET['tgl_akhir'] ?? '';
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$data = [];
$total_data = 0;
$total_pages = 1;
$nama_siswa = '';

if (!empty($id_pengguna) && !empty($tgl_awal) && !empty($tgl_akhir)) {
    $result_count = mysqli_query($koneksi, "
        SELECT COUNT(*) FROM tb_absensi 
        WHERE id_pengguna = '$id_pengguna' 
        AND tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'
    ");
    $total_data = mysqli_fetch_row($result_count)[0];
    $total_pages = ceil($total_data / $limit);

    $query = mysqli_query($koneksi, "
        SELECT * FROM tb_absensi 
        WHERE id_pengguna = '$id_pengguna' 
        AND tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'
        ORDER BY tanggal ASC
        LIMIT $limit OFFSET $offset
    ");
    while ($row = mysqli_fetch_assoc($query)) {
        $data[] = $row;
    }

    $get_nama = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama_lengkap FROM tb_pengguna WHERE id_pengguna='$id_pengguna'"));
    $nama_siswa = $get_nama['nama_lengkap'] ?? '-';
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Rekap Absensi Siswa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="../img/logo.png">
    <style> body { margin: 0; padding: 0; display: flex; }
     .main-content 
     { margin-left: 250px; 
        padding: 80px 20px 20px; width: 100%; }
      @media (max-width: 991.98px) { .main-content { margin-left: 0; } } /* Warna tabel saat dark mode */ .dark-mode .table { background-color: #1f1f1f; color: #f1f1f1; } .dark-mode .table th { background-color: #2c2f33; color: #ffffff; } .dark-mode .table td { background-color: #1f1f1f; /* <- ini penting! agar td tidak putih */ color: #f1f1f1; border-color: #444; /* agar tidak terlalu terang */ } /* Hover baris */ .dark-mode .table tbody tr:hover { background-color: #2a2a2a; } .dark-mode .table-bordered th, .dark-mode .table-bordered td { border: 1px solid #444 !important; } .dark-mode .form-control::placeholder { color: #aaa; } /* Form input dan select saat dark mode */ .dark-mode .form-control { background-color: #2c2c2c; color: #fff; border: 1px solid #555; } /* Placeholder agar lebih redup */ .dark-mode .form-control::placeholder { color: #aaa; } .dark-mode .btn-primary { background-color: #0d6efd; border-color: #0d6efd; } .dark-mode .btn-danger { background-color: #dc3545; border-color: #dc3545; } .dark-mode .btn-success { background-color: #198754; border-color: #198754; } .dark-mode .btn-secondary { background-color: #6c757d; border-color: #6c757d; } </style>
</head>

<body>
    <?php include '../includes/sidebar.php'; ?>
    <div class="main-content container mt-5">
        <h3>Rekap Absensi Siswa</h3>

        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <label>Nama Siswa</label>
                <select name="id_pengguna" class="form-control" required>
                    <option value="">-- Pilih Siswa --</option>
                    <?php while ($row = mysqli_fetch_assoc($siswa)): ?>
                        <option value="<?= $row['id_pengguna'] ?>" <?= ($id_pengguna == $row['id_pengguna']) ? 'selected' : '' ?>>
                            <?= $row['nama_lengkap'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label>Dari Tanggal</label>
                <input type="date" name="tgl_awal" class="form-control" value="<?= $tgl_awal ?>" required>
            </div>
            <div class="col-md-3">
                <label>Sampai Tanggal</label>
                <input type="date" name="tgl_akhir" class="form-control" value="<?= $tgl_akhir ?>" required>
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
        </form>

        <?php if (!empty($data)): ?>
            <h5 class="mb-3">Nama Siswa: <?= $nama_siswa ?></h5>
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Tanggal</th>
                        <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $d): ?>
                        <tr>
                            <td><?= date('d-m-Y', strtotime($d['tanggal'])) ?></td>
                            <td><?= $d['jam_masuk'] ?? '-' ?></td>
                            <td><?= $d['jam_pulang'] ?? '-' ?></td>
                            <td><?= $d['keterangan'] ?></td>
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
                                href="?id_pengguna=<?= $id_pengguna ?>&tgl_awal=<?= $tgl_awal ?>&tgl_akhir=<?= $tgl_akhir ?>&limit=<?= $limit ?>&page=<?= $i ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
<?php
    // Hitung rekap total untuk grafik
    $rekap_mingguan = [
        'Hadir' => 0,
        'Izin' => 0,
        'Sakit' => 0,
        'Alpha' => 0
    ];
    foreach ($data as $d) {
        $ket = $d['keterangan'];
        if (isset($rekap_mingguan[$ket])) {
            $rekap_mingguan[$ket]++;
        }
    }
    ?>

<h5 class="mt-4">Grafik Absensi</h5>
<canvas id="grafikAbsensi" width="400" height="200"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('grafikAbsensi').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Hadir', 'Izin', 'Sakit', 'Alpha'],
            datasets: [{
                label: 'Jumlah Kehadiran',
                data: [
                    <?= $rekap_mingguan['Hadir'] ?>,
                    <?= $rekap_mingguan['Izin'] ?>,
                    <?= $rekap_mingguan['Sakit'] ?>,
                    <?= $rekap_mingguan['Alpha'] ?>
                ],
                backgroundColor: [
                    'rgba(25, 135, 84, 0.7)',
                    'rgba(255, 193, 7, 0.7)',
                    'rgba(13, 202, 240, 0.7)',
                    'rgba(220, 53, 69, 0.7)'
                ],
                borderColor: [
                    'rgba(25, 135, 84, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(13, 202, 240, 1)',
                    'rgba(220, 53, 69, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
</script>

            <a href="rekap_mingguan_pdf.php?id_pengguna=<?= $id_pengguna ?>&tgl_awal=<?= $tgl_awal ?>&tgl_akhir=<?= $tgl_akhir ?>"
                target="_blank" class="btn btn-danger mt-3">Cetak PDF</a>
        <?php elseif (!empty($id_pengguna)): ?>
            <div class="alert alert-warning">Tidak ada data absensi di rentang tanggal tersebut.</div>
        <?php endif; ?>

        <a href="../admin/dashboard_admin.php" class="btn btn-secondary mt-3">Kembali</a>
    </div>
</body>

</html>