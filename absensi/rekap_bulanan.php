<?php
include '../includes/session.php';
include '../includes/config.php';

// Ambil siswa
$siswa = mysqli_query($koneksi, "SELECT * FROM tb_pengguna WHERE role='siswa'");

$id_pengguna = $_GET['id_pengguna'] ?? '';
$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');

$data = [];
if (!empty($id_pengguna)) {
    $query = mysqli_query($koneksi, "
        SELECT keterangan, COUNT(*) as total 
        FROM tb_absensi 
        WHERE id_pengguna='$id_pengguna' 
        AND MONTH(tanggal)='$bulan' AND YEAR(tanggal)='$tahun'
        GROUP BY keterangan
    ");
    while ($row = mysqli_fetch_assoc($query)) {
        $data[$row['keterangan']] = $row['total'];
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Rekap Bulanan Absensi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        /* Form input dan select saat dark mode */
        .dark-mode .form-control {
            background-color: #2c2c2c;
            color: #fff;
            border: 1px solid #555;
        }

        /* Placeholder input di dark mode */
        .dark-mode .form-control::placeholder {
            color: #aaa;
        }
        /* Card dan isinya saat dark mode */
        .dark-mode .card {
            background-color: #1e1e1e;
            color: #fff;
            border: 1px solid #444;
        }

        .dark-mode .card-title {
            color: #fff;
        }

        /* List-group saat dark mode */
        .dark-mode .list-group-item {
            background-color: #2c2c2c;
            color: #fff;
            border-color: #444;
        }

        /* Optional: chart background container */
        .dark-mode canvas {
            background-color: #1e1e1e;
        }

    </style>
</head>

<body>
    <?php include '../includes/sidebar.php'; ?>
    <div class="main-content container mt-5">
        <h3>Rekap Absensi Bulanan</h3>

        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <label>Nama Siswa</label>
                <select name="id_pengguna" class="form-control" required>
                    <option value="">-- Pilih Siswa --</option>
                    <?php while ($row = mysqli_fetch_assoc($siswa)): ?>
                        <option value="<?= $row['id_pengguna'] ?>" <?= $id_pengguna == $row['id_pengguna'] ? 'selected' : '' ?>>
                            <?= $row['nama_lengkap'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label>Bulan</label>
                <select name="bulan" class="form-control" required>
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>" <?= $bulan == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' ?>>
                            <?= date('F', mktime(0, 0, 0, $m, 10)) ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label>Tahun</label>
                <input type="number" name="tahun" class="form-control" value="<?= $tahun ?>" required>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Lihat Rekap</button>
            </div>
        </form>

        <?php if (!empty($data)): ?>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Rekap Bulan <?= date('F', mktime(0, 0, 0, $bulan, 10)) ?>     <?= $tahun ?></h5>
                    <ul class="list-group">
                        <li class="list-group-item">Hadir: <?= $data['Hadir'] ?? 0 ?></li>
                        <li class="list-group-item">Izin: <?= $data['Izin'] ?? 0 ?></li>
                        <li class="list-group-item">Sakit: <?= $data['Sakit'] ?? 0 ?></li>
                        <li class="list-group-item">Alpha: <?= $data['Alpha'] ?? 0 ?></li>
                        <h5 class="mt-4">Grafik Rekap</h5>
<canvas id="grafikAbsensi" width="400" height="200"></canvas>

<script>
const ctx = document.getElementById('grafikAbsensi').getContext('2d');
const grafikAbsensi = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Hadir', 'Izin', 'Sakit', 'Alpha'],
        datasets: [{
            label: 'Jumlah Hari',
            data: [
                <?= $data['Hadir'] ?? 0 ?>,
                <?= $data['Izin'] ?? 0 ?>,
                <?= $data['Sakit'] ?? 0 ?>,
                <?= $data['Alpha'] ?? 0 ?>
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
                precision: 0
            }
        }
    }
});
</script>

                    </ul>
                </div>
            </div>
            <a href="rekap_bulanan_pdf.php?id_pengguna=<?= $id_pengguna ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>"
                target="_blank" class="btn btn-danger mt-3">Cetak PDF</a>

        <?php elseif (!empty($id_pengguna)): ?>
            <div class="alert alert-warning">Tidak ada data absensi pada bulan tersebut.</div>
        <?php endif; ?>

        <a href="../admin/dashboard_admin.php" class="btn btn-secondary mt-3">Kembali</a>
    </div>
</body>

</html>