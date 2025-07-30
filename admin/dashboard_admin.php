<?php
include '../includes/config.php';
include '../includes/session.php';

$tanggal = date('Y-m-d');
$bulan = date('m');
$tahun = date('Y');

// Total pengguna
$totalSiswa = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM tb_pengguna WHERE role='siswa'"))['total'] ?? 0;
$totalAdmin = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM tb_pengguna WHERE role='admin'"))['total'] ?? 0;

// Hadir (termasuk: Hadir, Terlambat, Hadir (Lupa Absen))
$hadirQuery = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total FROM tb_absensi 
    WHERE tanggal = '$tanggal' AND (
        keterangan LIKE 'Hadir%' OR 
        keterangan = 'Terlambat'
    )
");
$hadir = mysqli_fetch_assoc($hadirQuery)['total'] ?? 0;

// Izin
$izin = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT COUNT(*) AS total FROM tb_absensi 
    WHERE tanggal = '$tanggal' AND keterangan = 'Izin'
"))['total'] ?? 0;

// Sakit
$sakit = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT COUNT(*) AS total FROM tb_absensi 
    WHERE tanggal = '$tanggal' AND keterangan = 'Sakit'
"))['total'] ?? 0;

// Alpha
$alpha = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT COUNT(*) AS total FROM tb_absensi 
    WHERE tanggal = '$tanggal' AND keterangan = 'Alpha'
"))['total'] ?? 0;

// Belum Absen
$belumAbsen = $totalSiswa - ($hadir + $izin + $sakit + $alpha);

// Grafik Bulanan Dinamis
function getTotalByKeterangan($koneksi, $keterangan, $bulan, $tahun)
{
    $result = mysqli_fetch_assoc(mysqli_query($koneksi, "
        SELECT COUNT(*) AS total FROM tb_absensi 
        WHERE keterangan LIKE '$keterangan%' 
        AND MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun'
    "));
    return $result['total'] ?? 0;
}

$grafik = [
    'Hadir' => getTotalByKeterangan($koneksi, 'Hadir', $bulan, $tahun) +
        getTotalByKeterangan($koneksi, 'Terlambat', $bulan, $tahun) +
        getTotalByKeterangan($koneksi, 'Hadir (Lupa Absen)', $bulan, $tahun),
    'Izin' => getTotalByKeterangan($koneksi, 'Izin', $bulan, $tahun),
    'Sakit' => getTotalByKeterangan($koneksi, 'Sakit', $bulan, $tahun),
    'Alpha' => getTotalByKeterangan($koneksi, 'Alpha', $bulan, $tahun)
];
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../img/logo.png">
    <style>
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

    <div class="content" id="mainContent">
        <h4>📊 Statistik Absensi Hari Ini (<?= date('d-m-Y') ?>)</h4>
        <a href="cron_alpha.php" class="btn btn-outline-danger btn-sm mb-3">
            🔄 Jalankan Auto-Alpha Sekarang
        </a>
        <div class="row g-3 mb-4 mt-2">
            <?php
            $cards = [
                ['Total Siswa', $totalSiswa, 'primary'],
                ['Hadir', $hadir, 'success'],
                ['Izin', $izin, 'warning'],
                ['Sakit', $sakit, 'info'],
                ['Alpha', $alpha, 'danger'],
                ['Belum Absen', $belumAbsen, 'secondary']
            ];
            $cards = [
                ['Total Admin', $totalAdmin, 'dark'], // ← Tambahan baru
                ['Total Siswa', $totalSiswa, 'primary'],
                ['Hadir', $hadir, 'success'],
                ['Izin', $izin, 'warning'],
                ['Sakit', $sakit, 'info'],
                ['Alpha', $alpha, 'danger'],
                ['Belum Absen', $belumAbsen, 'secondary']
            ];

            foreach ($cards as [$label, $value, $color]) {
                echo "
                <div class='col-md-2'>
                    <div class='card bg-$color'>
                    <div class='card-body text-center text-white'>
                        <h6 class='card-title mb-1'>$label</h6>
                        <h4 class='fw-bold'>$value</h4>
                    </div>
                </div>

                </div>";
            }
            ?>
        </div>

        <div class="card p-4">
            <h5 class="mb-3">📈 Grafik Rekap Bulan <?= date('F') ?></h5>
            <canvas id="grafikBulanan" height="100"></canvas>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>

        // Grafik
        const ctx = document.getElementById('grafikBulanan').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Hadir', 'Izin', 'Sakit', 'Alpha'],
                datasets: [{
                    label: 'Jumlah Hari',
                    data: [<?= $grafik['Hadir'] ?? 0 ?>, <?= $grafik['Izin'] ?? 0 ?>, <?= $grafik['Sakit'] ?? 0 ?>, <?= $grafik['Alpha'] ?? 0 ?>],
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
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    </script>
</body>

</html>